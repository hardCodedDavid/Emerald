<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Category;
use App\FarmList;
use App\Http\Controllers\Globals as Util;
use App\Investment;
use App\Mail\SendMailable;
use App\Notifications\BookingNotification;
use App\Notifications\CustomNotification;
use App\Transaction;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;
use Redirect;

class BookingController extends Controller
{
    /**
     * @var FarmList
     */
    private $farm;

    /**
     * @var PaymentController
     */
    private $payment;

    /**
     * BookingController constructor.
     */
    public function __construct()
    {
        $this->farm = new FarmList();
        $this->payment = new PaymentController();
    }

    public function autoBooking()
    {
        $farms = $this->farm->whereHas('category')->whereDate('start_date', Carbon::today()->toDateString())->get();

        if(count($farms) < 1){
            return false;
        }

        $farms->each(function($farm){
            $farm->bookings()->where('status','approved')->get()
                ->each(function($booking) use ($farm){

                    $totalAmountNeededForInvestment = $farm->price * $booking->units;
                    $totalAmountInEmeraldBank = $booking->user->emeraldbank->total_amount;

                    if($totalAmountInEmeraldBank == 0){
                        return false;
                    }

                    if($totalAmountNeededForInvestment > $totalAmountInEmeraldBank){
                        $remainderFromInvestmentAmount = $totalAmountInEmeraldBank % $farm->price;
                        $totalAmountNeededForInvestment = $totalAmountInEmeraldBank - $remainderFromInvestmentAmount;

                        $unitsThatCanBeInvested = $totalAmountNeededForInvestment / $farm->price;

                        if($unitsThatCanBeInvested < 1){
                            return false;
                        }

                        $booking->update(['units' => $unitsThatCanBeInvested]);
                    }

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

                    $booking->user->notify(new CustomNotification('Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Booking Created'));
                    $title= ' ';
                    $name = $booking->user->name;
                    $content = 'Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.';
                    $button = false;
                    $button_text = '';
                    $subject = "Booking Created";
                    Mail::to($booking->user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

            });
        });

    }

    public function index()
    {
        return view('user.bookings');
    }

    public function store(Request $request)
    {

        $farm = Farmlist::findOrfail($request->farm_id);
        $category = Category::findOrfail($request->category_id);

        //find the remainder from dividing the amount the user entered by the farm price (using modulus operator)
        //minus the remainer from the amount the user entered then divide it by the farm price to fin exact number of units

        if(($request->amount % $farm->price) != 0){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Amount must be multiples of farm price.</div></div>');
        }

        $units = ($request->amount - ($request->amount % $farm->price)) / $farm->price;

        if($request->payment_type == "null"){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You have to provide a payment method.</div></div>');
        }

        if($units < 1){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> Amount inputted is not up to one unit.</div></div>');
        }

        if($units > 250){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Booking units cannot exceed 250</div></div>');
        }

        if($farm->available_units < $units){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Farm does not have requested units. Available units is '. $farm->available_units.'</div></div>');
        }

        switch($request->payment_type){
            case 'bank':
                return $this->processBank($request, $farm, $category, $units);
                break;

            case 'online':
                return $this->processOnlinePayment($request, $farm, $category, $units);
                break;

            case 'wallet':
                return $this->processWalletPayment($request, $farm, $category, $units);
                break;
            default:
                break;

        }

    }

    public function destroy($id)
    {
        $booking = Booking::findOrfail($id);

        $booking->delete();

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Booking deleted successfully.</div></div>');

    }

    public function createFarmBooking($attributes = [])
    {
        return auth()->user()->bookings()->create($attributes);
    }

    private function processBank($request, $farm, $category, $units)
    {
        $booking = $this->createFarmBooking([
            'farmlist_id' => $farm->id,
            'category_id' => $category->id,
            'units' => $units,
            'rollover' => !! $request->rollover,
            'amount' => $request->amount,
            'status' => 'pending'
        ]);

        Transaction::create([
            'amount' => $request->amount,
            'type' => 'booking',
            'user' => auth()->user()->email,
            'status' => 'pending',
            'user_id' => auth()->user()->id,
            'location' => 'bank',
            'booking_id' => $booking->id
        ]);

        auth()->user()->notify(new CustomNotification('Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been received successfully and pending system approval.<br><br>', '<span class="dropdown-item-icon bg-warning text-white"><i class="fas fa-tag"></i></span>', 'Booking Created'));

        $title= ' ';
        $name = auth()->user()->name;
        $content = 'Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been received successfully and pending system approval.<br><br>Thank you for choosing Emerald Farms.';
        $button = false;
        $button_text = '';
        $subject = "Booking Created";
        Mail::to(auth()->user()->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Booking done successfully.</div></div>');
    }

    private function processOnlinePayment($request, $farm, $category, $units)
    {
        if($request->amount > 250000){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Our maximum online payment deposit is limited to NGN 250,000. Please deposit via bank deposit / USSD / Transfer option in the deposit dropdown.</div></div>');
        }else {
            $attributes = [
                'amount'=>$request->amount,
                'type'=>'booking',
                'location' => 'bank',
                'farm_id' => $farm->id,
                'category_id' => $category->id,
                'units' => $units,
                'rollover' => !! $request->rollover
            ];

            return Redirect::away($this->payment->pay(auth()->user()->email, $request->amount, $attributes));
        }
    }

    private function processWalletPayment(Request $request, $farm, $category, $units)
    {
        if(auth()->user()->wallet->total_amount < $request->amount){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Insufficient funds in your wallet.</div></div>');
        }

        $booking = $this->createFarmBooking([
            'farmlist_id' => $farm->id,
            'category_id' => $category->id,
            'units' => $units,
            'rollover' => !! $request->rollover,
            'amount' => $request->amount,
            'status' => 'approved'
        ]);

        auth()->user()->wallet()->decrement('total_amount', $request->amount);
        auth()->user()->emeraldbank()->increment('total_amount', $request->amount);

        Transaction::create([
            'amount' => $request->amount,
            'type' => 'booking',
            'user' => auth()->user()->email,
            'status' => 'approved',
            'user_id' => auth()->user()->id,
            'location' => 'wallet',
            'booking_id' => $booking->id
        ]);

        $farm->decrement('available_units', $units);

        auth()->user()->notify(new CustomNotification('Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Booking Created'));
        $title= ' ';
        $name = auth()->user()->name;
        $content = 'Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.';
        $button = false;
        $button_text = '';
        $subject = "Booking Created";
        Mail::to(auth()->user()->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        auth()->user()->notify(new BookingNotification($booking, false, false, true));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Booking done successfully.</div></div>');
    }

}
