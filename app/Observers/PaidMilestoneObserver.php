<?php

namespace App\Observers;

use App\PaidMilestone;
use App\MilestoneInvestment;
use App\Notifications\PaidMilestoneNotification;

class PaidMilestoneObserver
{
    /**
     * Handle the paid milestone "created" event.
     *
     * @param  \App\PaidMilestone  $paidMilestone
     * @return void
     */
    public function created(PaidMilestone $paidMilestone)
    {
        $investment = MilestoneInvestment::find($paidMilestone->milestone_investment_id);
        $user = $investment->user;
        if(count($investment->milestoneDates()) == count($investment->payments)){
            $isFinal = true;
        }else{
            $isFinal = false;
        }
        $user->notify(new PaidMilestoneNotification(explode(' ', $user->name)[0], $paidMilestone, $investment, $isFinal));
    }

    /**
     * Handle the paid milestone "updated" event.
     *
     * @param  \App\PaidMilestone  $paidMilestone
     * @return void
     */
    public function updated(PaidMilestone $paidMilestone)
    {
        //
    }

    /**
     * Handle the paid milestone "deleted" event.
     *
     * @param  \App\PaidMilestone  $paidMilestone
     * @return void
     */
    public function deleted(PaidMilestone $paidMilestone)
    {
        //
    }

    /**
     * Handle the paid milestone "restored" event.
     *
     * @param  \App\PaidMilestone  $paidMilestone
     * @return void
     */
    public function restored(PaidMilestone $paidMilestone)
    {
        //
    }

    /**
     * Handle the paid milestone "force deleted" event.
     *
     * @param  \App\PaidMilestone  $paidMilestone
     * @return void
     */
    public function forceDeleted(PaidMilestone $paidMilestone)
    {
        //
    }
}
