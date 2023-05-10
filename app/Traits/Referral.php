<?php

namespace App\Traits;

use App\Mail\SendMailable;
use App\Transaction;
use App\Wallet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait Referral {

    protected function payReferrer($referrerUser, $amount)
    {
    
        // if(auth()->user()->referrals()->whereCode($referrerUser->code)->exists()){
        //     return false;
        // }

        // $referralBenefit = 0.015 * $amount;

        // $referrerWallet = Wallet::where('user', $referrerUser->email)->first();

        // $referrerWallet->update([
        //     'total_amount'=> ($referrerWallet->total_amount + $referralBenefit)
        // ]);

        // Transaction::create([
        //     'amount'=>$referralBenefit,
        //     'type'=>'referral',
        //     'user'=>$referrerUser->email,
        //     'status'=>'approved',
        // ]);

        // auth()->user()->referrals()->create([
        //     'code' => $referrerUser->code
        // ]);

        // $title= ' ';
        // $name = $referrerUser->name;
        // $content = 'Your referred user has just made an investment successfully. <br> You are credited a bonus of '. number_format($referralBenefit,2);
        // $button = false;
        // $button_text = '';
        // $subject = "Referral Bonus";
        // Mail::to($referrerUser->email)->send(new SendMailable($title,$name,$content,$button,$button_text,$subject));

        // return true;
    }
}
