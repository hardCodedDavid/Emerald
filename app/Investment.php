<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
//    protected $fillable = [
//        'amount_invested', 'farmlist', 'package', 'user', 'maturity_date', 'maturity_status', 'units', 'returns', 'status', 'user_id','farm_id'
//    ];

    protected $guarded = [];

    protected $dates = ['maturity_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function farm()
    {
        return $this->belongsTo(FarmList::class);
    }

    public function isMature()
    {
        return $this->maturity_date != null && $this->maturity_date->lt(now());
    }

    public function isPaid()
    {
        return $this->paid == 1;
    }

}
