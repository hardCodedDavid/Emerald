<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Admin;
use DateTime;
use App\Package;
use App\Investment;
use App\FarmList;
use App\Bank;
use App\Wallet;
use App\Mail\SendMailable;
use Mail;
use App\Verified;
use App\NewsUpdate;

class Globals extends Controller
{
    public static function getUser(){
    	$id = Auth::id();
    	$user = User::where('id',$id)->first();
    	return $user;
    }

    public static function getAdmin(){
    	$id = Auth::guard('admin')->id();
    	$user = Admin::where('id',$id)->first();
    	return $user;
    }

    public static function getPassport($user){
    	if($user->passport != '' || $user->passport != null){
    		return asset($user->passport);
    	}else {
    		return asset("img/avatar.png");
    	}
    }

    public static function getUserByEmail($email){
        return User::where('email', $email)->first();
    }

    public static function getUserByCode($code){
        return User::where('code', $code)->first();
    }

    public static function getPackage($slug){
        return Package::where("slug", $slug)->first();
    }

    public static function getFarmlist($slug){
        return FarmList::where('slug', $slug)->first();
    }

    public static function completeProfile($user){
        if (
            $user->address == '' || $user->state == '' ||
            $user->country == '' || Bank::where('user', $user->email)->count() == 0 ||
            $user->city == '' || $user->dob == ''
        ){
            return true;
        }else {
            return false;
        }
    }

    public static function completeProfileKin($user){

        if (
            $user->nk_Name == NULL || $user->nk_Address == NULL ||
            $user->nk_Phone == NULL || $user->nk_Email == NULL
        ){
            return true;
        }else {
            return false;
        }
    }

    public static function getReopen(){
        $farms = FarmList::where('status', 'closed')->orderBy('id', 'desc')->get();
        foreach($farms as $farm){
            $end = strtotime($farm->close_date);
            $now = strtotime(now());
            $diff = $end-$now;if($diff > 0){
                FarmList::where('id', $farm->id)->update(['status'=>'opened']);
            }
        }
    }

    public static function getBank($id){
        return Bank::where('id', $id)->first();
    }

    public static function getWallet($user){
        return Wallet::where('user', $user->email)->first();
    }

    public static function getBanks($user){
        return Bank::where('user', $user)->get();
    }

    public function updateWallet($user,$amount){
        Wallet::where('user', $user)->update([
            'total_amount'=>$amount,
        ]);
    }

    public static function pay($user, $amount, $attribute){
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
//            "authorization: Bearer sk_test_93c63794ab92876d1d4406afdce58cff8703d4b7",
            "content-type: application/json",
            "cache-control: no-cache",
          ],
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        if($err){
          echo 'Error: ' . $err;
        }

        $tranx = json_decode($response, true);
        if(! $tranx['status']){
          echo 'Error: ' . $tranx['message'];
        }
        return $tranx['data']['authorization_url'];
    }

    public static function confirm($ref){
        $curl = curl_init();
        $reference = $ref;
        if(!$reference){
          echo 'Error: No reference supplied';
        }
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
//              "authorization: Bearer sk_test_93c63794ab92876d1d4406afdce58cff8703d4b7",
              "authorization: Bearer sk_live_cf3c6df4079b0dcff6feaee560c8d7aaebd5d7ac",
            "cache-control: no-cache"
          ],
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        if($err){
          echo 'Curl returned error: ' . $err;
        }
        $tranx = json_decode($response);
        return $tranx;
    }

    public static function compareTranaction($ref){
        $transaction = count(Verified::where('reference', $ref)->get());
        if($transaction > 0){
            return false;
        }else {
            return true;
        }
    }

    public static function getNewsCounts($farmlist){
        return count(NewsUpdate::where('farmlist', $farmlist)->get());
    }

    public static function getLastUpdated($farmlist){
        return NewsUpdate::orderBy('id', 'desc')->first()->created_at;
    }

    public static function getLatest(){
        return NewsUpdate::limit(4)->orderBy('id','desc')->get();
    }
}
