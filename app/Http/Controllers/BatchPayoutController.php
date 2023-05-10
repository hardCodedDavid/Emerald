<?php

namespace App\Http\Controllers;

use App\BatchPayout;
use Illuminate\Http\Request;

class BatchPayoutController extends Controller
{
    public function index()
    {
        $data = BatchPayout::all()->groupBy('farm_cycle');
        $farms = [];
        foreach ($data as $key=>$res) {
            $farms[] = ucwords($key);
        }
        if (\request('name_or_email') && \request('farm_cycle')) {
            $payouts = BatchPayout::where(function ($q) {
                $q->where('name', \request('name_or_email'))->orwhere('email', \request('name_or_email'));
            })->where('farm_cycle', \request('farm_cycle'))->get();
        } else {
            $payouts = [];
        }
        return view('user.batch-payouts.index', compact('farms', 'payouts'));
    }
}
