<?php

namespace App\Observers;

use App\Transaction;
use App\User;
use App\Notifications\TransactionNotification;

class TransactionObserver
{
    /**
     * Handle the transaction "created" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {
        $user = User::findOrFail($transaction->user_id);
        $status = $transaction->status;
        if($user){
            if($status == 'approved'){
                $user->notify(new TransactionNotification(explode(' ', $user->name)[0], false, false, true, $transaction));
            }else {
                $user->notify(new TransactionNotification(explode(' ', $user->name)[0], true, false, false, $transaction));
            }
        }
    }

    /**
     * Handle the transaction "updated" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        if($transaction->isDirty('status')){
            $user = User::findOrFail($transaction->user_id);
            $status = $transaction->status;
            if($status == 'approved'){
                $user->notify(new TransactionNotification(explode(' ', $user->name)[0], false, false, true, $transaction));
            }elseif($status == 'declined'){
                $user->notify(new TransactionNotification(explode(' ', $user->name)[0], false, true, false, $transaction));
            }
        }
    }

    /**
     * Handle the transaction "deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function deleted(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "restored" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function restored(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "force deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function forceDeleted(Transaction $transaction)
    {
        //
    }
}
