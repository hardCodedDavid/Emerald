<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaidMileStone extends Model
{
	protected $guarded = [];
	
    public function investment()
    {
        return $this->belongsTo(MilestoneInvestment::class);
    }
}
