<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Category;
use App\Exports\AllTransactionsExport;
use App\Exports\AllUsersExport;
use App\Exports\DepositTransactionsExport;
use App\Exports\InvestmentTransactionsExport;
use App\Exports\MilestoneInvestmentExport;
use App\Exports\PayoutRequestTransactionsExport;
use App\Exports\PayoutTransactionsExport;
use App\Exports\PaystackTransactionsExport;
use App\Exports\ReferralTransactionsExport;
use App\Exports\UnverifiedUsersExport;
use App\Exports\VerifiedUsersExport;
use App\Mail\NewsletterMailable;
use App\MilestoneFarm;
use App\MilestoneInvestment;
use App\Notifications\CustomNotification;
use App\Verified;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\FarmList;
use App\User;
use App\Package;
use App\Wallet;
use App\Investment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Transaction;
use App\Http\Controllers\Globals as Util;
use App\Admin;
use App\Mail\SendMailable;
use Mail;
use App\Bank;
use App\NewsUpdate;
use App\Farmfund;
use App\PaidMileStone;

class AdminController extends Controller
{

    public function index(){
    	$farmLists = count(FarmList::get());
    	$users = count(User::get());
    	$packages = count(Package::get());
    	$wallet = Wallet::sum('total_amount');
    	$recent = User::orderBy('id', 'desc')->limit(6)->get();
        $emeraldbank = FarmFund::sum('total_amount');
        $withdrawable = $emeraldbank - Booking::where('status','approved')->sum('amount');
    	return view('admin.index', ['farmLists'=>$farmLists, 'users'=>$users, 'packages'=>$packages, 'wallet'=>$wallet, 'recent'=>$recent, 'emeraldbank'=>$emeraldbank, 'withdrawable'=>$withdrawable]);

    }

    public function addPackages(){
    	return view('admin.addPackage');
    }

    public function addPackagesPost(Request $req){
    	Package::create([
    		'name'=>$req->name,
    		'interest'=>$req->interest,
    		'maturity_date'=>$req->maturity_date,
    		'available_units'=>$req->available_units,
    		'description'=>$req->description,
    	]);
    	return redirect("/admin/packages")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your package has been created successfully.</div></div>');
    }

    public function packages(){
    	return view('admin.packages', ['packages'=>Package::orderBy('id', 'desc')->get()]);
    }

    public function deletePackage($slug){
    	$investments = Investment::where('package', $slug)->get();
    	if(count($investments) > 0){
    		return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your package cannot be delete because it is currently tied to an investment.</div></div>');
    	}else {
    		Package::where('slug', $slug)->delete();
    		return redirect("/admin/packages")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your package has been deleted successfully.</div></div>');
    	}
    }

    public function editPackage($slug){
    	$package = Package::where('slug', $slug)->first();
    	return view('admin.addPackage', ['edit'=>true, 'package'=>$package]);
    }

    public function editPackagePost(Request $req){
    	Package::where('id', $req->id)->update([
    		'name'=>$req->name,
    		'interest'=>$req->interest,
    		'maturity_date'=>$req->maturity_date,
    		'available_units'=>$req->available_units,
    		'description'=>$req->description,
    	]);
    	return redirect("/admin/packages")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your package has been updated successfully.</div></div>');
    }

    public function categories(){
        return view('admin.categories', ['categories'=> Category::latest()->get()]);
    }

    public function addCategories(){
        return view('admin.addCategory');
    }

    public function addCategoriesPost(Request $req){

        Category::create([
            'name'=>$req->name,
            'description'=>$req->description,
        ]);

        return redirect("/admin/categories")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your category has been created successfully.</div></div>');
    }

    public function deleteCategories(Category $category){

        if($category->farms()->count() > 0){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your category cannot be delete because it is currently tied to a farm.</div></div>');
        }else {
            $category->delete();
            return redirect("/admin/categories")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your category has been deleted successfully.</div></div>');
        }
    }

    public function editCategory(Category $category){
        return view('admin.addCategory', ['edit'=>true, 'category'=>$category]);
    }

    public function editCategoryPost(Request $req){
        Category::findOrFail($req->id)->update([
            'name'=>$req->name,
            'description'=>$req->description,
        ]);

        return redirect("/admin/categories")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your category has been updated successfully.</div></div>');
    }

    public function addFarmlistPost(Request $req){

        if($req->category_id == 'null'){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You need to select a category.</div></div>');
        }

        if ($files = $req->file('cover')) {

           $destinationPath = 'assets/uploads/courses'; // upload path
           $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
           $files->move($destinationPath, $profileImage);

           if(FarmList::whereTitle($req->title)->exists() ){
               return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Farm name already exist. Choose a new name.</div></div>');
           }

           $farmlist = FarmList::create([
                'title'=>$req->title,
                'start_date'=> Carbon::parse($req->start_date)->format('Y-m-d H:i:s'),
                'close_date'=> Carbon::parse($req->close_date)->format('Y-m-d H:i:s'),
                'cover'=>$destinationPath."/".$profileImage,
                'price'=>$req->price,
                'description'=>$req->description,
                'interest' => $req->interest,
                'maturity_date' => $req->maturity_date,
                'available_units' => $req->available_units,
                'category_id' => $req->category_id
            ]);

           return redirect("/admin/farmlist/short")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your farm list has been created successfully.</div></div>');
       }

        return redirect("/admin/farmlist/short")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You need to provide a cover image.</div></div>');
    }

    public function farmlist(){
    	return view('admin.farmlist');
    }

    public function loadFarms(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            2=> 'cover',
            3=> 'start_date',
            4=> 'close_date',
            5=> 'price',
            6=> 'status',
            7=> 'interest',
            8=> 'maturity_date',
            9=> 'units',
            10=> 'actions',

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
                $nestedData['start_date'] = date('M d, Y', strtotime($post->start_date));
                $nestedData['close_date'] = date('M d, Y', strtotime($post->close_date));
                $nestedData['price'] = '₦' . number_format($post->price, 2);
                $nestedData['interest'] = $post->interest . '%';
                $nestedData['maturity_date'] = $post->maturity_date . ' Days';
                $nestedData['units'] = $post->available_units . ' Units';
                $nestedData['status'] = $status;
                $nestedData['actions'] =  '<div class="dropdown show">
                                            <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a href="/admin/farmlist/delete/'. $post->slug .'" class="dropdown-item" onclick="return confirm("Are you sure you want to delete this farmlist?");">Delete</a>
                                                <a href="/admin/farmlist/edit/'. $post->slug .'" class="dropdown-item">Edit</a>
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

    public function editFarmlist($slug){
    	return view('admin.farm.short', ['farmlist' => FarmList::where('slug', $slug)->first(), 'edit'=>true]);
    }

    public function editFarmlistPost(Request $request){
    	$editing = FarmList::where('id', $request->id)->first();

    	if ($files = $request->file('cover')) {
    		if($files != ''){
    			Storage::delete($editing->cover);
    			$destinationPath = 'assets/uploads/courses'; // upload path
	            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
	            $files->move($destinationPath, $profileImage);
	            $editing->update([
		    		'cover'=>$destinationPath."/".$profileImage,
	            ]);
    		}
       	}

        $editing->update([
            'title'=>$request->title,
            'start_date' => Carbon::parse($request->start_date)->format('Y-m-d H:i:s'),
            'close_date' => Carbon::parse($request->close_date)->format('Y-m-d H:i:s'),
            'price'=>$request->price,
            'description'=>$request->description,
            'interest' => $request->interest,
            'maturity_date' => $request->maturity_date,
            'available_units' => $request->available_units,
            'category_id' => $request->category_id
        ]);

        return redirect("/admin/farmlist/short")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your farm list has been edited successfully.</div></div>');

    }

    public function deleteFarmlist($slug){

        $farm = FarmList::where('slug', $slug)->first();

        if(! $farm){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Farm does not exist.</div></div>');

        }

    	if(Investment::where('farmlist', $slug)->count() > 0){
    		return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your farmlist cannot be delete because it is currently tied to an investment.</div></div>');
    	}

        if(Booking::where('farmlist_id', $farm->id)->count() > 0){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your farmlist cannot be delete because it is currently tied to a booking.</div></div>');
        }


        $farm->delete();
        return redirect("/admin/farmlist")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your farmlist has been deleted successfully.</div></div>');
    }

    public function users(){
    	return view('admin.users');
    }

    public function verifiedUsers(){
    	return view('admin.users', ['verified'=>true]);
    }

    public function unverifiedUsers(){
    	return view('admin.users', ['unverified'=>true]);
    }

    public function viewUser($id){
        $user = User::where('id', $id)->first();
        $active = Investment::where(['user'=>$user->email,'status'=>'active'])->get();
        $wallet = Wallet::where('user', $user->email)->first();
        $investments = Investment::where('user', $user->email)->orderBy('id', 'desc')->get();
        $transactions = Transaction::where('user', $user->email)->orderBy('id', 'desc')->get();
        $banks = Bank::where('user', $user->email)->get();
        return view('admin.viewUser', ['user'=>$user,'active'=>$active, 'wallet'=>$wallet, 'investments'=>$investments, 'transactions'=>$transactions, 'banks'=>$banks]);
    }

    public function transactions(){
    	return view('admin.transactions');
    }

    public function loadTransactions(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'name',
            2=> 'amount',
            3=> 'type',
            4=> 'status',
            5=> 'updated_at',
            6=> 'action',
        );

        $totalData = Transaction::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = Transaction::latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  Transaction::latest()->where('user','LIKE',"%{$search}%")
                ->orWhere('amount','LIKE',"%{$search}%")
                ->orWhere('status','LIKE',"%{$search}%")
                ->orWhere('type','LIKE',"%{$search}%")
                ->orWhereHas('user', function($q) use ($search){
                    $q->where('name','LIKE',"%{$search}%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = Transaction::where('user','LIKE',"%{$search}%")
                ->orWhere('amount','LIKE',"%{$search}%")
                ->orWhere('status','LIKE',"%{$search}%")
                ->orWhere('type','LIKE',"%{$search}%")
                ->orWhereHas('user', function($q) use ($search){
                    $q->where('name','LIKE',"%{$search}%");
                })
                ->count();

        }

        $data = array();
        if(!empty($posts)) {
            $i = 1;
            foreach ($posts as $post) {
                $links = '';
                if ($post->type == 'deposits' && $post->status == 'pending') {
                    $links .= '<a href="/admin/transactions/deposits/approve/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to approve this deposits?");">Approve</a>';
                    $links .= '<a href="/admin/transactions/deposits/decline/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to decline this deposits?");">Decline</a>';
                } elseif ($post->type == 'payouts' && $post->status == 'pending') {
                    $links .= '<a href="/admin/transactions/payouts/approve/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to approve this payouts?");">Approve</a>';
                    $links .= '<a href="/admin/transactions/payouts/decline/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to decline this payouts?");">Decline</a>';
                }else if($post->type == 'booking' && $post->status == 'pending'){
                    $links .= '<a href="/admin/bookings/' . $post->booking_id . '/approve" class="dropdown-item" onclick="return confirm("Are you sure you want to approve this payouts?");">Approve</a>';
                    $links .= '<a href="/admin/bookings/' . $post->booking_id . '/decline" class="dropdown-item" onclick="return confirm("Are you sure you want to decline this payouts?");">Decline</a>';
                }

                $status = '';

                if($post->status == 'pending'){
                    $status .= '<div class="badge badge-warning">Pending</div>';
                }elseif($post->status == 'approved' || $post->status == 'paid'){
                    $status .= '<div class="badge badge-success">Approved</div>';
                }elseif($post->status == 'declined'){
                    $status .= '<div class="badge badge-danger">Declined</div>';
                }else{
                    $status .= '<div class="badge badge-danger">' . $post->status . '</div>';
                }

                $member = Util::getUserByEmail($post->user);
                $nestedData['sn'] = $i++;
                $nestedData['name'] = '<a href="/admin/users/view/'. $member->id . '" target="_blank">
                                            ' .ucwords($member->name). '
                                        </a>';;
                $nestedData['amount'] = '₦' . number_format(implode("", explode(',',$post->amount))) .'.00';
                $nestedData['date_last_updated'] = date('M d, Y', strtotime($post->updated_at));
                $nestedData['type'] = $post->type == 'booking' ? 'Booking/Deposit' : ucwords($post->type);
                $nestedData['status'] = $status;
                if (($post->type == 'deposits' && $post->status == 'pending') || ($post->type == 'payouts' && $post->status == 'pending') || ($post->type == 'booking' && $post->status == 'pending')){
                    $nestedData['action'] = '<div class="dropdown show">
                                      <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                      </a>
                                           <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                ' . $links . '
                                          </div>
                                      </div>
                                    </div>';
                }else{
                    $nestedData['action'] = '';
                }
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

    public function approveDeposits($id){
        $transaction = Transaction::findOrFail($id);

        $transaction->update(['status'=>'approved']);

        if($transaction->location == 'bank'){
            $transaction->emeralduser->emeraldbank()->increment('total_amount', $transaction->amount);
            $depositLocation = 'Emerald Bank';
            $balance = $transaction->emeralduser->emeraldbank->fresh()->total_amount;
        }else{
            $transaction->emeralduser->wallet()->increment('total_amount', $transaction->amount);
            $depositLocation = 'Emerald Wallet';
            $balance = $transaction->emeralduser->wallet->fresh()->total_amount;
        }

        $used = User::where('email', $transaction->user)->first();

        $used->notify(new CustomNotification('Your deposit of <strong>₦'.number_format($transaction->amount,2).'</strong> has been approved successfully.<br><br> Wallet summary:<br> Deposit amount: <strong>₦'.number_format($transaction->amount,2).'</strong><br> New wallet balance:  <strong>₦'.number_format($balance,2).'</strong>', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-chart-pie"></i></span>', 'Deposit Created'));

        $title= ' ';
        $name = $used->name;
        $content = 'Your deposit of <strong>₦'.number_format($transaction->amount,2).'</strong> has been approved successfully.<br><br> Wallet summary:<br> Deposit amount: <strong>₦'.number_format($transaction->amount,2).'</strong><br> New wallet balance:  <strong>₦'.number_format($balance,2).'</strong> <br><br>Thank you for choosing Emerald Farms.';
        $button = false;
        $button_text = '';
        $subject = "Deposit Created";
        Mail::to($used->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Transaction approved succesfully.</div></div>');
    }

    public function declineDeposits($id){
        Transaction::where('id', $id)->update([
            'status'=>'declined',
        ]);
        $trans = Transaction::where('id', $id)->first();
        $used = User::where('email', $trans->user)->first();

        $used->notify(new CustomNotification('Your deposit of <strong>₦'.number_format($trans->amount,2).'</strong> has been declined.<br><br> Please contact our support team if you have any complaints or queries.', '<span class="dropdown-item-icon bg-danger text-white"><i class="fas fa-chart-pie"></i></span>', 'Deposit Declined'));

        $title= ' ';
        $name = $used->name;
        $content = 'Your deposit of <strong>₦'.number_format($trans->amount,2).'</strong> has been declined.<br><br> Please contact our support team if you have any complaints or queries.';
        $button = false;
        $button_text = '';
        $subject = "Deposit Declined";
        Mail::to($used->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Transaction declined succesfully.</div></div>');
    }

    public function declinePayouts($id){
        Transaction::where('id', $id)->update([
            'status'=>'declined',
        ]);
        $trans = Transaction::where('id', $id)->first();
        $used = User::where('email', $trans->user)->first();

        $used->notify(new CustomNotification('Your payout of <strong>₦'.number_format($trans->amount,2).'</strong> has been declined.<br><br> Please contact our support team if you have any complaints or queries.', '<span class="dropdown-item-icon bg-danger text-white"><i class="fas fa-chart-pie"></i></span>', 'Payout Declined'));

        $title= ' ';
        $name = $used->name;
        $content = 'Your payout of <strong>₦'.number_format($trans->amount,2).'</strong> has been declined.<br><br> Please contact our support team if you have any complaints or queries.';
        $button = false;
        $button_text = '';
        $subject = "Payout Declined";
        Mail::to($used->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Transaction declined succesfully.</div></div>');
    }

    public function approvePayouts($id){
        $transaction = Transaction::where('id', $id)->first();
        $wallet = Wallet::where('user', $transaction->user)->first();

        if($transaction->amount > $wallet->total_amount){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Cannot Approve Payout. User does not have enough funds in wallet for this payout</div></div>');
        }

        Transaction::where('id', $id)->update(['status'=>'paid']);
        $new = $wallet->total_amount - $transaction->amount;
        Wallet::where('id', $wallet->id)->update(['total_amount'=>$new]);
        $used = User::where('email', $wallet->user)->first();

        $used->notify(new CustomNotification('Your payout of <strong>₦'.number_format($transaction->amount,2).'</strong> has been approved.<br><br>Wallet summary:<br>Payout amount: <strong>₦'.number_format($transaction->amount,2).'</strong><br>New wallet balance: <strong>₦'.number_format($new,2).'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-chart-pie"></i></span>', 'Payout Created'));

        $title= ' ';
        $name = $used->name;
        $content = 'Your payout of <strong>₦'.number_format($transaction->amount,2).'</strong> has been approved.<br><br>Wallet summary:<br>Payout amount: <strong>₦'.number_format($transaction->amount,2).'</strong><br>New wallet balance: <strong>₦'.number_format($new,2).'</strong><br><br>Thank you for choosing Emerald Farms.';
        $button = false;
        $button_text = '';
        $subject = "Payout Created";
         Mail::to($used->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Payout made succesfully.</div></div>');
    }

    public function shortInvestments()
    {
        return view('admin.investments.short');
    }

    public function longInvestments()
    {
        return view('admin.investments.long');
    }

    public function loadInvestments(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'name',
            2=> 'amount',
            3=> 'farmlist',
            4=> 'maturity_date',
            5=> 'maturity_status',
            6=> 'units',
            7=> 'days_remaining',
            8=> 'expected_returns',
            9=> 'status',
            10=> 'status',
        );

        $totalData = Investment::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = Investment::latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  Investment::latest()->where('user','LIKE',"%{$search}%")
                ->orWhere('amount_invested','LIKE',"%{$search}%")
                ->orWhere('maturity_status','LIKE',"%{$search}%")
                ->orWhere('status','LIKE',"%{$search}%")
                ->orWhere('units','LIKE',"%{$search}%")
                ->orWhereHas('farm', function($q) use ($search){
                    $q->where('title','LIKE',"%{$search}%");
                })
                ->orWhereHas('user', function($q) use ($search){
                    $q->where('name','LIKE',"%{$search}%");
                })
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

                $cur = strtotime(date('Y-m-d H:i:s'));
                $mat = strtotime($post->maturity_date);
                $diff = $mat - $cur;
                $farm = Util::getFarmlist($post->farmlist);
                if(! $farm){
                    Log::info($post);
                    continue;
                }
                $interest = $post->amount_invested*(($farm->interest ?? 0)/100);
                $add = $post->amount_invested+$interest;

                $member = Util::getUserByEmail($post->user);
                $nestedData['sn'] = $i++;
                $nestedData['name'] = '<a href="/admin/users/view/'. $member->id . '" target="_blank">
                                            ' .ucwords($member->name). '
                                        </a>';;
                $nestedData['amount'] = '₦' . number_format($post->amount_invested, 2);
                $nestedData['farmlist'] = ucwords(Util::getFarmlist($post->farmlist)->title);
                $nestedData['maturity_date'] = $post->maturity_date != null ? date('M d, Y h:i A', strtotime($post->maturity_date)) : '<div class="badge badge-warning py-2 px-2">Pending</div>';

                if($post->maturity_status == 'pending'){
                    $mstatus = 'badge-warning';
                }elseif($post->maturity_status == 'matured'){
                    $mstatus = 'badge-success';
                }else{
                    $mstatus = 'badge-danger';
                }

                $nestedData['maturity_status'] = '<div class="badge '.$mstatus.' py-2 px-2">'. ucwords($post->maturity_status).'</div>';
                $nestedData['units'] = $post->units;
                $nestedData['expected_returns'] = '₦' . number_format($add, 2);
                $status = $post->status == 'active' ? 'badge-success':($post->status == 'pending' ? 'badge-warning':'badge-danger');
                if ($post->paid == 1){
                    $nestedData['status'] = '<div class="badge badge-success py-2 px-2">Paid</div>';
                }else{
                    $nestedData['status'] = '<div class="badge '. $status .' py-2 px-2">'. ucwords($post->status) .'</div>';
                }
                $nestedData['days_remaining'] = $post->maturity_date == null ? '0' : ($post->maturity_status == 'pending' ? round((($diff/24)/60)/60) : '0');
                $nestedData['rollover'] = $post->rollover ? 'Yes' : 'No';
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

    public function loadLongInvestments(Request $request)
    {
        $columns = array(
            0 =>'milestone_investments.id',
            1 =>'users.name',
            2=>'milestone_farms.title',
            3=> 'milestone_investments.amount_invested',
            4=> 'milestone_investments.maturity_status',
            5=> 'milestone_investments.units',
            6=> 'milestone_investments.status',
            7=> 'milestone_investments.status'
        );

        $totalData = MilestoneInvestment::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = MilestoneInvestment::latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  MilestoneInvestment::latest()->where('amount_invested','LIKE',"%{$search}%")
                ->orWhere('maturity_status','LIKE',"%{$search}%")
                ->orWhere('status','LIKE',"%{$search}%")
                ->orWhere('units','LIKE',"%{$search}%")
                ->orWhereHas('farm', function($q) use ($search){
                    $q->where('title','LIKE',"%{$search}%");
                })
                ->orWhereHas('user', function($q) use ($search){
                    $q->where('name','LIKE',"%{$search}%");
                })
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
                foreach($post->milestoneDates() as $key => $date){
                    if (count($post->milestoneDates()) == ($key + 1)){
                        $lastDate = $date;
                    }
                }

                $cur = strtotime(date('Y-m-d H:i:s'));
                $mat = strtotime($post->maturity_date);
                $diff = $mat - $cur;

                $interest = $post->amount_invested * (($farm->interest ?? 0) / 100);
                $add = $post->amount_invested + $interest;

                $nestedData['sn'] = $i++;
                $nestedData['name'] = '<a href="/admin/users/view/' . $post->user->id . '" target="_blank">
                                            ' . ucwords($post->user->name) . '
                                        </a>';
//                $nestedData['name'] = ucwords($post->user->name);
                $nestedData['amount'] = '₦' . number_format($post->amount_invested, 2);
                $nestedData['farm'] = ucwords($post->farm->title);
                $nestedData['milestone'] = count($post->payments) . '/' . count($post->milestoneDates());
                $nestedData['date_due'] = count($post->payments) < count($post->milestoneDates()) ? $post->milestoneDates()[count($post->payments)]->format('d M, Y h:i A') : 'Fully paid';
                if (strtotime($lastDate) < strtotime(now())){
                    $nestedData['status'] = '<span class="badge badge-success">completed</span>';
                }else{
                    $nestedData['status'] = '<span class="badge badge-warning">pending</span>';
                }
                $nestedData['action'] = '<a href="'.route('admin-long-investment.show', $post->id).'" style="white-space: nowrap" class="btn btn-success">View Investment</a>';
                $nestedData['days_remaining'] = $post->maturity_date == null ? '0' : ($post->maturity_status == 'pending' ? round((($diff/24)/60)/60) : '0');
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

    public function loadPayouts(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'name',
            2=> 'amount',
            3=> 'bank',
            4=> 'status',
            5=> 'date'
        );

        $totalData = Transaction::where('type','payouts')->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = Transaction::latest()
                ->where('type','payouts')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $posts =  Transaction::latest()
                ->where('user','LIKE',"%{$search}%")
                ->where(function($q) use ($search){
                    $q->orWhere('amount','LIKE',"%{$search}%");
                    $q->orWhere('status','LIKE',"%{$search}%");
                })
                ->orWhereHas('user', function($q) use ($search){
                    $q->where('name','LIKE',"%{$search}%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get()->filter(function($q){
                    if($q->type == 'payouts'){
                        return true;
                    }
                });

            $totalFiltered = count($posts);
        }


        $data = array();
        if(!empty($posts)) {
            $i = 1;
            foreach ($posts as $post) {
                $bankModel = Util::getBank($post->bank);
                $links = '';
                $links .= '<a href="/admin/transactions/payouts/approve/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to approve this payout?");">Approve</a>';
                $links .= '<a href="/admin/transactions/payouts/decline/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to decline this payout?");">Decline</a>';

                $bank = '';
                $status = '';

                if($bank != null){
//                    $bank .= "<big>Bank: </big>" . $bankModel->bank_name . "<br>";
//                    $bank .= '<big>Account name: </big>' . $bankModel->account_name . '<br>';
//                    $bank .= '<big>Account Number: </big>' . $bankModel->account_number . '<br>';
                    $bank .= '<button type="button" style="border: none; outline: none" onclick=\'showAccountDetails("'.$bankModel->bank_name.'", "'.$bankModel->account_name.'", "'.$bankModel->account_number.'")\' class="btn btn-success">View Bank Details</button>';
                }


                if($post->status == "pending"){
                    $status .= '<div class="badge badge-warning">Pending</div>';
                }elseif($post->status == "approved"){
                    $status .= '<div class="badge badge-success">Approved</div>';
                }elseif($post->status == "paid"){
                    $status .= '<div class="badge badge-success">Paid</div>';
                }elseif($post->status == "declined"){
                    $status .= '<div class="badge badge-danger">Declined</div>';
                }

                if ($post->type == 'payouts') {
                    $bankModel = Util::getBank($post->bank);
                    if ($bankModel != null) {
//                        $bank .= $bankModel->bank_name;
//                        $bank .= "-" . $bankModel->account_name;
//                        $bank .= "-" . $bankModel->account_number;

                        $bank .= '<button type="button" style="border: none; outline: none" onclick=\'showAccountDetails("'.$bankModel->bank_name.'", "'.$bankModel->account_name.'", "'.$bankModel->account_number.'")\' class="btn btn-success">View Bank Details</button>';
                    }
                }

                $member = Util::getUserByEmail($post->user);
                $nestedData['sn'] = $i++;
                $nestedData['name'] = ucwords($member->name ?? 'No Name');
                $nestedData['name'] = '<a href="/admin/users/view/'. $member->id . '" target="_blank">
                                            '. ucwords($member->name) .'
                                        </a>';
                $nestedData['amount'] = '₦' . number_format($post->amount, 2);
                $nestedData['type'] = $post->type;
                $nestedData['bank'] = $bank;
                $nestedData['status'] = $status;
                $nestedData['date'] = $post->created_at->format('M d, Y');
                if ($post->status == 'pending'){
                    $nestedData['action'] = '<div class="dropdown show">
                                      <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                      </a>
                                           <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                ' . $links . '
                                          </div>
                                      </div>
                                    </div>';
                }else{
                    $nestedData['action'] = '';
                }

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

    public function payoutsRequest(){
        $transactions = Transaction::where(['type'=>'payouts','status'=>'pending'])->orderBy('id', 'desc')->get();
        return view('admin.payoutRequest', ['transactions'=>$transactions]);
    }

    public function paystack()
    {
        return view('admin.paystack');
    }

    public function loadPaystack(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'amount',
            3 => 'type',
            4 => 'reference',
            5 => 'date',
        );

        $totalData = Verified::whereNotNull('user_id')->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = Verified::whereNotNull('user_id')->latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  Verified::whereNotNull('user_id')->latest()
                ->where('reference','LIKE',"%{$search}%")
                ->orWhere('amount','LIKE',"%{$search}%")
                ->orWhere('type','LIKE',"%{$search}%")
                ->orWhereHas('user', function($q) use ($search){
                    $q->where('name','LIKE',"%{$search}%");
                })
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

                $nestedData['sn'] = $i++;
                $nestedData['name'] = '<a href="/admin/users/view/'. $post->user->id . '" target="_blank">
                                            ' .ucwords($post->user->name). '
                                        </a>';;
                $nestedData['amount'] = '₦' . number_format($post->amount, 2);
                $nestedData['type'] = ucwords($post->type);
                $nestedData['reference'] = $post->reference;
                $nestedData['date'] = $post->created_at->format('M d, Y');
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

    public function wallets(){
        $wallets = Wallet::orderBy('id', 'desc')->get();
        return view('admin.wallets', ['wallets'=>$wallets]);
    }

    public function loadWallets(Request $request){
        $columns = array(0 =>'id',
            1 =>'user',
            2=> 'total_amount',
            3=> 'updated_at',
        );

        $totalData = Wallet::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = Wallet::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  Wallet::where('user','LIKE',"%{$search}%")
                            ->orWhere('total_amount', 'LIKE',"%{$search}%")
                            ->orWhereHas('user', function($q) use ($search){
                                $q->where('name','LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = count($posts);
        }


        $data = array();
        if(!empty($posts))
        {
            $i = 1;
            foreach ($posts as $post)
            {
                $member = Util::getUserByEmail($post->user);

                if(! $member){
                    Log::info($post->user);
                    continue;
                }

                //$show =  route('posts.show',$post->id);
                //$edit =  route('posts.edit',$post->id);

                $nestedData['sn'] = $i++;
                $nestedData['name'] = '<a href="/admin/users/view/'. $member->id . '" target="_blank">
                                            ' .ucwords($member->name). '
                                        </a>';
                $nestedData['amount'] = '₦'.number_format($post->total_amount,2);
                $nestedData['date_last_updated'] = date('M d, Y', strtotime($post->updated_at));
                $nestedData['action'] = '<a href="/admin/pay/wallet/'.$post->id.'" class="btn btn-success btn-rounded mb-4" target="_blank">Pay User</a>';
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

    public function payOutWallet($id){
        $wallet = Wallet::where('id', $id)->first();
        $member = Util::getUserByEmail($wallet->user);
        $transactions = Transaction::where('user', $member->email)->where(['type'=>'payouts','status'=>'pending'])->orderBy('id', 'desc')->get();
        return view('admin.walletPay', ['member'=>$member, 'wallet'=>$wallet, 'transactions' => $transactions]);
    }

    public function payOutWalletPost(Request $req){
        $member = Util::getUserByEmail($req->user);
        $wallet = Wallet::where('user', $member->email)->first();
        if($req->amount > $wallet->total_amount){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Insufficient funds!</div></div>');
        }else {
            $new = $wallet->total_amount - $req->amount;
            Wallet::where('user', $member->email)->update([
                'total_amount'=>$new,
            ]);
            Transaction::create([
                'amount'=>$req->amount,
                'type'=>'payouts',
                'user'=>$member->email,
                'user_id'=>$member->id,
                'status'=>'paid',
                'bank'=>null,
            ]);
            $title= ' ';
            $name = $member->name;
            $content = 'Your just got a payout of <strong>₦'.number_format($req->amount,2).'</strong>.<br><br> You currently have the total balance of <strong>₦'.number_format($new,2).'</strong> in your wallet.';
            $button = false;
            $button_text = '';
            $subject = "Wallet Payout ";
            Mail::to($member->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
            return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Payout made succesfully.</div></div>');
        }
    }

    public function loadAllUser(Request $request){
        $columns = array(0 =>'id',
            1 =>'name',
            2=> 'email',
            3=> 'passport',
            4 => 'id',
            5 => 'phone',
            6 => 'created_at',
        );

        $totalData = User::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = User::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  User::where('email','LIKE',"%{$search}%")
                            ->orWhere('name', 'LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = User::where('email','LIKE',"%{$search}%")
                             ->orWhere('name', 'LIKE',"%{$search}%")
                             ->count();
        }


        $data = array();
        if(!empty($posts))
        {
            $i = 1;
            foreach ($posts as $post)
            {

                $nestedData['sn'] = $i++;
                $nestedData['user'] = '<ul class="list-unstyled order-list m-b-0 m-b-0"><li class="team-member team-member-sm"><img class="rounded-circle" src="'.Util::getPassport($post) .'" alt="user" data-toggle="tooltip" title="" data-original-title="'.$post->name.'"></li></ul>';
                $nestedData['name'] = ucwords($post->name);
                $nestedData['email'] = strtolower($post->email);
                $nestedData['phone'] = ucwords($post->phone);
                if($post->email_verified_at != '')
					$nestedData['status'] = '<div class="badge badge-success">Verified</div>';
				else
					$nestedData['status'] = '<div class="badge badge-warning">Unverified</div>';
                $nestedData['date_joined'] = date('M d, Y', strtotime($post->created_at));
                $nestedData['action'] = '<div class="dropdown show">
                                          <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </a>

                                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a href="/admin/users/view/'.$post->id.'" class="dropdown-item">View User</a>
                                            <a href="/admin/users/'.$post->id.'/investments" class="dropdown-item">View User Investment</a>
                                            <a href="/admin/users/'.$post->id.'/wallets" class="dropdown-item">View User Wallet</a>
                                            <a href="/admin/users/'.$post->id.'/transactions" class="dropdown-item">View User Transactions</a>
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

    public function loadVerifiedUser(Request $request){
        $columns = array(0 =>'id',
            1 =>'name',
            2=> 'email',
            3=> 'passport',
            4 => 'id',
            5 => 'phone',
            6 => 'created_at',
        );

        $totalData = User::where('email_verified_at', '!=', null)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = User::where('email_verified_at', '!=', null)->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  User::where([['email','LIKE',"%{$search}%"],['email_verified_at', '!=', null]])
                            ->orWhere([['name', 'LIKE',"%{$search}%"],['email_verified_at', '!=', null]])
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = User::where([['email','LIKE',"%{$search}%"],['email_verified_at', '!=', null]])
                             ->orWhere([['name', 'LIKE',"%{$search}%"],['email_verified_at', '!=', null]])
                             ->count();
        }


        $data = array();
        if(!empty($posts))
        {
            $i = 1;
            foreach ($posts as $post)
            {

                $nestedData['sn'] = $i++;
                $nestedData['user'] = '<ul class="list-unstyled order-list m-b-0 m-b-0"><li class="team-member team-member-sm"><img class="rounded-circle" src="'.Util::getPassport($post) .'" alt="user" data-toggle="tooltip" title="" data-original-title="'.$post->name.'"></li></ul>';
                $nestedData['name'] = ucwords($post->name);
                $nestedData['email'] = strtolower($post->email);
                $nestedData['phone'] = ucwords($post->phone);
                if($post->email_verified_at != '')
					$nestedData['status'] = '<div class="badge badge-success">Verified</div>';
				else
					$nestedData['status'] = '<div class="badge badge-warning">Unverified</div>';
                $nestedData['date_joined'] = date('M d, Y', strtotime($post->created_at));

                $nestedData['action'] = '<div class="dropdown show">
                                          <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </a>

                                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a href="/admin/users/view/'.$post->id.'" class="dropdown-item">View</a>
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

    public function loadUnverifiedUser(Request $request){


        $columns = array(0 =>'id',
            1 =>'name',
            2=> 'email',
            3=> 'passport',
            4 => 'id',
            5 => 'phone',
            6 => 'created_at',
        );

        $totalData = User::where('email_verified_at', null)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = User::where('email_verified_at',null)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  User::where([['email','LIKE',"%{$search}%"],['email_verified_at', null]])
                ->orWhere([['name', 'LIKE',"%{$search}%"],['email_verified_at', null]])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = count($posts);
        }

        $data = array();
        if(!empty($posts))
        {
            $i = 1;
            foreach ($posts as $post)
            {

                $nestedData['sn'] = $i++;
                $nestedData['user'] = '<ul class="list-unstyled order-list m-b-0 m-b-0"><li class="team-member team-member-sm"><img class="rounded-circle" src="'.Util::getPassport($post) .'" alt="user" data-toggle="tooltip" title="" data-original-title="'.$post->name.'"></li></ul>';
                $nestedData['name'] = ucwords($post->name);
                $nestedData['email'] = strtolower($post->email);
                $nestedData['phone'] = ucwords($post->phone);
                if($post->email_verified_at != '')
					$nestedData['status'] = '<div class="badge badge-success">Verified</div>';
				else
					$nestedData['status'] = '<div class="badge badge-warning">Unverified</div>';
                $nestedData['date_joined'] = date('M d, Y', strtotime($post->created_at));
                $nestedData['action'] = '<div class="dropdown show">
                                          <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </a>

                                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a href="/admin/users/view/'.$post->id.'" class="dropdown-item">View</a>
                                            <a href="/admin/users/delete/'.$post->id.'" class="dropdown-item" onclick="return confirm(\'Are you sure you want to delete this user?\');">Delete</a>
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

    public function deleteUser($id){
        $user = User::where('id', $id)->first();
        Wallet::where('user', $user->email)->delete();
        Transaction::where('user', $user->email)->delete();
        Bank::where('user', $user->email)->delete();
        Investment::where('user', $user->email)->delete();
        User::where('email', $user->email)->delete();
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>User deleted succesfully.</div></div>');
    }

    public function profile(){
        $user = Util::getAdmin();
        return view('admin.profile', ['user'=>$user]);
    }

    public function profilePost(Request $req){
        Admin::where('id', $req->id)->update([
            'name'=>$req->name,
        ]);
        if ($files = $req->file('passport')) {
           $destinationPath = 'assets/uploads/passports'; // upload path
           $profileImage = date('YmdHis') . "_admin." . $files->getClientOriginalExtension();
           $files->move($destinationPath, $profileImage);
           Admin::where('id', $req->id)->update([
                'passport'=>$destinationPath."/".$profileImage,
            ]);
        }
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your profile has been updated.</div></div>');
    }

    public function deposits(){
//        $deposits = Transaction::where(['type'=>'deposits'])->latest('id')->get();

        return view('admin.deposits');
    }

    public function loadDeposits(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'user',
            2=> 'amount',
            3=> 'type',
            9=> 'status',
        );

        $totalData = Transaction::where(['type'=>'deposits'])->count();
        $totalData += Verified::where(['type'=>'deposits'])->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $transactions = Transaction::where(['type'=>'deposits'])->orWhere('type', 'booking')->latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $paystacks = Verified::where(['type'=>'deposits'])->orWhere('type', 'booking')->latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $posts = collect();

            foreach ($transactions as $deposit){
                $posts->push($deposit);
            }

            foreach ($paystacks as $deposit){
                $posts->push($deposit);
            }


        }
        else {
            $search = $request->input('search.value');

            $transactions =  Transaction::latest()
                ->where('user','LIKE',"%{$search}%")
                ->where(function($q) use ($search){
                    $q->orWhere('amount','LIKE',"%{$search}%");
                    $q->orWhere('status','LIKE',"%{$search}%");
                })
                ->orWhereHas('user', function($q) use ($search){
                    $q->where('name','LIKE',"%{$search}%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get()->filter(function($q){
                    if($q->type == 'deposits'){
                        return true;
                    }
                });

            $paystacks =  Verified::latest()
                ->where(function($q) use ($search){
                    $q->orWhere('amount','LIKE',"%{$search}%");
                })
                ->orWhereHas('user', function($q) use ($search){
                    $q->where('name','LIKE',"%{$search}%");
                    $q->orWhere('email','LIKE',"%{$search}%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get()->filter(function($q){
                    if($q->type == 'deposits'){
                        return true;
                    }
                });

            $posts = collect();

            foreach ($transactions as $deposit){
                $posts->push($deposit);
            }

            foreach ($paystacks as $deposit){
                $posts->push($deposit);
            }

            $totalFiltered = count($posts);
        }


        $data = array();

        if(!empty($posts)) {
            $i = 1;
            foreach ($posts as $post) {

                $status = '';
                $method = 'Bank';
                if(get_class($post) == Verified::class){
                    $status .= '<div class="badge badge-success">Approved</div>';
                    $method = 'Paystack';
                    $member = $post->user;

                }else{
                    if($post->status == "pending"){
                        $status .= '<div class="badge badge-warning">Pending</div>';
                    }elseif($post->status == "approved"){
                        $status .= '<div class="badge badge-success">Approved</div>';
                    }elseif($post->status == "paid"){
                        $status .= '<div class="badge badge-success">Paid</div>';
                    }elseif($post->status == "declined"){
                        $status .= '<div class="badge badge-danger">Declined</div>';
                    }
                    $member = Util::getUserByEmail($post->user);
                }

                $links = '';
                if ($post->type == 'deposits' && $post->status == 'pending' && get_class($post) != Verified::class) {
                    $links .= '<a href="/admin/transactions/deposits/approve/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to approve this deposits?");">Approve</a>';
                    $links .= '<a href="/admin/transactions/deposits/decline/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to decline this deposits?");">Decline</a>';
                }

                $nestedData['sn'] = $i++;
                $nestedData['name'] =  '<a href="/admin/users/view/'. $member->id . '" target="_blank">
                                            ' .ucwords($member->name). '
                                        </a>';
                $nestedData['amount'] = '₦' . number_format($post->amount, 2);
                $nestedData['type'] = $post->type == 'booking' ? 'Booking/Deposit' : ucwords($post->type);
                $nestedData['method'] = $method;
                $nestedData['status'] = $status;
                $nestedData['date'] = $post->created_at->format('M d, Y');
                if ($post->type == 'deposits' && $post->status == 'pending' && get_class($post) != Verified::class){
                    $nestedData['action'] = '<div class="dropdown show">
                                      <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                      </a>
                                           <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                ' . $links . '
                                          </div>
                                      </div>
                                    </div>';
                }else{
                    $nestedData['action'] = '';
                }

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

//    public function loadTransactionInvestments(Request $request)
//    {
//        $columns = array(
//            0 =>'id',
//            1 =>'user',
//            2=> 'amount',
//            3=> 'type',
//            9=> 'status',
//        );
//
//        $totalData = Transaction::where(['type'=>'investments'])->count();
//
//        $totalFiltered = $totalData;
//
//        $limit = $request->input('length');
//        $start = $request->input('start');
//        $order = $columns[$request->input('order.0.column')];
//        $dir = $request->input('order.0.dir');
//
//        if(empty($request->input('search.value')))
//        {
//            $posts = Transaction::where(['type'=>'investments'])->latest()->offset($start)
//                ->limit($limit)
//                ->orderBy($order,$dir)
//                ->get();
//
//
//        }
//        else {
//            $search = $request->input('search.value');
//
//            $posts =  Transaction::latest()
//                ->where('user','LIKE',"%{$search}%")
//                ->where(function($q) use ($search){
//                    $q->orWhere('amount','LIKE',"%{$search}%");
//                    $q->orWhere('status','LIKE',"%{$search}%");
//                })
//                ->orWhereHas('user', function($q) use ($search){
//                    $q->where('name','LIKE',"%{$search}%");
//                })
//                ->offset($start)
//                ->limit($limit)
//                ->orderBy($order,$dir)
//                ->get()->filter(function($q){
//                    if($q->type == 'investments'){
//                        return true;
//                    }
//                });
//
//            $totalFiltered = count($posts);
//        }
//
//
//        $data = array();
//
//        if(!empty($posts)) {
//            $i = 1;
//            foreach ($posts as $post) {
//
//                $status = '';
//                $method = 'Bank';
//                if(get_class($post) == Verified::class){
//                    $status .= '<div class="badge badge-success">Approved</div>';
//                    $method = 'Paystack';
//                    $member = $post->user;
//
//                }else{
//                    if($post->status == "pending"){
//                        $status .= '<div class="badge badge-warning">Pending</div>';
//                    }elseif($post->status == "approved"){
//                        $status .= '<div class="badge badge-success">Approved</div>';
//                    }elseif($post->status == "paid"){
//                        $status .= '<div class="badge badge-success">Paid</div>';
//                    }elseif($post->status == "declined"){
//                        $status .= '<div class="badge badge-danger">Declined</div>';
//                    }
//                    $member = Util::getUserByEmail($post->user);
//                }
//
//                $links = '';
//                if ($post->type == 'deposits' && $post->status == 'pending' && get_class($post) != Verified::class) {
//                    $links .= '<a href="/admin/transactions/deposits/approve/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to approve this deposits?");">Approve</a>';
//                    $links .= '<a href="/admin/transactions/deposits/decline/' . $post->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to decline this deposits?");">Decline</a>';
//                }
//
//                $nestedData['sn'] = $i++;
//                $nestedData['name'] =  '<a href="/admin/users/view/'. $member->id . '" target="_blank">
//                                            ' .ucwords($member->name). '
//                                        </a>';
//                $nestedData['amount'] = '₦' . number_format($post->amount, 2);
//                $nestedData['type'] = ucwords($post->type);
//                $nestedData['status'] = $status;
//                $nestedData['date'] = $post->created_at->format('M d, Y');
//
//                $data[] = $nestedData;
//            }
//        }
//
//        $json_data = array(
//            "draw"            => intval($request->input('draw')),
//            "recordsTotal"    => intval($totalData),
//            "recordsFiltered" => intval($totalFiltered),
//            "data"            => $data
//        );
//
//        echo json_encode($json_data);
//    }
    public function loadTransactionInvestments(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'name',
            2=> 'amount',
            5=> 'maturity_status',
            6=> 'units',
            9=> 'status',
        );

        $totalData = MilestoneInvestment::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = MilestoneInvestment::latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  MilestoneInvestment::latest()->where('user','LIKE',"%{$search}%")
                ->orWhere('amount_invested','LIKE',"%{$search}%")
                ->orWhere('maturity_status','LIKE',"%{$search}%")
                ->orWhere('status','LIKE',"%{$search}%")
                ->orWhere('units','LIKE',"%{$search}%")
                ->orWhereHas('farm', function($q) use ($search){
                    $q->where('title','LIKE',"%{$search}%");
                })
                ->orWhereHas('user', function($q) use ($search){
                    $q->where('name','LIKE',"%{$search}%");
                })
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

                $cur = strtotime(date('Y-m-d H:i:s'));
                $mat = strtotime($post->maturity_date);
                $diff = $mat - $cur;

                $interest = $post->amount_invested*(($farm->interest ?? 0)/100);
                $add = $post->amount_invested+$interest;

                $nestedData['name'] = '<a href="/admin/users/view/'. $post->user->id . '" target="_blank">
                                            ' .ucwords($post->user->name). '
                                        </a>';
//                $nestedData['name'] = ucwords($post->user->name);
//                $nestedData['amount'] = '₦' . number_format($post->amount_invested, 2);
                $nestedData['farm'] = ucwords($post->farm->title);
                $nestedData['milestone'] = count($post->payments).'/'.count($post->milestoneDates());
                $nestedData['date_due'] = count($post->payments) < count($post->milestoneDates()) ? $post->milestoneDates()[count($post->payments)] : 'Fully paid';

//                $nestedData['action'] = '<a href="'.route('admin-long-investment.show', $post->id).'" class="btn btn-success">View Investment</a>';

                $nestedData['days_remaining'] = $post->maturity_date == null ? '0' : ($post->maturity_status == 'pending' ? round((($diff/24)/60)/60) : '0');
                $lastDate = null;
                $nextDate = null;
                foreach ($post->milestoneDates() as $key => $date){
                    if ($nextDate == null && $date->gt(now())){
                        $nextDate = $date;
                    }

                    if (count($post->milestoneDates()) == ($key + 1)){
                        $nestedData['amount'] = 'NGN'.number_format(implode("", explode(',',$post->amount_invested)) + (implode("", explode(',',$post->getMilestoneReturn($key)))) ,2);
//                        $nestedData['amount'] = 'NGN'.number_format(implode("", explode(',',$post->amount_invested)) + (implode("", explode(',',$post->milestoneReturns())) / count($post->milestoneDates())) ,2);
                    }else{
                        $nestedData['amount'] = 'NGN'.number_format(implode("", explode(',',$post->getMilestoneReturn($key))),2);
//                        $nestedData['amount'] = 'NGN'.number_format(implode("", explode(',',$post->milestoneReturns())) / count($post->milestoneDates()),2);
                    }

                    if(!$date->gt(now())){
                        if(!$post->payments()->where('milestone', $key+1)->first()){
                            if ($key == 0){
                                $nestedData['action'] = '<a href="/investments/payout/approveNow/'.$post->id.'" class="btn btn-success" onclick="confirm(\'Are you sure you want to pay this milestone?\');">Pay Milestone</a>';
                            }else{
                                if ($post->payments()->where('milestone', $key)->first()){
                                        $nestedData['action'] = '<a href="/investments/payout/approveNow/'.$post->id.'" class="btn btn-success" onclick="confirm(\'Are you sure you want to pay this milestone?\');">Pay Milestone</a>';
                                }else{
                                        $nestedData['action'] = '<a href="#" class="btn btn-success disabled">Pay Milestone</a>';
                                }
                            }
                        }else{
                            $nestedData['action'] = '<a href="'.route('admin-long-investment.show', $post->id).'" class="btn btn-success">View Investment</a>';
                        }
                    }else{
                        $nestedData['action'] = '<a href="'.route('admin-long-investment.show', $post->id).'" class="btn btn-success">View Investment</a>';
                    }

                    if (!$date->gt(now()) && $nestedData['date_due'] != 'Fully paid' && (strtotime($nestedData['date_due']) < strtotime(now()))){
                        $nestedData['sn'] = $i++;
                        $nestedData['date_due'] = date('d M, Y h:i A', strtotime($nestedData['date_due']));
                        $data[] = $nestedData;
                    }
                }
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

    public function referrals(){
        $referrals = Transaction::where(['type'=>'referral'])->latest('id')->get();

        return view('admin.referrals', ['referrals'=>$referrals]);
    }

    public function investments(){
        $investments = Transaction::where(['type'=>'investments'])->latest('id')->get();

        return view('admin.investments', ['investments'=>$investments]);
    }

    public function news(){

        $news = NewsUpdate::orderBy('id', 'desc')->get();

        return view('admin.news', ['news'=>$news]);
    }

    public function addNews(){
        $farmlists = FarmList::orderBy('id', 'desc')->get();
        return view('admin.addNews', ['farmlists'=>$farmlists]);
    }

    public function addNewsPost(Request $req){
        NewsUpdate::create([
           'farmlist'=>$req->farmlist,
           'title'=>$req->title,
           'content'=>request('content'),
        ]);
        return redirect('/admin/news')->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>A new update has been added successfully.</div></div>');
    }

    public function editNewsPost(Request $req){
        NewsUpdate::where('id', $req->id)->update([
           'farmlist'=>$req->farmlist,
           'title'=>$req->title,
           'content'=> request('content')
        ]);
        return redirect('/admin/news')->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your update was successful.</div></div>');
    }

    public function editNews($slug){
        $new = NewsUpdate::where('slug', $slug)->first();
        $farmlists = FarmList::orderBy('id', 'desc')->get();
        return view('admin.addNews', ['farmlists'=>$farmlists, 'new'=>$new, 'edit'=>true]);
    }

    public function deleteNews($slug){
        NewsUpdate::where('slug', $slug)->delete();
        return redirect('/admin/news')->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You deleted an updates.</div></div>');
    }

    public function logout () {
        //logout user
        auth()->guard("admin")->logout();
        // redirect to homepage
        return redirect('/admin');
    }

    public function newsletter()
    {
        return view('admin.newsletter');
    }

    public function newsletterSend(Request $request)
    {

        $this->validate($request,[
           'subject' => 'required',
           'message' => 'required'
        ]);

        if($request->has('users')){
            $this->sendNewsletter($request->message, $request->subject);
            return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Newsletter sent to all users.</div></div>');

        }

        if($request->has('emails') && $request->emails != null){
            $this->sendNewsletter($request->message, $request->subject, explode(',', $request->emails));
            return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Newsletter sent to all specified emails.</div></div>');

        }

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Select all users or provide emails.</div></div>');

    }

    private function sendNewsletter($message, $subject, $emailLists = NULL)
    {

        if($emailLists == NULL){
            // User::all()->each(function($user) use($subject, $message){
            //     $message = str_ireplace('[name]', $user->name, $message);
            //     Mail::to($user->email)->send(new NewsletterMailable($subject, $message));
            // });

            // return true;

            User::chunk(20, function($users) use($subject, $message) {

                foreach($users as $user){
                    $message = str_ireplace('[name]', $user->name, $message);
                    Mail::to($user->email)->send(new NewsletterMailable($subject, $message));
                }

            });

            return true;
        }

        foreach($emailLists as $email){
            Mail::to(trim($email))->send(new NewsletterMailable($subject, $message));
        }
    }

    public function payouts(){
        return view('admin.payouts');
    }

    public function loadPayout(Request $request)
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
                $action = '';

                if($post->isOpen()){
                    $status .= '<div class="badge badge-success">Open</div>';
                }elseif($post->isClosed()){
                    $action =  '<div class="dropdown show">
                                            <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a href="/admin/payouts/'. $post->slug .'" class="dropdown-item">View</a>
                                            </div>
                                        </div>';
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
                $nestedData['actions'] =  $action;


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

    public function showFarmPayout($slug)
    {
        if(! FarmList::where('slug', $slug)->exists()){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Farmlist does not exist.</div></div>');
        }

        return view('admin.viewPayout', ['farmlist' => FarmList::where('slug', $slug)->first()]);
    }

    public function downloadTransactions($type)
    {

        $types = ['all','investments','deposits', 'referrals', 'payouts', 'payoutRequests', 'users','verified','unverified','paystack','milestone-investments'];

        if(! in_array($type, $types)){
            request()->session()->flash('error', 'Something went wrong!');
            return back();
        }

        switch($type){
            case 'all':
                return $this->downloadExcel(new AllTransactionsExport,'All Transactions.xlsx' );
                break;

            case 'investments':
                return $this->downloadExcel(new InvestmentTransactionsExport,'Investment Transactions.xlsx' );
                break;

            case 'milestone-investments':
                return $this->downloadExcel(new MilestoneInvestmentExport,'Longterm Investment Transactions.xlsx' );
                break;

            case 'deposits':
                return $this->downloadExcel(new DepositTransactionsExport,'Deposit Transactions.xlsx' );
                break;

            case 'referrals':
                return $this->downloadExcel(new ReferralTransactionsExport,'Referral Transactions.xlsx' );
                break;

            case 'payouts':
                return $this->downloadExcel(new PayoutTransactionsExport,'Payout Transactions.xlsx' );
                break;

            case 'payoutRequests':
                return $this->downloadExcel(new PayoutRequestTransactionsExport,'Payout Request Transactions.xlsx' );
                break;

            case 'users':
                return $this->downloadExcel(new AllUsersExport,'All Users List.xlsx' );
                break;

            case 'verified':
                return $this->downloadExcel(new VerifiedUsersExport,'Verified Users List.xlsx' );
                break;

            case 'unverified':
                return $this->downloadExcel(new UnverifiedUsersExport,'Unverified Users List.xlsx' );
                break;

            case 'paystack':
                return $this->downloadExcel(new PaystackTransactionsExport,'Paystack List.xlsx' );
                break;

            default:
                break;
        }


    }

    protected function downloadExcel($data, $name = 'Transactions.xlsx')
    {
        return Excel::download($data, $name);
    }

    public function withdrawForUser(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        switch($request->location){

            case 'wallet':

                if($user->wallet->total_amount < $request->amount){
                    return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Not sufficient funds in account.</div></div>');
                }

                if($request->amount == 0){
                    return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Amount must be greater than 0.</div></div>');
                }

                $user->wallet()->decrement('total_amount', $request->amount);

                $type = 'Wallet';
                $balance = $user->wallet->total_amount;
                break;

            case 'bank':

                if($user->emeraldbank->total_amount < $request->amount){
                    return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Not sufficient funds in account.</div></div>');
                }

                if($request->amount == 0){
                    return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Amount must be greater than 0.</div></div>');
                }

                $user->emeraldbank()->decrement('total_amount', $request->amount);

                $type = 'Emerald Bank';
                $balance = $user->emeraldbank->total_amount;
                break;
        }

        Transaction::create([
            'amount'=>$request->amount,
            'type'=>'payouts',
            'user'=> $user->email,
            'status'=>'approved',
            'user_id' => $user->id
        ]);


        $user->notify(new CustomNotification('Your payout of <strong>₦'.number_format($request->amount,2).'</strong> has been approved.<br><br>Wallet summary:<br>Payout amount: <strong>₦'.number_format($request->amount,2).'</strong><br>New wallet balance: <strong>₦'.number_format($user->wallet->fresh()->total_amount,2).'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-chart-pie"></i></span>', 'Payout Created'));

        $title= ' ';
        $name = $user->name;
        $content = 'Your payout of <strong>₦'.number_format($request->amount,2).'</strong> has been approved.<br><br>Wallet summary:<br>Payout amount: <strong>₦'.number_format($request->amount,2).'</strong><br>New wallet balance: <strong>₦'.number_format($user->wallet->fresh()->total_amount,2).'</strong><br><br>Thank you for choosing Emerald Farms.';
        $button = false;
        $button_text = '';
        $subject = "Payout Created";
        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Withdrawal Successful.</div></div>');
    }

    public function depositForUser(Request $request)
    {

        $user = User::findOrFail($request->user_id);

        switch($request->location){

            case 'wallet':
                $user->wallet()->increment('total_amount', $request->amount);
                $balance = $user->wallet->fresh()->total_amount;
                break;

            case 'bank':
                $user->emeraldbank()->increment('total_amount', $request->amount);
                $balance = $user->emeraldbank->fresh()->total_amount;
                break;
        }

        $transaction = Transaction::create([
            'amount'=>$request->amount,
            'type'=>'deposits',
            'user'=> $user->email,
            'status'=>'approved',
            'user_id' => $user->id
        ]);

        $user->notify(new CustomNotification('Your deposit of <strong>₦'.number_format($transaction->amount,2).'</strong> has been approved successfully.<br><br> Wallet summary:<br> Deposit amount: <strong>₦'.number_format($transaction->amount,2).'</strong><br> New wallet balance:  <strong>₦'.number_format($balance,2).'</strong>', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-chart-pie"></i></span>', 'Deposit Created'));

        $title= ' ';
        $name = $user->name;
        $content = 'Your deposit of <strong>₦'.number_format($transaction->amount,2).'</strong> has been approved successfully.<br><br> Wallet summary:<br> Deposit amount: <strong>₦'.number_format($transaction->amount,2).'</strong><br> New wallet balance:  <strong>₦'.number_format($balance,2).'</strong> <br><br>Thank you for choosing Emerald Farms.';
//        $content = 'Dear '.Str::ucfirst($user->name).',<br><br> Your deposit of N'.number_format($request->amount,2).' has been queued and pending system approval.<br><br> We will update the status of your transaction soon.';
        $button = false;
        $button_text = '';
        $subject = "Deposit Created";
        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Deposit Successful.</div></div>');

    }

    public function transferForUser(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        if($request->from == $request->to){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You cannot make transfer to the same wallet</div></div>');
        }

        switch($request->from){

            case 'wallet':
                $from = $user->wallet;
                $fromName = "Wallet";
                break;

            case 'bank':
                $from = $user->emeraldbank;
                $fromName = "Emerald Bank";
                break;

        }

        if($request->from  == 'bank'){
            $withdrawableAmount = $user->emeraldbank->total_amount - $user->bookings()->where('status','approved')->sum('amount');

            if($request->amount > $withdrawableAmount){
                return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You do not have sufficient funds in the account you want to make transfer from or fundsa are tied to investments.</div></div>');
            }
        }

        if($request->amount > $from->total_amount){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You do not have sufficient funds in the account you want to make transfer from.</div></div>');
        }

        switch($request->to){

            case 'wallet':
                $to = $user->wallet;
                $toName = "Wallet";

                break;

            case 'bank':
                $to = $user->emeraldbank;
                $toName = "Emerald Bank";

                break;

        }

        $from->decrement('total_amount', $request->amount);
        $to->increment('total_amount', $request->amount);

        Transaction::create([
            'amount'=>$request->amount,
            'type'=>'transfer',
            'user'=>$user->email,
            'status'=>'approved',
            'user_id' => $user->id
        ]);


        $title= ' ';
        $name = auth()->user()->name;
        $content = "Your interaccount transfer of <strong>₦ ". number_format($request->amount) ."</strong> from <strong>{$fromName}</strong> to <strong>{$toName}</strong> was successful.";
        $button = false;
        $button_text = '';
        $subject = "Interaccount Transfer";
        Mail::to(auth()->user()->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your interaccount funds transfer was successful.</div></div>');
    }

    public function investLongForUser(Request $req)
    {
        if($req->farm_id == 'null'){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Choose Farm first.</div></div>');
        }

        $user = User::findOrFail($req->user_id);
        $farmlist = MilestoneFarm::findOrFail($req->farm_id);
        $wallet = Wallet::where('user', $user->email)->latest()->first();
        if($req->unit < 1){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your unit should not be lower than 1.</div></div>');
        }

        // if($req->unit > 250){
        //     return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You can not purchase more than 250 units, kindly reduce your number of units.</div></div>');
        // }

        if($req->unit > $farmlist->available_units){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your unit is higher than the available units.</div></div>');
        }

        $total_amount_returns = $req->unit * $farmlist->price;

        if($wallet->total_amount < $total_amount_returns){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> Insufficient funds</div></div>');
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

            $user->notify(new CustomNotification('Your Long-term investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$units.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong><br>Duration: <strong>'.$investment->getPaymentDurationInDays().'days</strong><br>Investment Milestones: <strong>'.$investment->farm->milestone.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format($user->wallet->fresh()->total_amount, 2) .'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Long-term Investment Created'));

            $title= ' ';
            $name = $user->name;
            $content = 'Your Long-term investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$units.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong><br>Duration: <strong>'.$investment->getPaymentDurationInDays().'days</strong><br>Investment Milestones: <strong>'.$investment->farm->milestone.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format($user->wallet->fresh()->total_amount, 2) .'</strong><br><br>Thank you for choosing Emerald Farms.';
            $button = false;
            $button_text = '';
            $subject = "Long-term Investment Created";
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

            $title = ' ';
            $name = $user->name;
            $content = 'Thank you for your investment in our <strong>'.$farmlist->title.'</strong> Farm Project. <br><br>Together we can rewrite history and make memories.<br><br> Updates will be posted on the website weekly. You can request to visit the farm; you only have to schedule a visitation date ahead. Please find attached your certificate of investment.<br><br> Once again, Thank you';
            $button = false;
            $button_text = '';
            $button_link = '';
            $subject = "Investment Certificate";
            \Illuminate\Support\Facades\Mail::to($user->email)->send((new SendMailable($title,$name,$content,$button,$button_text,$subject,$button_link))->attachData($pdf->output(), "certificate.pdf"));

            return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your investment has been approved.</div></div>');
        }
    }

    public function investForUser(Request $request)
    {

        if($request->farm_id == 'null'){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Choose Farm first.</div></div>');
        }

       $farmlist = FarmList::findOrFail($request->farm_id);

       $user = User::findOrFail($request->user_id);

        if($request->unit < 1){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Unit to sponsor should not be lower than 1.</div></div>');
        } else {
            $total_amount_returns = $request->unit * $farmlist->price;
            if($user->wallet->total_amount < $total_amount_returns){
                return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> Please fund user account first</div></div>');
            }else {

                $user->wallet()->decrement('total_amount', $total_amount_returns);

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
                    'amount'=>$total_amount_returns,
                    'type'=>'investments',
                    'user'=>$user->email,
                    'status'=>'approved',
                    'user_id' => $user->id
                ]);

                $farmlist->decrement('available_units', $request->unit );

                $user->notify(new CustomNotification('Your investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$request->unit.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format($user->wallet->fresh()->total_amount, 2) .'</strong>.', '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-tag"></i></span>', 'Investment Created'));

                $title= ' ';
                $name = $user->name;
                $content = 'Your investment of <strong>₦'. number_format($total_amount_returns,2).'</strong> has been created successfully. <br><br>Investment Overview: <br>Amount Invested: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>Units Purchased: <strong>'.$request->unit.'</strong><br>Farm: <strong>'.$farmlist->title.'</strong>.<br><br>Wallet summary:<br>Payout amount: <strong>₦'. number_format($total_amount_returns,2).'</strong><br>New wallet balance: <strong>₦'. number_format($user->wallet->fresh()->total_amount, 2) .'</strong><br><br>Thank you for choosing Emerald Farms.';
                $button = false;
                $button_text = '';
                $subject = "Investment Created";
                Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

                return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your investment has been approved.</div></div>');
            }
        }

    }

    public function admins()
    {
        return view('admin.admins');
    }

    public function loadAllAdmin(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'name',
            2=> 'email',
            6 => 'created_at',
        );

        $totalData = Admin::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = Admin::offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $posts =  Admin::where('email','LIKE',"%{$search}%")
                ->orWhere('name', 'LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = count($posts);
        }


        $data = array();
        if(!empty($posts))
        {
            $i = 1;
            foreach ($posts as $post)
            {

                $nestedData['sn'] = $i++;
                $nestedData['name'] = ucwords($post->name);
                $nestedData['email'] = strtolower($post->email);
                $nestedData['role'] = Admin::$roles[$post->role];

                $nestedData['action'] = '<div class="dropdown show">
                                          <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Change Roles
                                                    </a>

                                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a href="/admin/roles/'. $post->id .'/super" class="dropdown-item">Make Super Admin</a>
                                                <a href="/admin/roles/'. $post->id .'/manager" class="dropdown-item">Make Manager Admin</a>
                                                <a href="/admin/roles/'. $post->id .'/staff" class="dropdown-item">Make Staff Admin</a>
                                                <a href="/admin/admins/'. $post->id .'/delete" class="dropdown-item">Delete Admin</a>
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

    public function makeSuperAdmin(Admin $admin)
    {

        if($admin->role == 3){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> User already super admin.</div></div>');
        }

        $admin->update(['role' => 3]);

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> User made super admin.</div></div>');

    }

    public function makeManagerAdmin(Admin $admin)
    {

        if($admin->role == 2){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> User already manager admin.</div></div>');
        }

        $admin->update(['role' => 2]);

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> User made manager admin.</div></div>');
    }

    public function makeStaffAdmin(Admin $admin)
    {

        if($admin->role == 1){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> User already staff admin.</div></div>');
        }

        $admin->update(['role' => 1]);

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button> User made staff admin.</div></div>');
    }

    public function adminsCreate()
    {
        return view('admin.createAdmin');
    }

    public function adminsCreatePost(Request $request)
    {
        $validatedData = $this->validate($request,[
           'name' => 'required | string',
           'password' => 'required | min:8 | confirmed',
           'email' =>  'required | unique:admins',
            'role' => 'required | string'
        ]);

        Admin::create([
            'name' => $validatedData['name'],
            'password' => \Hash::make($validatedData['password']),
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('admin.admin.users')->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Admin Created.</div></div>');
    }

    public function deleteAdmin(Admin $admin)
    {
        if($admin->id == auth()->guard('admin')->id()){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You cannot delete yourself.</div></div>');
        }

        $admin->delete();
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Admin Deleted.</div></div>');
    }

    public function showUserInvestments(User $user){
        return view('admin.userInvestments', ['investments' => Investment::whereUser($user->email)->get()]);
    }

    public function showUserLongInvestments(User $user){
        return view('admin.userLongInvestments', ['investments' => MilestoneInvestment::where('user_id', $user->id)->get()]);
    }

    public function showUserWallet(User $user){
        return view('admin.wallet', ['user' => $user]);
    }

    public function showUserTransaction(User $user)
    {
        return view('admin.userTransactions',['transactions' => Transaction::whereUserId($user->id)->get()]);
    }

    public function showLongInvestments($id)
    {
        return view('admin.long-payout.show',['investment' => MilestoneInvestment::findOrFail($id)]);
    }

    public function payMilestone($id){
        $investment = MilestoneInvestment::findOrFail($id);
        $milestones = $investment->milestoneDates();
        $paid = $investment->payments;
        $amount = 0;
        if(count($milestones) == (count($paid) + 1)){
            $completed = true;
            $amount += (int)implode("", explode(',',$investment->amount_invested)) + (int)(implode("", explode(',',$investment->getMilestoneReturn(count($paid)))));
//            $amount += implode("", explode(',',$investment->amount_invested)) + (implode("", explode(',',$investment->milestoneReturns())) / count($investment->milestoneDates()));
        }else {
            $completed = false;
            $amount += (int)implode("", explode(',',$investment->getMilestoneReturn(count($paid))));
//            $amount += implode("", explode(',',$investment->milestoneReturns())) / count($investment->milestoneDates());
        }
        $data = [
            'amount'=>$amount,
            'milestone'=>count($paid)+1,
        ];
        $investment->payments()->save(new PaidMileStone($data));
        $data2 = [
            'amount'=>$amount,
            'type'=>'Milestone Payout',
            'user'=>$investment->user->email,
            'user_id'=>$investment->user->id,
            'status'=>'approved',
        ];
        $investment->paid = $completed ? 1 : 0;
        $investment->update();
        Transaction::create($data2);
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Payment made successfully.</div></div>');
    }
}
