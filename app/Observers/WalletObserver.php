<?php

namespace App\Observers;

use App\Wallet;
use App\User;
use App\Notifications\WalletNotification;

class WalletObserver
{
    /**
     * Handle the wallet "created" event.
     *
     * @param  \App\Wallet  $wallet
     * @return void
     */
    public function created(Wallet $wallet)
    {
        //
    }

    /**
     * Handle the wallet "updated" event.
     *
     * @param  \App\Wallet  $wallet
     * @return void
     */
    public function updated(Wallet $wallet)
    {
        $user = User::findOrFail($wallet->user_id);
        if($wallet->isDirty('total_amount')){
            $old_amount = $wallet->getOriginal('total_amount'); 
            $amount = $wallet->total_amount; 
            $user->notify(new WalletNotification(explode(' ', $user->name)[0], $old_amount, $amount));
        }
    }

    /**
     * Handle the wallet "deleted" event.
     *
     * @param  \App\Wallet  $wallet
     * @return void
     */
    public function deleted(Wallet $wallet)
    {
        //
    }

    /**
     * Handle the wallet "restored" event.
     *
     * @param  \App\Wallet  $wallet
     * @return void
     */
    public function restored(Wallet $wallet)
    {
        //
    }

    /**
     * Handle the wallet "force deleted" event.
     *
     * @param  \App\Wallet  $wallet
     * @return void
     */
    public function forceDeleted(Wallet $wallet)
    {
        //
    }
}
