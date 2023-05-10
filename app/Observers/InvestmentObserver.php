<?php

namespace App\Observers;

use App\Investment;
use App\Mail\SendMailable;
use App\Notifications\InvestmentNotification;
use App\User;
use Illuminate\Support\Facades\Mail;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class InvestmentObserver
{
    /**
     * Handle the investment "created" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function created(Investment $investment)
    {
        $user = User::find($investment->user_id);
        if($user){
            $status = $investment->status;
            if($status == 'pending'){
                $user->notify(new InvestmentNotification(explode(' ', $user->name)[0], $investment, false, false, false, false, true));
            }elseif($status == 'active') {
                $user->notify(new InvestmentNotification(explode(' ', $user->name)[0], $investment, false, false, false, true, false));
            }
        }
    }

    /**
     * Handle the investment "updated" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function updated(Investment $investment)
    {
        if($investment->isDirty('status')){
            $user = User::find($investment->user_id);
            $status = $investment->status;
            if($status == 'pending'){
                $this->payRef($user, $investment);
                $user->notify(new InvestmentNotification(explode(' ', $user->name)[0], $investment, false, false, false, false, true));
            }elseif($status == 'active') {
                $user->notify(new InvestmentNotification(explode(' ', $user->name)[0], $investment, false, false, false, true, false));
            }elseif($status == 'declined'){
                $user->notify(new InvestmentNotification(explode(' ', $user->name)[0], $investment, true, false, false, false, false));
            }
        }elseif($investment->isDirty('maturity_status')){
            $user = User::find($investment->user_id);
            $status = $investment->status;
            if($status == 'matured'){
                $user->notify(new InvestmentNotification(explode(' ', $user->name)[0], $investment, false, false, true, false, false));
            }
        }elseif($investment->isDirty('paid')){
            $user = User::find($investment->user_id);
            if($investment->paid > 0){
                $user->notify(new InvestmentNotification(explode(' ', $user->name)[0], $investment, false, true, false, false, false));
            }
        }
    }

    /**
     * Handle the investment "deleted" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function deleted(Investment $investment)
    {
        //
    }

    /**
     * Handle the investment "restored" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function restored(Investment $investment)
    {
        //
    }

    /**
     * Handle the investment "force deleted" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function forceDeleted(Investment $investment)
    {
        //
    }
}
