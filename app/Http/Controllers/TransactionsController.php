<?php

namespace App\Http\Controllers;

use App\Exports\User\AllTransactionsExport;
use App\Exports\User\DepositTransactionsExport;
use App\Exports\User\InvestmentTransactionsExport;
use App\Exports\User\PayoutTransactionsExport;
use App\Http\Controllers\Globals as Util;
use App\Investment;
use App\Mail\SendMailable;
use App\MilestoneInvestment;
use App\Notifications\CustomNotification;
use App\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;

class TransactionsController extends Controller
{

    public function index()
    {

        if(! auth()->user()->hasCompletedProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        }

        if(! auth()->user()->hasCompletedKinProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        }

        return view('user.transactions', ['transactions' => Transaction::where('user', auth()->user()->email)->latest()->get()]);
    }

    public function investments($type)
    {

        switch($type){
            case 'long':
                $investments = MilestoneInvestment::whereUserId(auth()->id())->get();
                $view ='user.investment.longInvestShow';
                break;
            case 'short':
                $investments = Investment::where('user', auth()->user()->email)->latest()->get()->filter(function($investment){
                    if(Util::getFarmlist($investment->farmlist)){
                        return true;
                    }
                });
                $view ='user.investments' ;
                break;
            default:
                break;
        }



        if(! auth()->user()->hasCompletedProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        }

        if(! auth()->user()->hasCompletedKinProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        }

        return view($view, ['investments'=>$investments]);
    }

    public function payouts()
    {

        if(! auth()->user()->hasCompletedProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        }

        if(! auth()->user()->hasCompletedKinProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        }

        return view('user.payouts', ['payouts' => Transaction::where(['user' => auth()->user()->email, 'type'=>'payouts'])->latest()->get()]);
    }

    public function deposits()
    {

        if(! auth()->user()->hasCompletedProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        }

        if(! auth()->user()->hasCompletedKinProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        }

        $allDeposits = collect();
        // $deposits = Transaction::where(['user' => auth()->user()->email, 'type'=>'deposits'])->orWhere(['user' => auth()->user()->email, 'type'=>'booking'])->latest()->get();
        $deposits = Transaction::where('user', auth()->user()->email)->where(function ($q) { $q->where('type', 'deposits')->orWhere('type', 'booking'); })->latest()->get();
        $paystackDeposit = auth()->user()->paystack()->whereType('deposits')->latest()->get();

        foreach ($deposits as $deposit){
            $allDeposits->push($deposit);
        }

        foreach ($paystackDeposit as $deposit){
            $allDeposits->push($deposit);
        }

        return view('user.deposits', ['deposits'=>$allDeposits->sortByDesc('created_at')]);

    }

    public function referral()
    {

        if(! auth()->user()->hasCompletedProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete your registration to access the system.</div></div>');
        }

        if(! auth()->user()->hasCompletedKinProfile()){
            return redirect("/profile")->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Please complete next of kin details.</div></div>');
        }

        return view('user.referral', ['referrals' => Transaction::where(['user' => auth()->user()->email, 'type'=>'referral'])->get()]);
    }

    public function download($type)
    {

        $types = ['all','investments','deposits', 'payouts'];

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

            case 'deposits':
                return $this->downloadExcel(new DepositTransactionsExport,'Deposit Transactions.xlsx' );
                break;

            case 'payouts':
                return $this->downloadExcel(new PayoutTransactionsExport,'Payout Transactions.xlsx' );
                break;

            default:
                break;
        }


    }

    protected function downloadExcel($data, $name = 'Transactions.xlsx')
    {
        return Excel::download($data, $name);
    }

    public function interAccountTransfer(Request $request)
    {
        if($request->from == $request->to){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You cannot make transfer to the same wallet</div></div>');
        }

        switch($request->from){

            case 'wallet':
                $from = auth()->user()->wallet;
                $fromName = "Wallet";
                break;

            case 'bank':
                $from = auth()->user()->emeraldbank;
                $fromName = "Emerald Bank";
                break;
        }

        if($request->from  == 'bank'){
            $withdrawableAmount = auth()->user()->emeraldbank->total_amount - auth()->user()->bookings()->where('status','approved')->sum('amount');

            if($request->amount > $withdrawableAmount){
                return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You do not have sufficient funds in the account you want to make transfer from or fundsa are tied to investments.</div></div>');
            }
        }

        if($request->amount > $from->total_amount){
            return redirect()->back()->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You do not have sufficient funds in the account you want to make transfer from.</div></div>');
        }

        switch($request->to){

            case 'wallet':
                $to = auth()->user()->wallet;
                $toName = "Wallet";

                break;

            case 'bank':
                $to = auth()->user()->emeraldbank;
                $toName = "Emerald Bank";

                break;

        }

        $from->decrement('total_amount', $request->amount);
        $to->increment('total_amount', $request->amount);

        Transaction::create([
            'amount'=>$request->amount,
            'type'=>'transfer',
            'user'=>auth()->user()->email,
            'status'=>'approved',
            'user_id' => auth()->id()
        ]);

        auth()->user()->notify(new CustomNotification("Your inter-account transfer of <b>₦". number_format($request->amount) ."</b> from ". $fromName ." to ". $toName ." was successful. Should you have any questions or complaints, please kindly contact our support team.", '<span class="dropdown-item-icon bg-success text-white"><i class="fas fa-exchange-alt"></i></span>', 'Inter-account Transfer'));

        $title= ' ';
        $name = auth()->user()->name;
        $content = "Your inter-account transfer of <b>₦". number_format($request->amount) ."</b> from ". $fromName ." to ". $toName ." was successful. Should you have any questions or complaints, please kindly contact our support team.";
//        $content = "Your interaccount transfer of NGN ". number_format($request->amount) ." from {$fromName} to {$toName} was successful.";
        $button = false;
        $button_text = '';
        $subject = "Inter-account Transfer";
        Mail::to(auth()->user()->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your interaccount funds transfer was successful.</div></div>');

    }

}
