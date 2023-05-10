<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class FarmList extends Model
{
    use HasSlug;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    protected $fillable = [
        'slug', 'title', 'start_date', 'close_date', 'status', 'cover', 'price', 'description', 'package', 'interest', 'maturity_date', 'available_units', 'category_id'
    ];


    protected $dates = [
        'start_date', 'close_date'
    ];

    public function isOpen()
    {
        return $this->start_date->lte(now()) && $this->close_date->gt(now());
    }

    public function isPending()
    {
        return now()->gt($this->start_date);
    }

    public function isClosed()
    {
        return $this->close_date->lte(now());
    }

    public function getEditStartDateAttribute()
    {
        return Carbon::parse($this->start_date)->format('Y-m-d\TH:i');
    }

    public function getEditCloseDateAttribute()
    {
        return Carbon::parse($this->close_date)->format('Y-m-d\TH:i');
    }

    public function investments()
    {
        return $this->hasMany(Investment::class,'farm_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'farmlist_id');
    }

    public function canStartInvestment()
    {
        return $this->close_date->lt(now());
    }

    public function canOpenFarm()
    {
        return $this->start_date->lt(now());
    }
}
