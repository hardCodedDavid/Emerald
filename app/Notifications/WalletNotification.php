<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletNotification extends Notification
{
    use Queueable;

    public $name, $old_amount, $amount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $old_amount, $amount)
    {
        $this->name = $name;
        $this->old_amount = $old_amount;
        $this->amount = $amount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
//        return ['mail', 'database'];
        return [];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if($this->old_amount > $this->amount) return (new MailMessage)
            ->greeting('Dear '.ucwords($this->name).',')
            ->line('Your wallet was debited <b>₦'.number_format($this->old_amount - $this->amount, 2).'</b>')
            ->line('Your new wallet balance is <b>₦'.number_format($this->amount,2).'</b>')
            ->line('Thank you for using our application!')
            ->view('emails.new_custom');

        if($this->old_amount < $this->amount) return (new MailMessage)
            ->greeting('Dear '.ucwords($this->name).',')
            ->line('Your wallet has been credited with <b>₦'.number_format($this->amount-$this->old_amount, 2).'</b>')
            ->line('Your new wallet balance is <b>₦'.number_format($this->amount,2).'</b>')
            ->line('Thank you for using our application!')
            ->view('emails.new_custom');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if($this->old_amount > $this->amount)
            return [
                'body'=>'Your wallet was debited <b>₦'.number_format($this->old_amount - $this->amount, 2).'</b> <br>Your new wallet balance is <b>₦'.number_format($this->amount,2).'</b>',
                'icon'=>'<span class="dropdown-item-icon bg-info text-white"> <i class="fas fa-wallet"></i></span>',
                'title'=>'Debit Notification'
            ];

        if($this->old_amount < $this->amount)
            return [
                'body'=>'Your wallet has been credited with <b>₦'.number_format($this->amount-$this->old_amount, 2).'</b><br>Your new wallet balance is <b>₦'.number_format($this->amount,2).'</b>',
                'icon'=>'<span class="dropdown-item-icon bg-success text-white"> <i class="fas fa-wallet"></i><span>',
                'title'=>'Credit Notification'
            ];
    }
}
