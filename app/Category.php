<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function farms()
    {
        return $this->hasMany(FarmList::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
