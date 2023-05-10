<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Jusibe\JusibeChannel;
use NotificationChannels\Jusibe\JusibeMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class BirthdayNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Happy Birthday!')
                    ->line($notifiable->name .'!')
                    ->line('We heard it\'s your birthday today, and we would like to wish you well. ')
                    ->line('With love from all of us at Emerald Farms.')
                    ->line('Have a great day!');
    }


    public function toArray($notifiable)
    {
        return [
            'body'=>'We heard it\'s your birthday today, and we would like to wish you well. <br>With love from all of us at Emerald Farms.<br>Have a great day!',
            'icon'=>'<span class="dropdown-item-icon bg-danger text-white"> <i class="fas fa-tag"></i></span>',
            'title'=>'Happy Birthday!'
        ];
    }

}
