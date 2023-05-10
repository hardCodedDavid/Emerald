<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\FarmList;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Globals;
use App\Investment;
use App\Mail\SendMailable;
use App\MilestoneFarm;
use App\MilestoneInvestment;
use App\Notifications\CustomNotification;
use App\Transaction;
use Illuminate\Http\Request;
use Mail;

class PayoutController extends Controller
{
    public function index()
    {
        return view('admin.payout.index', ['categories' => Category::all()]);
    }

    public function showFarmsByCategory(Category $category)
    {
        return view('admin.payout.showFarm', ['farmlists' => FarmList::where('category_id', $category->id)->get()]);
    }

    public function showInvestmentsByCategoryAndFarm(Category $category, FarmList $farmlist)
    {
        //the category might be useful later but for now, it really ain't

        return view('admin.payout.show', ['farmlist' => $farmlist]);
    }

    public function payoutInvestment($id)
    {
        $investment = Investment::findOrFail($id);
        $user = Globals::getUserByEmail($investment->user);

        if($investment->isPaid()){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Payout has already been approved.</div></div>');
        }

        if(! $investment->isMature()){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Investment still running.</div></div>');
        }

        Transaction::create([
            'amount'=>$investment->amount_invested * (($investment->farm->interest  + 100)/100),
            'type'=>'payouts',
            'user'=>$user->email,
            'status'=>'approved',
            'user_id' => $user->id
        ]);

//        if($investment->rollover == 1){
//            $booking = $user->bookings()->create([
//                'category_id' => $investment->farm->category_id,
//                'units' => $investment->units,
//                'amount' => $investment->amount_invested * (($investment->farm->interest  + 100)/100),
//                'rollover' => $investment->rollover,
//                'status' => 'approved'
//            ]);
//
//            $user->emeraldbank()->increment('total_amount', $investment->amount_invested * (($investment->farm->interest  + 100)/100));
//
//            $user->notify(new CustomNotification("Your investment of <strong>₦".$investment->amount_invested."</strong> in <strong>".$investment->farm->title."</strong> has been paid into your emerald bank account.<br><br>Payout Overview:<br>Amount Invested: <strong>₦". number_format($investment->amount_invested) ."</strong><br>Amount Paid: <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> <br><br>Should you have any questions or complaints, please kindly contact our support team.", '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Payout'));
//
//            $user->notify(new CustomNotification('Because you have rollover activated on your recently completed investment, our system has automatically stored your payout of <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> in your Emerald Bank.<br><br>We will automatically sponsor a new farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Rollover Created'));
//
//            $title= ' ';
//            $name = $user->name;
//            $content = "Your investment of <strong>₦".$investment->amount_invested."</strong> in <strong>".$investment->farm->title."</strong> has been paid into your emerald bank account.<br><br>Payout Overview:<br>Amount Invested: <strong>₦". number_format($investment->amount_invested) ."</strong><br>Amount Paid: <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> <br><br>Should you have any questions or complaints, please kindly contact our support team.<br><br> Thank you for choosing Emerald Farms.<br><br>";
//            $button = false;
//            $button_text = '';
//            $subject = "Investment Payout";
//            Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
//
//            $title= ' ';
//            $name = $user->name;
//            $content = "Because you have rollover activated on your recently completed investment, our system has automatically stored your payout of <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> in your Emerald Bank.<br><br>We will automatically sponsor a new farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.";
//            $button = false;
//            $button_text = '';
//            $subject = "Rollover Created";
//            Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
//        }else{

//            $user->emeraldbank()->increment('total_amount', $investment->amount_invested * (($investment->farm->interest  + 100)/100));

            $user->notify(new CustomNotification("Your investment of <strong>₦".$investment->amount_invested."</strong> in <strong>".$investment->farm->title."</strong> has been paid directly into your bank account.<br><br>Payout Overview:<br>Amount Invested: <strong>₦". number_format($investment->amount_invested) ."</strong><br>Amount Paid: <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> <br><br>Should you have any questions or complaints, please kindly contact our support team.", '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Payout'));
            $title= ' ';
            $name = $user->name;
            $content = "Your investment of <strong>₦".$investment->amount_invested."</strong> in <strong>".$investment->farm->title."</strong> has been paid directly into your bank account.<br><br>Payout Overview:<br>Amount Invested: <strong>₦". number_format($investment->amount_invested) ."</strong><br>Amount Paid: <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> <br><br>Should you have any questions or complaints, please kindly contact our support team.<br><br> Thank you for choosing Emerald Farms.<br><br>";
            $button = false;
            $button_text = '';
            $subject = "Investment Payout";
            // Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
//        }

        $investment->update(['paid' => 1]);


        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Payout Approved.</div></div>');
    }

    public function payoutAllInvestments($id)
    {
        $farmlist = FarmList::findOrFail($id);
        $error = false;

        $farmlist->investments
            ->each(function($investment) use (&$error, $farmlist){
                $user = Globals::getUserByEmail($investment->user);

                if(! $investment->isMature()){
                    $error = true;
                }

                if (!$investment->isPaid() && $investment->isMature()){
                    Transaction::create([
                        'amount' => $investment->amount_invested * (($investment->farm->interest  + 100)/100),
                        'type' => 'payouts',
                        'user' => $user->email,
                        'status' => 'approved',
                        'user_id' => $user->id
                    ]);

//                    if($investment->rollover == 1){
//                        $booking = $user->bookings()->create([
//                            'category_id' => $investment->farm->category_id,
//                            'units' => $investment->units,
//                            'amount' => $investment->amount_invested * (($investment->farm->interest  + 100)/100),
//                            'rollover' => $investment->rollover,
//                            'status' => 'approved'
//                        ]);
//
//                        $user->notify(new CustomNotification("Your investment of <strong>₦".$investment->amount_invested."</strong> in <strong>".$investment->farm->title."</strong> has been paid into your emerald bank account.<br><br>Payout Overview:<br>Amount Invested: <strong>₦". number_format($investment->amount_invested) ."</strong><br>Amount Paid: <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> <br><br>Should you have any questions or complaints, please kindly contact our support team.", '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Payout'));
//
//                        $user->notify(new CustomNotification('Because you have rollover activated on your recently completed investment, our system has automatically stored your payout of <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> in your Emerald Bank.<br><br>We will automatically sponsor a new farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Rollover Created'));
//
//                        $user->emeraldbank()->increment('total_amount', $investment->amount_invested * (($investment->farm->interest  + 100)/100));
//
//                        $title= ' ';
//                        $name = $user->name;
//                        $content = "Your investment of <strong>₦".$investment->amount_invested."</strong> in <strong>".$investment->farm->title."</strong> has been paid into your emerald bank account.<br><br>Payout Overview:<br>Amount Invested: <strong>₦". number_format($investment->amount_invested) ."</strong><br>Amount Paid: <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> <br><br>Should you have any questions or complaints, please kindly contact our support team.<br><br> Thank you for choosing Emerald Farms.<br><br>";
//                        $button = false;
//                        $button_text = '';
//                        $subject = "Investment Payout";
//                        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
//
//                        $title= ' ';
//                        $name = $user->name;
//                        $content = "Because you have rollover activated on your recently completed investment, our system has automatically stored your payout of <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> in your Emerald Bank.<br><br>We will automatically sponsor a new farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.";
//                        $button = false;
//                        $button_text = '';
//                        $subject = "Rollover Created";
//                        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
//
//                    }else{

//                    $user->emeraldbank()->increment('total_amount', $investment->amount_invested * (($investment->farm->interest  + 100)/100));

                        $user->notify(new CustomNotification("Your investment of <strong>₦".$investment->amount_invested."</strong> in <strong>".$investment->farm->title."</strong> has been paid directly into your bank account.<br><br>Payout Overview:<br>Amount Invested: <strong>₦". number_format($investment->amount_invested) ."</strong><br>Amount Paid: <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> <br><br>Should you have any questions or complaints, please kindly contact our support team.", '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Payout'));

                        $title= ' ';
                        $name = $user->name;
                        $content = "Your investment of <strong>₦".$investment->amount_invested."</strong> in <strong>".$investment->farm->title."</strong> has been paid directly into your bank account.<br><br>Payout Overview:<br>Amount Invested: <strong>₦". number_format($investment->amount_invested) ."</strong><br>Amount Paid: <strong>₦". number_format($investment->amount_invested * ($investment->farm->interest + 100)/100 ) ."</strong> <br><br>Should you have any questions or complaints, please kindly contact our support team.<br><br> Thank you for choosing Emerald Farms.<br><br>";
                        $button = false;
                        $button_text = '';
                        $subject = "Investment Payout";
                        // Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

//                    }

                    $investment->update(['paid' => 1]);
                }

            });

        if($error){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Some Payouts were not approved.</div></div>');
        }

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Payout All  Approved.</div></div>');
    }

    public function longPayout()
    {
        return view('admin.long-payout.index', ['farms' => MilestoneFarm::all()]);
    }

    public function longPayoutShow($id)
    {
        return view('admin.long-payout.showFarm', ['investments' => MilestoneInvestment::whereFarmId($id)->get()]);
    }

}
