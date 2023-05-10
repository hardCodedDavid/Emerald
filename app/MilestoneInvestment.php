<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MilestoneInvestment extends Model
{
    protected $guarded = [];

    protected $dates = ['approved_date'];

    public function farm()
    {
        return $this->belongsTo(MilestoneFarm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function milestoneDates()
    {
        $dates = [];

        $investmentTenureInDays = $this->getPaymentDurationInDays();
        $timesToPay = ceil($investmentTenureInDays / $this->farm->milestone);

        for ($i = 1; $i <= $this->farm->milestone; $i++){
            $daysToAdd = $timesToPay * $i;
            $dates[] = $this->approved_date->addDays($daysToAdd);
        }
        return $dates;
    }

    public function getMilestoneReturn($milestone)
    {
        $interest = json_decode($this->farm->interest);
        return $this->amount_invested * ($interest[$milestone] / 100);
    }

    public function getTotalROI()
    {
        $total = 0;
        for ($i=0; $i < $this->farm->milestone; $i++) {
            $total += $this->getMilestoneReturn($i);
        }
        return $total;
    }

    // public function milestoneReturns()
    // {
        // $percentage = $this->farm->interest/100;
        // $fullReturns = $this->amount_invested * $percentage;
        // return number_format(10000);
    // }

    public function getPaymentDurationInDays()
    {
        return $this->approved_date->diffInDays($this->approved_date->addMonths($this->farm->duration));
    }

    public function investmentStatus()
    {
        return 'pending';
    }

    public function payments()
    {
        return $this->hasMany(PaidMileStone::class);
    }
}
