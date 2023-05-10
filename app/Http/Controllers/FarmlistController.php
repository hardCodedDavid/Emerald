<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Category;
use App\FarmList;
use App\Http\Controllers\Globals as Util;
use App\Investment;
use App\Mail\SendMailable;
use App\MilestoneFarm;
use App\MilestoneInvestment;
use App\Notifications\CustomNotification;
use App\Notifications\InvestmentNotification;
use App\Notifications\MilestoneInvestmentNotification;
use App\Transaction;
use App\User;
use App\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Log;
use Barryvdh\DomPDF\Facade as PDF;
use Redirect;

class FarmlistController extends Controller
{
    /**
     * @var PaymentController
     */
    public $payment;

    public function __construct()
    {
        $this->payment = new PaymentController();
    }

    public function invest(Request $request)
    {
        $user = Util::getUser();
        $farmlist = FarmList::findOrFail($request->id);
        $wallet = Wallet::where('user', $user->email)->first();

        if($request->unit < 1){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your unit should not be lower than 1.</div></div>');
        }

        if($request->unit > 250){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You can not purchase more than 250 units, kindly reduce your number of units.</div></div>');
        }

        if($request->unit > $farmlist->available_units){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your unit is higher than the available units.</div></div>');
        }

        $total_amount_returns = $request->unit * $farmlist->price;

        if($wallet->total_amount < $total_amount_returns){

            if($total_amount_returns > 250000){
                return redirect()->to('/wallet')->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> Our maximum online transaction is limited to N250,000. Please <a  href="#" style="text-decoration: underline;" data-toggle="modal" data-target="#exampleModal1">Click Here</a> to make a bank deposit / Transfer / USSD</div></div>');
            }

            $attribute = array('farmlist' => $farmlist->slug, 'amount' => $total_amount_returns, 'units' => $request->unit, 'type' => 'investments', 'rollover' => !! $request->rollover);

            return Redirect::away(Util::pay($user->email, $total_amount_returns, $attribute));

        } else {

            $newdd = $wallet->total_amount - $total_amount_returns;
            $leftC = $farmlist->available_units - $request->unit;

            $wallet->update(['total_amount' => $newdd]);

            Investment::create([
                'amount_invested'=>$total_amount_returns,
                'farmlist'=>$farmlist->slug,
                'user'=>$user->email,
                'maturity_status'=>'pending',
                'units'=>$request->unit,
                'returns'=>$total_amount_returns,
                'status'=>'pending',
                'user_id' => $user->id,
                'farm_id' => $farmlist->id,
                'rollover' => !! $request->rollover
            ]);

            Transaction::create([
                'amount' => $total_amount_returns,
                'type' => 'investments',
                'user' => $user->email,
                'status' => 'approved',
                'user_id' => $user->id
            ]);

            $farmlist->update(['available_units' => $leftC]);

            $user->notify(new CustomNotification('Your investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$request->unit.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Created'));
            $title= ' ';
            $name = $user->name;
//            $content = 'Your investment has been created successfully. <br> You invested N'. number_format($total_amount_returns,2).' under the farmlist titled <big>'.$farmlist->title.'</big>.';
            $content = 'Your investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$request->unit.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong><br><br>Thank you for choosing Emerald Farms.';
            $button = false;
            $button_text = '';
            $subject = "Investment Created";
            Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

            return redirect("/farmlist")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your investment has been approved.</div></div>');
        }
    }

    public static function checkForFarmCloseDateAndStartInvestmentCountdown(){
        FarmList::whereStatus('opened')->get()
            ->each(function($farm){
                if($farm->canStartInvestment()){
                    Investment::whereFarmlist($farm->slug)->whereNull('maturity_date')
                        ->whereStatus('pending')->get()
                        ->each(function($investment) use ($farm){
                            $user = User::whereEmail($investment->user)->first();

                            $investment->update([
                                'maturity_date' => now()->addDays($farm->maturity_date),
                                'maturity_status' => 'pending',
                                'status' => 'active',
                            ]);

                            $units = $investment->units;

                            if ($units >= 1 && $units < 51){
                                $sponsor = 'silver';
                            }elseif($units >= 51 && $units < 201){
                                $sponsor = 'gold';
                            }elseif ($units >= 201){
                                $sponsor = 'platinum';
                            }

                            $data = [
                                'name' => $user->name,
                                'units' => $units,
                                'farm' => $farm->title,
                                'date' => date("jS \of F Y", strtotime(now())),
                                'sponsor' => $sponsor
                            ];

                            $pdf = PDF::loadView('pdf.certificate', $data);

                            $user->notify(new CustomNotification('We are happy to inform you that your investment of <strong>₦'. number_format($investment->amount_invested,2).'</strong> in <strong>'.$farm->title.'</strong> farm has begun.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Initiated'));

                            $title = ' ';
                            $name = $user->name;
                            $content = 'We are happy to inform you that your investment of <strong>₦'. number_format($investment->amount_invested,2).'</strong> in <strong>'.$farm->title.'</strong> farm has begun. <br><br>Click the button below to view your investment.<br><br>';
                            $button = true;
                            $button_text = 'View Investment';
                            $button_link = url('/transactions/investments/short/'.$investment->id.'/show');
                            $subject = "Investment Initiated";
                            Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject,$button_link));

                            $title = ' ';
                            $name = $user->name;
                            $content = 'Thank you for your investment in our <strong>'.$farm->title.'</strong> Farm Project. <br><br>Together we can rewrite history and make memories.<br><br> Updates will be posted on the website weekly. You can request to visit the farm; you only have to schedule a visitation date ahead. Please find attached your certificate of investment.<br><br> Once again, Thank you';
                            $button = false;
                            $button_text = '';
                            $button_link = '';
                            $subject = "Investment Certificate";
                            Mail::to($user->email)->send((new SendMailable($title,$name,$content,$button,$button_text,$subject,$button_link))->attachData($pdf->output(), "certificate.pdf"));
                        });

                    $farm->update(['status'=>'closed']);

                }
            });
    }

    public static function checkForFarmStartDateAndMarkFarmAsOpen()
    {
        FarmList::whereStatus('pending')->get()
            ->each(function($farm){
                if($farm->canOpenFarm()){

                    $farm->update(['status'=>'opened']);

                    $category = Category::find($farm->category_id);

                    if($category){
                        $category->bookings()->whereNotNull('farmlist_id')->where('status', 'approved')->get()
                            ->each(function($booking) use ($farm){

                                $unitsThatCanBeInvested = (int) ($booking->amount / $farm->price);

                                $totalAmountNeededForInvestment = $farm->price * $unitsThatCanBeInvested;

                                $totalAmountInEmeraldBank = $booking->user->emeraldbank->total_amount;

//                                $totalAmountNeededForInvestment = $farm->price * $booking->units;
//                                $totalAmountInEmeraldBank = $booking->user->emeraldbank->total_amount;

                                if($totalAmountInEmeraldBank <= 0){
                                    return false;
                                }

                                if($totalAmountNeededForInvestment > $totalAmountInEmeraldBank){
//                                    $remainderFromInvestmentAmount = $totalAmountInEmeraldBank % $farm->price;
//                                    $totalAmountNeededForInvestment = $totalAmountInEmeraldBank - $remainderFromInvestmentAmount;

//                                    $unitsThatCanBeInvested = $totalAmountNeededForInvestment / $farm->price;

                                    if($unitsThatCanBeInvested < 1){
                                        return false;
                                    }
                                }

                                $booking->update(['amount' => $totalAmountNeededForInvestment,'units' => $unitsThatCanBeInvested]);

                                Investment::create([
                                    'amount_invested' => $totalAmountNeededForInvestment,
                                    'farmlist' => $farm->slug,
                                    'user' => $booking->user->email,
                                    'maturity_status' => 'pending',
                                    'units' => $booking->fresh()->units,
                                    'returns' => $totalAmountNeededForInvestment,
                                    'status' => 'pending',
                                    'user_id' => $booking->user->id,
                                    'farm_id' => $farm->id,
                                    'rollover' => $booking->rollover
                                ]);

//                                Transaction::create([
//                                    'amount'=>$totalAmountNeededForInvestment,
//                                    'type'=>'investments',
//                                    'user'=>$booking->user->email,
//                                    'status'=>'approved',
//                                    'user_id' => $booking->user->id
//                                ]);

                                if ($farm->available_units <= 0){
                                    return false;
                                }

                                $booking->update(['status' => 'sponsored']);
                                $booking->user->emeraldbank()->decrement('total_amount',$totalAmountNeededForInvestment);

                                $title= ' ';
//                                $booking->user->notify(new CustomNotification('Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Booking Created'));
                                $booking->user->notify(new CustomNotification('Your investment of <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong><br>Units Purchased: <strong>'.$booking->fresh()->units.'</strong><br>Farm: <strong>'.$farm->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong><br>New wallet balance: <strong>₦'. number_format($booking->user->wallet->fresh()->total_amount, 2) .'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Created'));

                                $name = $booking->user->name;
                                $content = 'Your investment of <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong><br>Units Purchased: <strong>'.$booking->fresh()->units.'</strong><br>Farm: <strong>'.$farm->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong><br>New wallet balance: <strong>₦'. number_format($booking->user->wallet->fresh()->total_amount, 2) .'</strong><br><br>Thank you for choosing Emerald Farms.';
//                                $content = 'Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.';
                                $button = false;
                                $button_text = '';
                                $subject = "Investment Created";
                                try {
                                    Mail::to($booking->user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
                                }catch(\Exception $exception){
                                    return false;
                                }

                                $booking->update(['farmlist_id' => $farm->id, 'status' => 'sponsored']);
                            });
                    }

//                    $farm->bookings()->where('status','approved')->get()
//                        ->each(function($booking) use ($farm){
//
//                            $totalAmountNeededForInvestment = $farm->price * $booking->units;
//                            $totalAmountInEmeraldBank = $booking->user->emeraldbank->total_amount;
//
//                            if($totalAmountInEmeraldBank == 0){
//                                return false;
//                            }
//
//                            if($totalAmountNeededForInvestment > $totalAmountInEmeraldBank){
//                                $remainderFromInvestmentAmount = $totalAmountInEmeraldBank % $farm->price;
//                                $totalAmountNeededForInvestment = $totalAmountInEmeraldBank - $remainderFromInvestmentAmount;
//
//                                $unitsThatCanBeInvested = $totalAmountNeededForInvestment / $farm->price;
//
//                                if($unitsThatCanBeInvested < 1){
//                                    return false;
//                                }
//
//                                $booking->update(['units' => $unitsThatCanBeInvested]);
//                            }
//
//                            Investment::create([
//                                'amount_invested' => $totalAmountNeededForInvestment,
//                                'farmlist' => $farm->slug,
//                                'user' => $booking->user->email,
//                                'maturity_status' => 'pending',
//                                'units' => $booking->fresh()->units,
//                                'returns' => $totalAmountNeededForInvestment,
//                                'status' => 'pending',
//                                'user_id' => $booking->user->id,
//                                'farm_id' => $farm->id,
//                                'rollover' => $booking->rollover
//                            ]);
//
////                            Transaction::create([
////                                'amount'=>$totalAmountNeededForInvestment,
////                                'type'=>'investments',
////                                'user'=>$booking->user->email,
////                                'status'=>'approved',
////                                'user_id' => $booking->user->id
////                            ]);
//
//                            $booking->update(['status' => 'sponsored']);
//                            $booking->user->emeraldbank()->decrement('total_amount',$totalAmountNeededForInvestment);
//
//                            $title= ' ';
//
//                            $booking->user->notify(new CustomNotification('Your investment of <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong><br>Units Purchased: <strong>'.$booking->fresh()->units.'</strong><br>Farm: <strong>'.$farm->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Created'));
//
//                            $name = $booking->user->name;
////                            $content = 'Your investment has been created successfully. <br> You invested N'. number_format($totalAmountNeededForInvestment,2).' under the farmlist titled:'.$farm->title;
//                            $content = 'Your investment of <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong><br>Units Purchased: <strong>'.$booking->fresh()->units.'</strong><br>Farm: <strong>'.$farm->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($totalAmountNeededForInvestment,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong><br><br>Thank you for choosing Emerald Farms.';
//                            $button = false;
//                            $button_text = '';
//                            $subject = "Investment Created";
//                            Mail::to($booking->user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
//                        });

//                    $category = Category::find($farm->category_id);
//
//                    if($category){
//                        $category->bookings()->whereNull('farmlist_id')->where('status', 'approved')->get()
//                            ->each(function($booking) use ($farm){
//
//                                $totalAmountNeededForInvestment = $farm->price * $booking->units;
//                                $totalAmountInEmeraldBank = $booking->user->emeraldbank->total_amount;
//
//                                if($totalAmountInEmeraldBank == 0){
//                                    return false;
//                                }
//
//                                if($totalAmountNeededForInvestment > $totalAmountInEmeraldBank){
//                                    $remainderFromInvestmentAmount = $totalAmountInEmeraldBank % $farm->price;
//                                    $totalAmountNeededForInvestment = $totalAmountInEmeraldBank - $remainderFromInvestmentAmount;
//
//                                    $unitsThatCanBeInvested = $totalAmountNeededForInvestment / $farm->price;
//
//                                    if($unitsThatCanBeInvested < 1){
//                                        return false;
//                                    }
//
//                                    $booking->update(['units' => $unitsThatCanBeInvested]);
//                                }
//
//                                Investment::create([
//                                    'amount_invested' => $totalAmountNeededForInvestment,
//                                    'farmlist' => $farm->slug,
//                                    'user' => $booking->user->email,
//                                    'maturity_status' => 'pending',
//                                    'units' => $booking->fresh()->units,
//                                    'returns' => $totalAmountNeededForInvestment,
//                                    'status' => 'pending',
//                                    'user_id' => $booking->user->id,
//                                    'farm_id' => $farm->id,
//                                    'rollover' => $booking->rollover
//                                ]);
//
//                                Transaction::create([
//                                    'amount'=>$totalAmountNeededForInvestment,
//                                    'type'=>'investments',
//                                    'user'=>$booking->user->email,
//                                    'status'=>'approved',
//                                    'user_id' => $booking->user->id
//                                ]);
//
//                                $booking->update(['status' => 'sponsored']);
//                                $booking->user->emeraldbank()->decrement('total_amount',$totalAmountNeededForInvestment);
//
//                                $title= ' ';
//
//                                $booking->user->notify(new CustomNotification('Your investment of N'. number_format($totalAmountNeededForInvestment,2).' has been created successfully. <br><br>Investment Overview: <br>Amount Invested: N'. number_format($totalAmountNeededForInvestment,2).'<br>Units Purchased: '.$booking->fresh()->unit.'<br>Farm: '.$farm->title.'.<br><br>Wallet summary:<br>Payout amount: N'. number_format($totalAmountNeededForInvestment,2).'<br>New wallet balance: N'. auth()->user()->wallet->fresh()->total_amount .'.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Created'));
//
//                                $name = $booking->user->name;
////                                $content = 'Your investment has been created successfully. <br> You invested N'. number_format($totalAmountNeededForInvestment,2).' under the farmlist titled:'.$farm->title;
//                                $content = 'Your investment of N'. number_format($totalAmountNeededForInvestment,2).' has been created successfully. <br><br>Investment Overview: <br>Amount Invested: N'. number_format($totalAmountNeededForInvestment,2).'<br>Units Purchased: '.$booking->fresh()->unit.'<br>Farm: '.$farm->title.'.<br><br>Wallet summary:<br>Payout amount: N'. number_format($totalAmountNeededForInvestment,2).'<br>New wallet balance: N'. auth()->user()->wallet->fresh()->total_amount .'<br><br>Thank you for choosing Emerald Farms.';
//                                $button = false;
//                                $button_text = '';
//                                $subject = "Investment Created";
//                                Mail::to($booking->user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
//
//
//                                $booking->update(['farmlist_id' => $farm->id, 'status' => 'sponsored']);
//                            });
//                    }

                }
            });
        FarmList::whereStatus('pending')->get()
            ->each(function($farm){
                if (strtotime($farm->created_at.' + 1 hour') < strtotime(now())){
                    $category = Category::find($farm->category_id);

                    if($category){
                        $category->bookings()->whereNull('farmlist_id')->where('status', 'approved')->get()
                            ->each(function($booking) use ($farm){

                                $unitsThatCanBeInvested = (int) ($booking->amount / $farm->price);

                                $totalAmountNeededForInvestment = $farm->price * $unitsThatCanBeInvested;

                                $totalAmountInEmeraldBank = $booking->user->emeraldbank->total_amount;

//                                $totalAmountNeededForInvestment = $farm->price * $booking->units;
//                                $totalAmountInEmeraldBank = $booking->user->emeraldbank->total_amount;

                                if($totalAmountInEmeraldBank <= 0){
                                    return false;
                                }


                                if($totalAmountNeededForInvestment > $totalAmountInEmeraldBank){
//                                    $remainderFromInvestmentAmount = $totalAmountInEmeraldBank % $farm->price;
//                                    $totalAmountNeededForInvestment = $totalAmountInEmeraldBank - $remainderFromInvestmentAmount;

//                                    $unitsThatCanBeInvested = $totalAmountNeededForInvestment / $farm->price;

                                    if($unitsThatCanBeInvested < 1){
                                        return false;
                                    }
                                }

                                if ($farm->available_units <= 0){
                                    return false;
                                }

                                $booking->update(['amount' => $totalAmountNeededForInvestment, 'units' => $unitsThatCanBeInvested]);

                                $farm->decrement('available_units', $booking->fresh()->units);

                                Investment::create([
                                    'amount_invested' => $totalAmountNeededForInvestment,
                                    'farmlist' => $farm->slug,
                                    'user' => $booking->user->email,
                                    'maturity_status' => 'pending',
                                    'units' => $booking->fresh()->units,
                                    'returns' => $totalAmountNeededForInvestment,
                                    'status' => 'pending',
                                    'user_id' => $booking->user->id,
                                    'farm_id' => $farm->id,
                                    'rollover' => $booking->rollover
                                ]);

                                Transaction::create([
                                    'amount'=>$totalAmountNeededForInvestment,
                                    'type'=>'investments',
                                    'user'=>$booking->user->email,
                                    'status'=>'approved',
                                    'user_id' => $booking->user->id
                                ]);

                                $booking->update(['status' => 'sponsored']);
                                $booking->user->emeraldbank()->decrement('total_amount',$totalAmountNeededForInvestment);

                                $title= ' ';
                                $booking->user->notify(new CustomNotification('Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Booking Created'));

                                $name = $booking->user->name;
//                                $content = 'Your investment has been created successfully. <br> You invested N'. number_format($totalAmountNeededForInvestment,2).' under the farmlist titled:'.$farm->title;
                                $content = 'Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.';
                                $button = false;
                                $button_text = '';
                                $subject = "Booking Created";
                                try {
                                    Mail::to($booking->user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
                                }catch(\Exception $exception){
                                    return false;
                                }

                                $booking->update(['farmlist_id' => $farm->id, 'status' => 'sponsored']);
                            });
                    }
                }
            });

    }

    public function checkForMaturityAndMarkClosed()
    {
        Investment::oldest()->get()->each(function ($investment){
            if($investment->maturity_status != 'matured' && $investment->status != 'closed' && $investment->maturity_date != null){
                $mat = strtotime($investment->maturity_date);
                $cur = strtotime(now());
                $diff = $mat-$cur;
                if($diff < 0){
                    $investment->update(['maturity_status'=>'matured', 'status'=>'closed']);
                    $user = User::find($investment->user_id);
                    if($user){
                        $user->notify(new InvestmentNotification(explode(' ', $user->name)[0], $investment, false, false, true, false, false));
                    }
                }
            }
        });

//        MilestoneInvestment::oldest()->get()->each(function ($investment){
//            if($investment->maturity_status != 'matured' && $investment->status != 'closed' && $investment->maturity_date != null){
//                $mat = strtotime($investment->maturity_date);
//                $cur = strtotime(now());
//                $diff = $mat-$cur;
//                if($diff < 0){
//                    $investment->update(['maturity_status'=>'matured', 'status'=>'closed']);
//                    $user = User::find($investment->user_id);
//                    if($user){
//                        $user->notify(new MilestoneInvestmentNotification(explode(' ', $user->name)[0], $investment, false, false, true, false, false));
//                    }
//                }
//            }
//        });
    }

    public function longTermFarm()
    {
        return view('user.farm.longterm',['farmlists' => MilestoneInvestment::all()]);
    }

    public function loadLongFarms(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            2=> 'cover',
            3=> 'price',
            4=> 'status',
            5=> 'interest',
            6=> 'milestone',
            7=> 'available_units',
            8=> 'farm_opening_date',
            9=> 'investment_start_date',
        );

        $totalData = MilestoneFarm::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = MilestoneFarm::latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  MilestoneFarm::latest()->where('title','LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = MilestoneFarm::where('title','LIKE',"%{$search}%")
                ->count();
        }


        $data = array();
        if(!empty($posts)) {
            $i = 1;
            foreach ($posts as $post) {
                $status = '';
                $statusLinks = '<a href="/farmlist/long/'. $post->slug .'/show" class="dropdown-item">View Farm</a>';

                if($post->isOpen()){
                    $status .= '<div class="badge badge-success">Open</div>';
                    $statusLinks .= '<a href="/farmlist/long/invest/'. $post->slug .'" class="dropdown-item">Invest</a>';
                }elseif($post->isClosed()){
                    $status .= '<div class="badge badge-danger">Closed</div>';
                }elseif($post->available_units < 1){
                    $status .= '<div class="badge badge-danger">Sold Out</div>';
                }else{
                    $status .= '<div class="badge badge-warning">Coming Soon</div>';
                }

                $nestedData['sn'] = $i++;
                $nestedData['title'] = ucwords($post->title);
                $nestedData['cover'] = '<a href="' . asset($post->cover). '" data-featherlight="image" data-title="' .$post->title. '">
                                            <img src="' . asset('assets/uploads/courses/farmlist.png') . '" alt="' . $post->title .'" width="70">
                                        </a>';
                $nestedData['start_date'] = '₦' . number_format($post->price, 2);
                $nestedData['close_date'] = '₦' . number_format($post->price, 2);
                $nestedData['price'] = '₦' . number_format($post->price, 2);
                $nestedData['interest'] = $post->interest . '%';
                $nestedData['milestone'] = $post->milestone;
                $nestedData['duration'] = $post->duration;
                $nestedData['units'] = $post->available_units . ' Units';
                $nestedData['farm_opening_date'] = $post->start_date->format('M d, Y \a\t h:i A');
                $nestedData['investment_start_date'] = $post->close_date->format('M d, Y \a\t h:i A');
                $nestedData['status'] = $status;
                $nestedData['actions'] =  '<div class="dropdown show">
                                            <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                '.$statusLinks.'
                                            </div>
                                        </div>';


                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function showLong($slug)
    {
        $farmlist = MilestoneFarm::where('slug', $slug)->first();

        return view('user.farm.showLongFarm', ['farmlist'=>$farmlist]);
    }

    public function investLong($slug){
        $farmlist = MilestoneFarm::where('slug', $slug)->first();
        if(! $farmlist->isOpen()){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>This farmlist is not open for investment.</div></div>');
        }else{
            $user = Util::getUser();
            if(Util::completeProfile($user))
                return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
            else if(Util::completeProfileKin($user))
                return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
            else
                return view('user.investment.longInvest', ['farmlist'=>$farmlist]);
        }
    }

    public function investLongPost(Request $req)
    {
        $user = Util::getUser();
        $farmlist = MilestoneFarm::findOrFail($req->id);
        $wallet = Wallet::where('user', $user->email)->first();

        if($req->unit < 1){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your unit should not be lower than 1.</div></div>');
        }

        if($req->unit > 250){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You can not purchase more than 250 units, kindly reduce your number of units.</div></div>');
        }

        if($req->unit > $farmlist->available_units){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your unit is higher than the available units.</div></div>');
        }

        $total_amount_returns = $req->unit * $farmlist->price;

        if($wallet->total_amount < $total_amount_returns){
            return redirect()->to('/wallet')->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> Our maximum online transaction is limited to N250,000. Please <a  href="#" style="text-decoration: underline;" data-toggle="modal" data-target="#exampleModal1">Click Here</a> to make a bank deposit / Transfer / USSD</div></div>');
        } else {

            $newdd = $wallet->total_amount - $total_amount_returns;
            $leftC = $farmlist->available_units - $req->unit;

            $wallet->update(['total_amount'=>$newdd]);

            $investment = MilestoneInvestment::create([
                'amount_invested'=>$total_amount_returns,
                'maturity_status'=>'pending',
                'units'=>$req->unit,
                'returns'=>$total_amount_returns,
                'status'=>'active',
                'user_id' => $user->id,
                'farm_id' => $farmlist->id,
                'approved_date' => now()
            ]);

            Transaction::create([
                'amount' => $total_amount_returns,
                'type' => 'long-investments',
                'user' => $user->email,
                'status' => 'approved',
                'user_id' => $user->id
            ]);

            $farmlist->update(['available_units' => $leftC]);

            $units = $investment->units;

            if ($units >= 1 && $units < 51){
                $sponsor = 'silver';
            }elseif($units >= 51 && $units < 201){
                $sponsor = 'gold';
            }elseif ($units >= 201){
                $sponsor = 'platinum';
            }

            $data = [
                'name' => $user->name,
                'units' => $units,
                'farm' => $farmlist->title,
                'date' => date("jS \of F Y", strtotime(now())),
                'sponsor' => $sponsor
            ];

            $pdf = PDF::loadView('pdf.certificate', $data);

            $user->notify(new CustomNotification('Your Long-term investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$units.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong><br>Duration: <strong>'.$investment->getPaymentDurationInDays().'days</strong><br>Investment Milestones: <strong>'.$investment->farm->milestone.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Long-term Investment Created'));

            $title= ' ';
            $name = $user->name;
            $content = 'Your Long-term investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$units.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong><br>Duration: <strong>'.$investment->getPaymentDurationInDays().'days</strong><br>Investment Milestones: <strong>'.$investment->farm->milestone.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong><br><br>Thank you for choosing Emerald Farms.';
            $button = false;
            $button_text = '';
            $subject = "Long-term Investment Created";
            Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

            $title = ' ';
            $name = $user->name;
            $content = 'Thank you for your investment in our <strong>'.$farmlist->title.'</strong> Farm Project. <br><br>Together we can rewrite history and make memories.<br><br> Updates will be posted on the website weekly. You can request to visit the farm; you only have to schedule a visitation date ahead. Please find attached your certificate of investment.<br><br> Once again, Thank you';
            $button = false;
            $button_text = '';
            $button_link = '';
            $subject = "Investment Certificate";
            \Illuminate\Support\Facades\Mail::to($user->email)->send((new SendMailable($title,$name,$content,$button,$button_text,$subject,$button_link))->attachData($pdf->output(), "certificate.pdf"));

//            $title= '';
//            $name = Admin::first()->name;
//            $content = 'An investment has been created successfully by '.$user->name.'. An investment of N '. number_format($total_amount_returns,2).' under the farmlist titled <big>'.$farmlist->title.'</big>.';
//            $button = true;
//            $button_text = 'View Investment';
//            $button_link = route('admin.investments.long');
//            $subject = "New Investment Created";
//            Mail::to('transactions@emeraldfarms.ng')->send(new SendMailable($title,$name,$content,$button,$button_text,$subject,$button_link));

            return redirect("/farmlist/long")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your investment has been approved.</div></div>');
        }
    }
}
