<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MailResetPasswordNotification extends Notification
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
        return ['mail'];
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
                    //->view('reset.emailer')
                  ->from('noreply@example.com')
                  ->subject( 'Reset your password' )
                  ->line( "Hello! " )
                  ->line("You are receiving this email because we received a password reset request for your account.")
                  ->action( 'Reset Password', $link )
                  //->attach('reset.attachment')
                  ->line( "This password reset link will expire in 60 minutes." )
                  ->line("If you did not request a password reset, no further action is required.")
                  ->line("Regards,")
                  ->line("Second Salary");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
