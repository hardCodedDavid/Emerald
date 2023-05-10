<?php

namespace App\Http\Controllers;

use App\FarmList;
use App\Investment;
use App\Mail\SendMailable;
use App\Transaction;
use App\User;
use App\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function pay($user, $amount, $attribute){
        $curl = curl_init();

        $email = $user;
        $amount = $amount*100;

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",

            CURLOPT_POSTFIELDS => json_encode([
                'amount'=>$amount,
                'email'=>$email,
                'bearer'=>'subaccount',
                'metadata'=>$attribute,
            ]),

            CURLOPT_HTTPHEADER => [
                "authorization: Bearer sk_live_cf3c6df4079b0dcff6feaee560c8d7aaebd5d7ac",
//                "authorization: Bearer sk_test_93c63794ab92876d1d4406afdce58cff8703d4b7",
                "content-type: application/json",
                "cache-control: no-cache",
            ],
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);

        if($error){
            echo 'Error: ' . $error;
        }

        $transaction = json_decode($response, true);

        if(! $transaction['status']){
            echo 'Error: ' . $transaction['message'];
        }

        return $transaction['data']['authorization_url'];
    }

    public function verifyPay(Request $request)
    {
        if(! $this->compareTransaction($request->trxref)){
            return redirect('/wallet')->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Invalid transaction detected.</div></div>');
        }

        $payload = $this->confirmTransaction($request->trxref);

        if(! $payload->status){
            return redirect('/wallet')->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>An error occurred while making a payment.</div></div>');
        }

        $user = User::where('email', $payload->data->customer->email)->first();

        switch($payload->data->metadata->type){

            case 'booking':
                $this->processBooking($payload, $user, $request->trxref);
                return redirect("/wallet")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your investment has been approved.</div></div>');
                break;

            case 'deposits':
                $this->processDeposits($payload, $user, $request->trxref);
                return redirect('/wallet')->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your deposit has been approved and successfully.</div></div>');
                break;

            case 'investments':
                $this->processInvestments($payload, $user, $request->trxref);
                return redirect("/farmlist")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your investment has been approved.</div></div>');
                break;

            default:
                return redirect('/wallet')->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>An error occurred while making a payment.</div></div>');
                break;
        }
    }

    public function confirmTransaction($reference){
        $curl = curl_init();

        if(! $reference){
            echo 'Error: No reference supplied';
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer sk_live_cf3c6df4079b0dcff6feaee560c8d7aaebd5d7ac",
//                "authorization: Bearer sk_test_93c63794ab92876d1d4406afdce58cff8703d4b7",
                "accept: application/json",
                "cache-control: no-cache"
            ],
        ));
        $response = curl_exec($curl);
        $error = curl_error($curl);
        if($error){
            echo 'Curl returned error: ' . $error;
        }

        return json_decode($response);
    }

    public function compareTransaction($reference){

        if(Verified::where('reference', $reference)->count() > 0){
            return false;
        }

        return true;
    }

    public function processBooking($payload, $user, $reference)
    {
        $units = $payload->data->metadata->units;
        $amount = $payload->data->amount /100;

        $farmlist = FarmList::findOrFail($payload->data->metadata->farm_id);

        $farmlist->decrement('available_units', $units);

        Verified::create([
            'reference' => $reference,
            'type' => 'booking',
            'user_id' => $user->id,
            'amount' => $amount
        ]);

        $booking = $user->bookings()->create([
            'farmlist_id' => $farmlist->id,
            'category_id' => $payload->data->metadata->category_id,
            'units' => $units,
            'rollover' => !! $payload->data->metadata->rollover,
            'amount' => $amount,
            'status' => 'approved'
        ]);

        Transaction::create([
            'amount' => $amount,
            'type' => 'investments',
            'user' => $user->email,
            'status' => 'approved',
            'user_id' => $user->id,
            'booking_id' => $booking->id

        ]);

        $user->emeraldbank()->increment('total_amount', $amount);

        $title= ' ';
        $name = $user->name;
        $content = 'Your farm booking has been successful. NGN '. number_format($booking->amount) .' from emerald bank will be automatically used to sponsor a farm for you.';
        $button = false;
        $button_text = '';
        $subject = "Investment Booked";
        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
    }

    private function processDeposits($payload, $user, $reference)
    {
        $amount = ($payload->data->amount /100);

        if($payload->data->metadata->location == 'bank'){
            $user->emeraldbank()->increment('total_amount', $amount);
            $depositLocation = 'Emerald Bank';
            $balance = $user->emeraldbank->fresh()->total_amount;
            //todo add it to booking

        }else{
            $user->wallet()->increment('total_amount', $amount);
            $depositLocation = 'Emerald Wallet';
            $balance = $user->wallet->fresh()->total_amount;
        }

        Transaction::create([
            'amount' => $amount,
            'type' => 'deposits',
            'user' => $user->email,
            'status' => 'approved',
            'user_id' => $user->id,
            'location' => $payload->data->metadata->location
        ]);

        Verified::create([
            'reference' => $reference,
            'type' => 'deposits',
            'user_id' => $user->id,
            'amount' => $amount
        ]);

        $title= ' ';
        $name = $user->name;
        $content = 'Your deposit of N'.number_format($amount,2).' in '. $depositLocation. ' has been approved successfully.<br>You have the total balance of N'.number_format($balance,2).' in your wallet';
        $button = false;
        $button_text = '';
        $subject = "Deposit Approved";
        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));
    }

    private function processInvestments($payload, $user, $reference)
    {
        $amount = ($payload->data->amount /100);

        $user = User::where('email', $user->email)->first();
        $farmlist = FarmList::where('slug', $payload->data->metadata->farmlist)->first();

        Investment::create([
            'amount_invested' => $amount,
            'farmlist' => $farmlist->slug,
            'user' => $user->email,
            'maturity_status' => 'pending',
            'units' => $payload->data->metadata->units,
            'returns' => $amount,
            'status' => 'pending',
            'user_id' => $user->id,
            'farm_id' => $farmlist->id,
            'rollover' => $payload->data->metadata->rollover
        ]);

        Transaction::create([
            'amount' => $amount,
            'type' => 'investments',
            'user' => $user->email,
            'status' => 'approved',
            'user_id' => $user->id
        ]);

        $farmlist->decrement('available_units', $payload->data->metadata->units);

        Verified::create([
            'reference' => $reference,
            'type' => 'investments',
            'user_id' => $user->id
        ]);

        $title= ' ';
        $name = $user->name;
        $content = 'Your investment has been created successfully. <br> You invested N'. number_format($amount,2).' under the farmlist titled <big>'.$farmlist->title.'</big>.';
        $button = false;
        $button_text = '';
        $subject = "Investment Created";
        Mail::to($user->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

    }


}
