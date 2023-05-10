<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Category;
use App\Notifications\CustomNotification;
use App\Traits\Referral;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Investment;
use App\Transaction;
use App\Wallet;
use App\Http\Controllers\Globals as Util;
use App\Package;
use App\FarmList;
use App\User;
use App\Bank;
use App\Mail\SendMailable;
use Illuminate\Support\Str;
use Mail;
//use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Redirect;
use App\Verified;
use App\NewsUpdate;
use App\MilestoneInvestment;
use Spatie\Browsershot\Browsershot;

class HomeController extends Controller
{
    use Referral;

    /**
     * @var PaymentController
     */
    private $payment;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->payment = new PaymentController();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $user = Util::getUser();
        $investments = count(Investment::where('user', $user->email)->get());
        $activeInvestmentsCount = Investment::where('user', $user->email)->where('status', 'active')->count();
        $payouts = Transaction::where(['user'=>$user->email, 'type'=>'payouts'])->sum('amount');
        $deposits = Transaction::where(['user'=>$user->email, 'type'=>'deposits', 'status' => 'approved'])->sum('amount');
        $wallet = Wallet::where('user', $user->email)->first();
        if(Util::completeProfile($user)){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        }elseif(Util::completeProfileKin($user)){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        }else{
            return view('user.home', ['investments'=>$investments, 'activeInvestmentsCount' => $activeInvestmentsCount, 'payouts'=>$payouts, 'deposits'=>$deposits, 'wallet'=>$wallet]);
        }
    }

    public function packages(){
        $user = Util::getUser();
        $packages = Package::orderBy('id', 'desc')->get();
        if(Util::completeProfile($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        else if(Util::completeProfileKin($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        else
            return view('user.packages', ['packages'=>$packages]);
    }

    public function farmlist(){
        $farmlists = FarmList::latest()->get();

        $pending = FarmList::where(function($query){
            $query->whereDate('start_date', '>', now());
        })->orderBy('id', 'desc')->get();

        $opened = FarmList::where(function($query){
            $query->whereDate('start_date','<=', now());
            $query->whereDate('close_date', '>', now());
        })->orderBy('id', 'desc')->get();

        $closed = FarmList::where(function($query){
            $query->whereDate('close_date', '<=', now());
        })->orderBy('id', 'desc')->get();

        $type = 'all';

        $user = Util::getUser();
        if(Util::completeProfile($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        else if(Util::completeProfileKin($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        else
            return view('user.farm.shortterm', ['farmlists'=>$farmlists, 'opened'=>$opened, 'closed'=>$closed, 'pending'=>$pending, 'all'=>true, 'type' => $type]);
    }

    public function pending(){
        $farmlists = FarmList::orderBy('id', 'desc')->get();

        $pending = FarmList::where(function($query){
            $query->whereDate('start_date', '>', now());
        })->orderBy('id', 'desc')->get();

        $opened = FarmList::where(function($query){
            $query->whereDate('start_date','<=', now());
            $query->whereDate('close_date', '>', now());
        })->orderBy('id', 'desc')->get();

        $closed = FarmList::where(function($query){
            $query->whereDate('close_date', '<=', now());
        })->orderBy('id', 'desc')->get();

        $type = 'pending';

        $user = Util::getUser();
        if(Util::completeProfile($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        else if(Util::completeProfileKin($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        else
            return view('user.farm.shortterm', ['farmlists'=>$farmlists, 'opened'=>$opened, 'closed'=>$closed, 'pending'=>$pending, 'pend'=>true, 'type' => $type]);
    }

    public function closed(){
        $farmlists = FarmList::orderBy('id', 'desc')->get();

        $pending = FarmList::where(function($query){
            $query->whereDate('start_date', '>', now());
        })->orderBy('id', 'desc')->get();

        $opened = FarmList::where(function($query){
            $query->whereDate('start_date','<=', now());
            $query->whereDate('close_date', '>', now());
        })->orderBy('id', 'desc')->get();

        $closed = FarmList::where(function($query){
            $query->whereDate('close_date', '<=', now());
        })->orderBy('id', 'desc')->get();

        $type = 'closed';

        $user = Util::getUser();
        if(Util::completeProfile($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        else
            return view('user.farm.shortterm', ['farmlists'=>$farmlists, 'opened'=>$opened, 'closed'=>$closed, 'pending'=>$pending, 'close'=>true, 'type' => $type]);
    }

    public function opened(){
        $farmlists = FarmList::orderBy('id', 'desc')->get();

        $pending = FarmList::where(function($query){
            $query->whereDate('start_date', '>', now());
        })->orderBy('id', 'desc')->get();

        $opened = FarmList::where(function($query){
            $query->whereDate('start_date','<=', now());
            $query->whereDate('close_date', '>', now());
        })->orderBy('id', 'desc')->get();

        $closed = FarmList::where(function($query){
            $query->whereDate('close_date', '<=', now());
        })->orderBy('id', 'desc')->get();

        $type = 'opened';

        $user = Util::getUser();
        if(Util::completeProfile($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        else
        return view('user.farm.shortterm', ['farmlists'=>$farmlists, 'opened'=>$opened, 'closed'=>$closed, 'pending'=>$pending, 'open'=>true, 'type' => $type]);
    }

    public function loadFarms(Request $request, $type)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            2=> 'cover',
            3=> 'price',
            4=> 'status',
            5=> 'interest',
            6=> 'maturity_date',
            7=> 'available_units',
            8=> 'farm_opening_date',
            9=> 'investment_start_date',
        );

        switch ($type){
            case 'pending':
                $totalData = FarmList::where(function($query){
                    $query->whereDate('start_date', '>', now());
                })->count();

                break;
            case 'closed':
                $totalData = FarmList::where(function($query){
                    $query->whereDate('close_date', '<=', now());
                })->count();

                break;
            case 'opened':
                $totalData = FarmList::where(function($query){
                    $query->whereDate('start_date','<=', now());
                    $query->whereDate('close_date', '>', now());
                })->count();

                break;
            default:
                $totalData = FarmList::count();
                break;
        }

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            switch ($type){
                case 'pending':
                    $posts = FarmList::where(function($query){
                        $query->whereDate('start_date', '>', now());
                    })->latest()->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                    break;
                case 'closed':
                    $posts = FarmList::where(function($query){
                        $query->whereDate('close_date', '<=', now());
                    })->latest()->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                    break;
                case 'opened':
                    $posts = FarmList::where(function($query){
                        $query->whereDate('start_date','<=', now());
                        $query->whereDate('close_date', '>', now());
                    })->latest()->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                    break;
                default:
                    $posts = FarmList::latest()->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                    break;
            }

        }
        else {
            $search = $request->input('search.value');

            switch ($type){
                case 'pending':

                    $posts =  FarmList::latest()->where('title','LIKE',"%{$search}%")
                                ->where(function($query){
                                    $query->whereDate('start_date', '>', now());
                                })->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

                    $totalFiltered = FarmList::where('title','LIKE',"%{$search}%")
                                    ->where(function($query){
                                        $query->whereDate('start_date', '>', now());
                                    })->count();
                    break;
                case 'closed':

                    $posts =  FarmList::latest()->where('title','LIKE',"%{$search}%")
                                ->where(function($query){
                                    $query->whereDate('close_date', '<=', now());
                                })->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

                    $totalFiltered = FarmList::where('title','LIKE',"%{$search}%")
                                    ->where(function($query){
                                        $query->whereDate('close_date', '<=', now());
                                    })->count();
                    break;
                case 'opened':

                    $posts =  FarmList::latest()->where('title','LIKE',"%{$search}%")
                                ->where(function($query){
                                    $query->whereDate('start_date','<=', now());
                                    $query->whereDate('close_date', '>', now());
                                })
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

                    $totalFiltered = FarmList::where('title','LIKE',"%{$search}%")
                                        ->where(function($query){
                                            $query->whereDate('start_date','<=', now());
                                            $query->whereDate('close_date', '>', now());
                                        })->count();
                    break;
                default:

                    $posts =  FarmList::latest()->where('title','LIKE',"%{$search}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

                    $totalFiltered = FarmList::where('title','LIKE',"%{$search}%")
                                    ->count();
                    break;
            }

        }


        $data = array();
        if(!empty($posts)) {
            $i = 1;
            foreach ($posts as $post) {
                $status = '';
                $statusLinks = '<a href="/farmlist/'. $post->slug .'" class="dropdown-item">View Farm</a>';

                if($post->isOpen()){
                    $status .= '<div class="badge badge-success">Open</div>';
                    $statusLinks .= '<a href="/farmlist/invest/'. $post->slug .'" class="dropdown-item">Invest</a>';
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
                $nestedData['price'] = '₦' . number_format($post->price, 2);
                $nestedData['interest'] = $post->interest . '%';
                $nestedData['maturity_date'] = $post->maturity_date . ' Days';
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

    public function invest($slug){
        $farmlist = FarmList::where('slug', $slug)->first();
        if(! $farmlist->isOpen()){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>This farmlist is not open for investment.</div></div>');
        }else{
            $user = Util::getUser();
            if(Util::completeProfile($user))
                return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
            else if(Util::completeProfileKin($user))
                return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
            else
                return view('user.addInvest', ['farmlist'=>$farmlist]);
        }
    }

    public function verifyPay(Request $req){
        $trx = $req->trxref;
        if(Util::compareTranaction($trx)){
            $confirm = Util::confirm($trx);
            $email = $confirm->data->customer->email;
            $type = $confirm->data->metadata->type;
            $location = $confirm->data->metadata->location;
            $amount = $confirm->data->amount /100;

            if($confirm->data->status == 'success'){
                if($type=='investments'){
                    $farmlistSlug = $confirm->data->metadata->farmlist;
                    $units = $confirm->data->metadata->units;

                    $user = User::where('email', $email)->first();
                    $farmlist = FarmList::where('slug', $farmlistSlug)->first();
                    $wallet = Wallet::where('user', $user->email)->first();

                    $leftC = $farmlist->available_units - $units;

                    Investment::create([
                        'amount_invested'=>$amount,
                        'farmlist'=>$farmlist->slug,
                        'user'=>$email,
                        'maturity_status'=>'pending',
                        'units'=>$units,
                        'returns'=>$amount,
                        'status'=>'pending',
                        'user_id' =>  $user->id,
                        'farm_id' => $farmlist->id,
                        'rollover' => $confirm->data->metadata->rollover
                    ]);

                    Transaction::create([
                        'amount'=>$amount,
                        'type'=>'investments',
                        'user'=>$email,
                        'status'=>'approved',
                        'user_id' => $user->id
                    ]);

                    $farmlist->update([
                        'available_units'=>$leftC,
                    ]);

                    Verified::create([
                        'reference' => $trx,
                        'type' => $type,
                        'user_id' => $user->id
                    ]);
                    $title= ' ';
                    $name = $user->name;

                    $user->notify(new CustomNotification('Your investment of <strong>₦'. number_format($amount,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($amount,2).'</strong><br>Units Purchased: <strong>'.$req->unit.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($amount,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Created'));
//                    $content = 'Your investment has been created successfully. <br> You invested N'. number_format($amount,2).' under the farmlist titled <big>'.$farmlist->title.'</big>.';
                    $content = 'Your investment of <strong>₦'. number_format($amount,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($amount,2).'</strong><br>Units Purchased: <strong>'.$req->unit.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($amount,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong><br><br>Thank you for choosing Emerald Farms.';
                    $button = false;
                    $button_text = '';
                    $subject = "Investment Created";
                    Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

//                    $title= '';
//                    $name = Admin::first()->name;
//                    $content = 'An investment has been created successfully by '.$user->name.'. An investment of N '. number_format($amount,2).' under the farmlist titled: '.$farmlist->title.'.';
//                    $button = true;
//                    $button_text = 'View Investment';
//                    $button_link = route('admin.investments');
//                    $subject = "New Investment Created";
//                    Mail::to('transactions@emeraldfarms.ng')->send(new SendMailable($title,$name,$content,$button,$button_text,$subject,$button_link));

                    if(isset($confirm->data->metadata->referrer)){
                        $referrerUser = Util::getUserByCode($confirm->data->metadata->referrer);
                        $this->payReferrer($referrerUser, $amount);
                    }
                    return redirect("/farmlist")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your investment has been approved.</div></div>');

                }else if($type == 'deposits'){
                    $user = User::where('email', $email)->first();

                    if($location == 'bank'){
                        $user->emeraldbank()->increment('total_amount', $amount);
                        $depositLocation = 'Emerald Bank';
                        $balance = $user->emeraldbank->fresh()->total_amount;

                        $user->bookings()->create([
                            'category_id' => $confirm->data->metadata->category_id,
                            'units' => $confirm->data->metadata->units
                        ]);

                    }else{
                        $user->wallet()->increment('total_amount', $amount);
                        $depositLocation = 'Emerald Wallet';
                        $balance = $user->wallet->fresh()->total_amount;
                    }

                    $transaction = Transaction::create([
                        'amount' => $amount,
                        'type' => 'deposits',
                        'user' => $user->email,
                        'status' => 'approved',
                        'user_id' => $user->id,
                        'location' => $location
                    ]);

                    Verified::create([
                        'reference' => $trx,
                        'type' => $type,
                        'user_id' => $user->id
                    ]);

                    $user->notify(new CustomNotification('Your deposit of <strong>₦'.number_format($transaction->amount,2).'</strong> has been approved successfully.<br><br> Wallet summary:<br> Deposit amount: <strong>₦'.number_format($transaction->amount,2).'</strong><br> New wallet balance:  <strong>₦'.number_format($balance,2).'</strong>', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-chart-pie"></i></span>', 'Deposit Created'));

                    $title= ' ';
                    $name = $user->name;
                    $content = 'Your deposit of <strong>₦'.number_format($transaction->amount,2).'</strong> has been approved successfully.<br><br> Wallet summary:<br> Deposit amount: <strong>₦'.number_format($transaction->amount,2).'</strong><br> New wallet balance:  <strong>₦'.number_format($balance,2).'</strong> <br><br>Thank you for choosing Emerald Farms.';
                    $button = false;
                    $button_text = '';
                    $subject = "Deposit Created";
                    Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

                    $title= '';
                    $name = Admin::first()->name;
                    $content = 'A deposit request of <strong>₦'.number_format($amount,2). '</strong> in <strong>'. $depositLocation.  '</strong> has been done successfully via paystack funding by <strong>'.$user->name.'</strong>';
                    $button = true;
                    $button_text = 'View Deposit';
                    $button_link = route('admin.paystack');
                    $subject = "New deposit Approved";
                    // Mail::to('transactions@emeraldfarms.ng')->send(new SendMailable($title,$name,$content,$button,$button_text,$subject, $button_link));
                    return redirect('/wallet')->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your deposit has been approved and successfully.</div></div>');
                }else if($type == 'booking'){
                    $units = $confirm->data->metadata->units;

                    $user = User::where('email', $email)->first();
                    $farmlist = FarmList::findOrFail($confirm->data->metadata->farm_id);

                    Transaction::create([
                        'amount'=>$amount,
                        'type'=>'investments',
                        'user'=>$email,
                        'status'=>'approved',
                        'user_id' => $user->id
                    ]);

                    $farmlist->decrement('available_units', $units);

                    Verified::create([
                        'reference' => $trx,
                        'type' => $type,
                        'user_id' => $user->id
                    ]);

                    $booking = $user->bookings()->create([
                        'farmlist_id' => $farmlist->id,
                        'category_id' => $confirm->data->metadata->category_id,
                        'units' => $units,
                        'rollover' => !! $confirm->data->metadata->rollover,
                        'amount' => $amount,
                        'status' => 'approved'
                    ]);

                    $user->emeraldbank()->increment('total_amount', $amount);

                    $user->notify(new CustomNotification('Your booking of ₦ '. number_format($booking->amount) .' for '.ucwords($farmlist->title).' has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Booking Created'));


                    $title= ' ';
                    $name = $user->name;
                    $content = 'Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farmlist->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.';
                    $button = false;
                    $button_text = '';
                    $subject = "Booking Created";
                    Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

//                    $title= ' ';
//                    $name = Admin::first()->name;
//                    $content = 'A farm booking has been successfully created by <strong>' . $user->name . '</strong> in <strong>' . $farmlist->title . '</strong> farm';
//                    $button = true;
//                    $button_text = 'View Bookings';
//                    $button_link = route('admin.bookings.show', $farmlist->id);
//                    $subject = "Investment Booked";
//                    Mail::to('transactions@emeraldfarms.ng')->send(new SendMailable($title,$name,$content,$button,$button_text,$subject,$button_link));

                    return redirect("/wallet")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your investment has been approved.</div></div>');
                }
            } else {
                return redirect('/wallet')->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>An error occurred while making a payment.</div></div>');
            }
        }else {
            return redirect('/wallet')->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Invalid transaction detected.</div></div>');
        }

    }

    public function investPost(Request $req){

        $user = Util::getUser();
        $farmlist = FarmList::findOrFail($req->id);
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

            if($total_amount_returns > 250000){
                return redirect()->to('/wallet')->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> Our maximum online transaction is limited to N250,000. Please <a  href="#" style="text-decoration: underline;" data-toggle="modal" data-target="#exampleModal1">Click Here</a> to make a bank deposit / Transfer / USSD</div></div>');
            }

            $attribute = array('farmlist' => $farmlist->slug, 'amount' => $total_amount_returns, 'units' => $req->unit, 'type' => 'investments', 'rollover' => !! $req->rollover);

            return Redirect::away(Util::pay($user->email, $total_amount_returns, $attribute));

        } else {

            $newdd = $wallet->total_amount - $total_amount_returns;
            $leftC = $farmlist->available_units - $req->unit;

            $wallet->update(['total_amount'=>$newdd]);

            Investment::create([
                'amount_invested'=>$total_amount_returns,
                'farmlist'=>$farmlist->slug,
                'user'=>$user->email,
                'maturity_status'=>'pending',
                'units'=>$req->unit,
                'returns'=>$total_amount_returns,
                'status'=>'pending',
                'user_id' => $user->id,
                'farm_id' => $farmlist->id,
                'rollover' => !! $req->rollover
            ]);

            Transaction::create([
                'amount' => $total_amount_returns,
                'type' => 'investments',
                'user' => $user->email,
                'status' => 'approved',
                'user_id' => $user->id
            ]);

            $farmlist->update(['available_units' => $leftC]);

            $user->notify(new CustomNotification('Your investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$req->unit.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Created'));

            $title= ' ';
            $name = $user->name;
//            $content = 'Your investment has been created successfully. <br><br>Here is your investment overview below: <br>Amount Invested: N'. number_format($total_amount_returns,2).'<br>Units Purchased: '.$req->unit.'<br>Farm: '.$farmlist->title.'.';
            $content = 'Your investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$req->unit.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format(auth()->user()->wallet->fresh()->total_amount, 2) .'</strong><br><br>Thank you for choosing Emerald Farms.';
            $button = false;
            $button_text = '';
            $subject = "Investment Created";
            Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));


//            $title= '';
//            $name = Admin::first()->name;
//            $content = 'An investment has been created successfully by '.$user->name.'. An investment of N '. number_format($total_amount_returns,2).' under the farmlist titled <big>'.$farmlist->title.'</big>.';
//            $button = true;
//            $button_text = 'View Investment';
//            $button_link = route('admin.investments.short');
//            $subject = "New Investment Created";
//            Mail::to('transactions@emeraldfarms.ng')->send(new SendMailable($title,$name,$content,$button,$button_text,$subject,$button_link));

            return redirect("/farmlist")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your investment has been approved.</div></div>');
        }
    }

    public function wallet(){
        $user = Util::getUser();
        $banks = Bank::where('user', $user->email)->get();

        if(Util::completeProfile($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        else if(Util::completeProfileKin($user))
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        else
        return view('user.wallet', ['banks'=>$banks]);
    }

    public function addPayoutPost(Request $req){
        $user = Util::getUser();
        $banks = count(Bank::where('user', $user->email)->get());
        if($banks < 1){
            return redirect('/banks/add')->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please add at least one receiving bank account.</div></div>');
        }else{
            $transa = Transaction::where(['user'=>$user->email,'type'=>'payouts','status'=>'pending'])->get();
            if(count($transa) > 0){
                return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You have a pending payout. Please kindly contact the admin to approve your pending payment.</div></div>');
            }else {
                $wallet = Wallet::where('user', $user->email)->first();
                if($req->amount > $wallet->total_amount){
                    return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Insufficient wallet balance.</div></div>');
                }else {
                     Transaction::create([
                        'amount'=>$req->amount,
                        'type'=>'payouts',
                        'user'=>$user->email,
                        'status'=>'pending',
                        'bank'=>$req->bank,
                        'user_id' => $user->id
                    ]);

                    $user->notify(new CustomNotification('Your payout of ₦'.number_format($req->amount,2).' has been queued and pending system approval.<br><br>We will update the status of your transaction within 24 hours.', '<span class="dropdown-item-icon bg-warning text-white"><i class="fas fa-chart-pie"></i></span>', 'Payout Request'));

                    $title= ' ';
                    $name = $user->name;
                    $content = 'Your payout of <strong>₦'.number_format($req->amount,2).'</strong> has been queued and pending system approval.<br><br>We will update the status of your transaction within 24 hours.<br><br>Thank you for choosing Emerald Farms.';
                    $button = false;
                    $button_text = '';
                    $subject = "Payout Request";
                    Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

                    $title= '';
                    $name = Admin::first()->name;
                    $content = 'A payout request of <strong>₦'.number_format($req->amount,2).'</strong> has been initiated by <strong>'. $user->name.'</strong> from the current wallet balance of <strong>₦'.number_format($wallet->total_amount,2).'</strong>. Kindly login to approve or decline';
                    $button = true;
                    $button_text = 'View Payout';
                    $button_link = route('admin.payouts');
                    $subject = "Payout Request";
                    // Mail::to('transactions@emeraldfarms.ng')->send(new SendMailable($title,$name,$content,$button,$button_text,$subject, $button_link));
                    return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your request has been submitted and pending authorization.</div></div>');
                }
            }
        }

    }

    public function addDepositPost(Request $req)
    {
        if(! $req->filled('amount')){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You have to provide an amount you want to deposit.</div></div>');
        }

        if($req['method'] == "null"){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You have to provide a payment method.</div></div>');
        }

        $user = Util::getUser();
        if($req['method'] == 'bank'){
            Transaction::create([
                'amount'=>$req->amount,
                'type'=>'deposits',
                'user'=>$user->email,
                'status'=>'pending',
                'bank'=>$req->bank,
                'user_id' => $user->id,
                'location' => $req->location
            ]);

            if($req->location == 'bank') {
                auth()->user()->bookings()->create([
                    'category_id' => $req->category_id,
                    'units' => $req->units,
                    'amount'=>$req->amount,
                ]);
            }

            $user->notify(new CustomNotification('Your deposit of <strong>₦'.number_format($req->amount,2).'</strong> has been queued and pending system approval.<br><br> We will update the status of your transaction with 24 hours.', '<span class="dropdown-item-icon bg-warning text-white"><i class="fas fa-chart-pie"></i></span>', 'Deposit Queued'));

            $title= ' ';
            $name = $user->name;
            $content = 'Your deposit of <strong>₦'.number_format($req->amount,2).'</strong> has been queued and pending system approval.<br><br> We will update the status of your transaction with 24 hours. <br><br> Thank you for choosing Emerald Farms.';
//            $content = 'Your deposit of N'.number_format($req->amount,2).' has been booked successfully.<br>Pending system approval. <br> We will update the status of your transaction within 24hours.';
            $button = false;
            $button_text = '';
            $subject = "Deposit Queued";
            Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

            $title= '';
            $name = Admin::first()->name;
            $content = 'A deposit request of <strong>₦'.number_format($req->amount,2).'</strong> has been booked by <strong>'.$user->name.'</strong><br>Pending your approval. Kindly login to your dashboard to approve';
            $button = true;
            $button_text = 'View Deposit';
            $button_link = route('admin.deposits');
            $subject = "New deposit booked";
            // Mail::to('transactions@emeraldfarms.ng')->send(new SendMailable($title,$name,$content,$button,$button_text,$subject, $button_link));

            return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your request has been submitted and pending authorization.</div></div>');
        }else {
            if($req->amount > 250000){
                return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Our maximum online payment deposit is limited to N250,000. Please deposit via bank deposit / USSD / Transfer option in the deposit dropdwon.</div></div>');
            }else {
                $attributes = ['amount'=>$req->amount, 'type'=>'deposits','location' => $req->location];

                if($req->location == 'bank'){
                    $attributes = array_merge($attributes,['category_id' => $req->category_id, 'units' => $req->units]);
                }

                return Redirect::away($this->payment->pay($user->email, $req->amount, $attributes));

            }
        }
    }

    public function profile(){
        $user = Util::getUser();
        $banks = Bank::where('user', $user->email)->get();
        if($user->welcome_mail != 'sent'){

            $title= ' ';
            $name = '';
            $content = 'Welcome to Emerald Farms <strong>'.ucwords($user->name).'</strong>.<br><br>You are about to start a journey of fulfilment.<br><br>We are excited to have you on board as together we can develop the Agricultural Sector in Nigeria, ensure food security, environmental sustainability and economic opportunities.<br><br>If you have any questions or enquiries, please do not hesitate to write or call any of our hotlines.';
            $button = false;
            $button_text = '';
            $button_link = '';
            $subject = "Welcome Mail";
            Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject,$button_link));
            User::where('id', $user->id)->update(['welcome_mail'=>'sent']);
        }
        $active = Investment::where(['user'=>$user->email,'status'=>'active'])->get();
        return view('user.profile', ['user'=>$user,'active'=>$active,'banks' => $banks]);
    }

    public function editProfile(Request $req){
        $user = Util::getUser();
        User::where('id', $user->id)->update([
            'name'=>$req->name,
            'phone'=>$req->phone,
            'address'=>$req->address,
            'state'=>$req->state,
            'country'=>$req->country,
            'zip'=>$req->zip,
            'city'=>$req->city,
            'dob' => $req->dob
        ]);

        if ($files = $req->file('passport')) {

            if(!in_array($files->getClientOriginalExtension(),['jpg', 'jpeg','png','gif'])){
                redirect()->back()->with('message', '<div class="alert alert-error alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Upload proper image.</div></div>');
            }
           $destinationPath = 'assets/uploads/passports'; // upload path
           $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
           $files->move($destinationPath, $profileImage);
           User::where('id', $user->id)->update([
                'passport'=>$destinationPath."/".$profileImage,
            ]);
        }
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your profile has been updated.</div></div>');
    }

    public function editProfileKin(Request $req){
        $user = Util::getUser();
        User::where('id', $user->id)->update([
            'nk_Name'=>$req->name,
            'nk_Phone'=>$req->phone,
            'nk_Address'=>$req->address,
            'nk_Email'=>$req->email,
        ]);

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your next of kin details has been updated.</div></div>');
    }

    public function banks(){
        $user = Util::getUser();
        $banks = Bank::where('user', $user->email)->get();
        return view('user.banks', ['banks'=>$banks]);
    }

    public function addBank(){
        return view('user.addBank');
    }

    public function deleteBank($id){
        $bank = Bank::findOrFail($id);

        if($bank->user != auth()->user()->email){
            return back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Cannot delete bank detail.</div></div>');;
        }

        $bank->delete();
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Bank details deleted successfully.</div></div>');;
    }

    public function addbankPost(Request $req){
        $user = Util::getUser();
        $banks = Bank::where('user', $user->email)->get();
        $bank = Bank::where('user', auth()->user()['email'])->where('account_number', $req->account_number)->where('bank_name', $req->bank)->first();
        if ($bank){
            return redirect("/banks")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You already added this account number.</div></div>');
        }
        if(count($banks) >2){
            return redirect("/banks")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You cannot create more than 3 bank account details.</div></div>');
        }else {

            Bank::create([
                'bank_name'=>$req->bank,
                'account_name'=>$req->account_name,
                'account_number'=>$req->account_number,
                'user'=>$user->email,
                'account_information' => $req->account_information
            ]);

            if ($req['saveAsDefault']){
                $user['name'] = $req['account_name'];
                $user->update();
            }

            return redirect("/banks")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Bank details created successfully.</div></div>');
        }
    }

    public function editBank($id){
        return view('user.editBank',['bank' => Bank::findOrFail($id)]);
    }

    public function editBankPost(Request $req){
        $user = Util::getUser();
        $bank = Bank::where('user', $user->email)->whereId($req->bank_id)->first();
        if ($bank['bank_name'] != $req['bank'] || $bank['account_number'] != $req['account_number']){
            $newBank = Bank::where('user', auth()->user()['email'])->where('account_number', $req->account_number)->first();
            if ($newBank){
                return redirect("/banks")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You already added this account number.</div></div>');
            }
        }
        if(! $bank){
            return redirect("/banks")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Something went wrong</div></div>');
        }else {

            $bank->update([
                'bank_name'=>$req->bank,
                'account_name'=>$req->account_name,
                'account_number'=>$req->account_number,
                'user'=>$user->email,
                'account_information'=>$req->account_information,
            ]);

            if ($req['saveAsDefault']){
                $user['name'] = $req['account_name'];
                $user->update();
            }

            return redirect("/banks")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Bank details updated successfully.</div></div>');
        }
    }

    public function newslist($farmlist){
        if(request()->get('q') != ''){
            $q = request()->get('q');
            $farmlist = FarmList::where('slug', $farmlist)->first();
            $news = NewsUpdate::where([['farmlist', $farmlist->slug], ['title', 'LIKE', "%{$q}%"]])->orderBy('id', 'desc')->get();
            return view('user.newslists', ['news'=>$news, 'farmlist'=>$farmlist]);
        }else {
            $farmlist = FarmList::where('slug', $farmlist)->first();
            $news = NewsUpdate::where('farmlist', $farmlist->slug)->orderBy('id', 'desc')->get();
            return view('user.newslists', ['news'=>$news, 'farmlist'=>$farmlist]);
        }

    }

    public function news(){
        $farmlists = FarmList::orderBy('id', 'desc')->get();
        return view('user.news', ['farmlists'=>$farmlists]);
    }

    public function viewNews($slug){
        $news = NewsUpdate::where('slug', $slug)->first();
        return view('user.viewNews', ['news'=>$news]);
    }

    public function logout () {
        //logout user
        auth()->logout();
        // redirect to homepage
        return redirect("/");
    }

    public function referralGuide()
    {
        return view('user.referralGuide');
    }

    public function show($slug)
    {
        $farmlist = FarmList::where('slug', $slug)->first();

        return view('user.showFarm', ['farmlist'=>$farmlist]);
    }

    public function farmlistByCategory(Category $category)
    {

        $farmlist = $category->farms()->latest()->get()->filter(function($farm){
            return  $farm->start_date->gt(now()) && $farm->close_date->gt(now());
        })->first();

        if(! $farmlist){
            return response()->json(['data' => null], 200);
        }

        return response()->json(['data' => $farmlist], 200);
    }

    public function showLongInvestments($id)
    {
        return view('user.investment.show',['investment' => MilestoneInvestment::findOrFail($id)]);
    }

    public function showShortInvestments($id)
    {
        return view('user.investment_show',['investment' => Investment::findOrFail($id)]);
    }
}
