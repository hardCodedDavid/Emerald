<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class MilestoneFarm extends Model
{
    use HasSlug;

    protected $guarded = [];

    protected $dates = [
        'start_date', 'close_date'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

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
        return $this->hasMany(MilestoneInvestment::class);
    }

}
