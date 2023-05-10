<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verified extends Model
{
    protected $fillable = [
        'reference', 'user_id', 'amount', 'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
