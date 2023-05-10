<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingNotification extends Notification
{
    use Queueable;

    public $isDeclined;
    public $isApproved;
    public $isBooked;
    public $inv;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($inv, $isApproved, $isDeclined, $isBooked)
    {
        $this->inv = $inv;
        $this->isApproved = $isApproved;
        $this->isDeclined = $isDeclined;
        $this->isBooked = $isBooked;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if($this->isDeclined){
            return [
                'body'=>'Your <strong>₦'.number_format($this->inv->amount).'</strong> booking of <strong>'.$this->inv->units.'</strong> units with <strong>'.ucwords($this->inv->farm->title).'</strong> farm has been declined.',
                'icon'=>'<span class="dropdown-item-icon bg-danger text-white"> <i class="fas fa-tag"></i></span>',
                'title'=>'Booking Declined'
            ];
        }elseif($this->isApproved){
            return [
                'body'=>'Your <strong>₦'.number_format($this->inv->amount).'</strong> booking of <strong>'.$this->inv->units.'</strong> units with <strong>'.ucwords($this->inv->farm->title).'</strong> farm has been approved.',
                'icon'=>'<span class="dropdown-item-icon bg-success text-white"> <i class="fas fa-tag"></i></span>',
                'title'=>'Booking Created'
            ];
        }elseif($this->isBooked){
            return [
                'body'=>'Your booking of <strong>₦ '. number_format($this->inv->amount) .'</strong> for <strong>'.ucwords($this->inv->farm->title).'</strong> has been registered successfully and saved into your Emerald Bank.<br><br>Our system will automatically sponsor the farm for you when it opens.',
                'icon'=>'<span class="dropdown-item-icon bg-success text-white"> <i class="fas fa-tag"></i></span>',
                'title'=>'Investment Booked'
            ];
        }
    }
}
