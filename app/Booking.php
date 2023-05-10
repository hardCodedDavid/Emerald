<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function farm()
    {
        return $this->belongsTo(FarmList::class, 'farmlist_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
