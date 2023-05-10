<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\Category;
use App\FarmList;
use App\Http\Controllers\Controller;
use App\Investment;
use App\Mail\SendMailable;
use App\Notifications\BookingNotification;
use App\Notifications\CustomNotification;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index()
    {
        return view('admin.bookingsFarm');
    }

    public function loadBooking(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            2 => 'cover',
            3 => 'status',
            4 => 'actions',

        );

        $totalData = FarmList::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = FarmList::latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  FarmList::latest()->where('title','LIKE',"%{$search}%")
                ->orWhere('price','LIKE',"%{$search}%")
                ->orWhere('interest','LIKE',"%{$search}%")
                ->orWhere('status','LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = count($posts);
        }


        $data = array();
        if(!empty($posts)) {
            $i = 1;
            foreach ($posts as $post) {
                $status = '';

                if($post->isOpen()){
                    $status .= '<div class="badge badge-success">Open</div>';
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
                $nestedData['status'] = $status;
                $nestedData['actions'] = '<div class="dropdown show">
                                            <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a href="/admin/farmlist/'. $post->id .'/bookings" class="dropdown-item">View</a>
                                            </div>
                                        </div>';;
                ;


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

    public function show(FarmList $farmlist)
    {
        return view('admin.bookingsFarmShow', ['farmlist' => $farmlist]);
    }

    public function approve(Booking $booking)
    {
        if($booking->farm->available_units < $booking->units){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Farm does not have sufficient units to approve booking.</div></div>');
        }

        if ($booking->farm->isOpen()){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Farm already opened, cannot approve booking.</div></div>');
        }elseif ($booking->farm->isClosed()){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Farm already closed, cannot approve booking.</div></div>');
        }else{
            $booking->update(['status' => 'approved']);
            $booking->user->emeraldbank()->increment('total_amount', $booking->amount);
            $subject = "Booking Created";
            $content = 'Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($booking->farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.';
            $booking->user->notify(new BookingNotification($booking, true, false, false));
        }

        $booking->transaction()->update(['status' => 'approved']);

        $booking->farm()->decrement('available_units', $booking->units);

        $user = $booking->user;
        $title= ' ';
        $name = $user->name;
        $button = false;
        $button_text = '';
        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Booking Approved succesfully.</div></div>');
    }

    public function decline(Booking $booking)
    {
        $booking->update(['status'=>'declined']);
        $booking->transaction()->update(['status' => 'declined']);

        $user = $booking->user;
        $title= ' ';
        $name = $user->name;
        $content = "Your Booking in <strong>{$booking->farm->title}</strong> has been declined. Please reach out to the admin if this is a mistake.";
        $button = false;
        $button_text = '';
        $subject = "Booking Declined";
        $booking->user->notify(new BookingNotification($booking, false, true, false));
        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Booking Declined succesfully.</div></div>');
    }

    public function bookForUser(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $farm = FarmList::findOrfail($request->farm_id);
        $category = Category::findOrfail($request->category_id);

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
                return $this->processBank($request, $user, $farm, $category, $units);
                break;

            case 'wallet':
                return $this->processWalletPayment($request, $user, $farm, $category, $units);
                break;

            default:
                return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Choose Payment Method.</div></div>');
                break;

        }

    }

    private function processBank($request, $user, $farm, $category, $units)
    {
        $booking = $this->createFarmBooking($user, [
            'farmlist_id' => $farm->id,
            'category_id' => $category->id,
            'units' => $units,
            'rollover' => !! $request->rollover,
            'amount' => $request->amount,
            'status' => 'approved'
        ]);

        Transaction::create([
            'amount' => $request->amount,
            'type' => 'booking',
            'user' => $user->email,
            'status' => 'approved',
            'user_id' => $user->id,
            'location' => 'wallet',
            'booking_id' => $booking->id
        ]);

        $user->emeraldbank()->increment('total_amount', $request->amount);
        $farm->decrement('available_units', $units);

        $user->notify(new CustomNotification('Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Booked'));

        $title= ' ';
        $name = $user->name;
        $content = 'Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.';
        $button = false;
        $button_text = '';
        $subject = "Investment Booked";
        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Booking done successfully via deposit.</div></div>');
    }

    private function processWalletPayment(Request $request, $user, $farm, $category, $units)
    {
        if($user->wallet->total_amount < $request->amount){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Insufficient funds in user\'s wallet.</div></div>');
        }

        $booking = $this->createFarmBooking($user, [
            'farmlist_id' => $farm->id,
            'category_id' => $category->id,
            'units' => $units,
            'rollover' => !! $request->rollover,
            'amount' => $request->amount,
            'status' => 'approved'
        ]);

        Transaction::create([
            'amount' => $request->amount,
            'type' => 'booking',
            'user' => $user->email,
            'status' => 'approved',
            'user_id' => $user->id,
            'location' => 'wallet',
            'booking_id' => $booking->id
        ]);

        $user->wallet()->decrement('total_amount', $request->amount);
        $user->emeraldbank()->increment('total_amount', $request->amount);
        $farm->decrement('available_units', $units);

        $user->notify(new CustomNotification('Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Booked'));

        $title= ' ';
        $name = $user->name;
        $content = 'Your booking of <strong>₦ '. number_format($booking->amount) .'</strong> for <strong>'.ucwords($farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.<br><br>Thank you for choosing Emerald Farms.';
        $button = false;
        $button_text = '';
        $subject = "Investment Booked";
        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Booking done successfully from wallet.</div></div>');
    }

    public function createFarmBooking($user, $attributes = [])
    {
        return $user->bookings()->create($attributes);
    }

}
