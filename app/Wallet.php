<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'total_amount', 'user', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
