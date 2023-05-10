<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvestmentNotification extends Notification
{
    use Queueable;

    public $name;
    public $inv;

    public $isDeclined;
    public $isPaid;
    public $isMatured;
    public $isActive;
    public $isPending;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $inv, $isDeclined, $isPaid, $isMatured, $isActive, $isPending)
    {
        $this->name = $name;
        $this->inv = $inv;
        $this->isDeclined = $isDeclined;
        $this->isPaid = $isPaid;
        $this->isMatured = $isMatured;
        $this->isActive = $isActive;
        $this->isPending = $isPending;
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
//        return ['database'];
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
        if($this->isDeclined){
            return (new MailMessage)
                    ->greeting('Dear '.ucwords($this->name).',')
                    ->line('Your <b>₦'.number_format($this->inv->amount_invested).'</b> purchase of <b>'.$this->inv->units.' units</b> with <b>'.ucwords($this->inv->farm->title).'</b> farm has been declined.')
                    ->line('If you have any complaints or queries, please reach out to our support desk.')
                    ->line('Thank you for trusting us!')
                    ->view('emails.new_custom');
        }elseif($this->isPaid){
            return (new MailMessage)
                    ->greeting('Dear '.ucwords($this->name).',')
                    ->line('Your <b>₦'.number_format($this->inv->amount_invested).'</b> purchase of <b>'.$this->inv->units.' units</b> with <b>'.ucwords($this->inv->farm->title).'</b> farm has been paid to your wallet.')
                    ->line('If you have any complaints or queries, please reach out to our support desk.')
                    ->line('Thank you for trusting us!')
                    ->view('emails.new_custom');
        }elseif($this->isMatured){
            return (new MailMessage)
                    ->greeting('Dear '.ucwords($this->name).',')
                    ->line('Your <b>₦'.number_format($this->inv->amount_invested).'</b> purchase of <b>'.$this->inv->units.' units</b> with <b>'.ucwords($this->inv->farm->title).'</b> farm has matured for payouts.')
                    ->line('If you have any complaints or queries, please reach out to our support desk.')
                    ->line('Thank you for trusting us!')
                    ->view('emails.new_custom');
        }elseif($this->isActive){
            return (new MailMessage)
                    ->greeting('Dear '.ucwords($this->name).',')
                    ->line('Your <b>₦'.number_format($this->inv->amount_invested).'</b> purchase of <b>'.$this->inv->units.' units</b> with <b>'.ucwords($this->inv->farm->title).'</b> farm is now active.')
                    ->line('If you have any complaints or queries, please reach out to our support desk.')
                    ->line('Thank you for trusting us!')
                    ->view('emails.new_custom');
        }elseif($this->isPending){
            return (new MailMessage)
                    ->greeting('Dear '.ucwords($this->name).',')
                    ->line('Your <b>₦'.number_format($this->inv->amount_invested).'</b> purchase of <b>'.$this->inv->units.' units</b> with <b>'.ucwords($this->inv->farm->title).'</b> farm is successful.')
                    ->line('If you have any complaints or queries, please reach out to our support desk.')
                    ->line('Thank you for trusting us!')
                    ->view('emails.new_custom');
        }

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
                'body'=>'Your ₦'.number_format($this->inv->amount_invested).' purchase of '.$this->inv->units.' units with '.ucwords($this->inv->farm->title).' farm has been declined.',
                'icon'=>'<span class="dropdown-item-icon bg-danger text-white"> <i class="fas fa-chart-line"></i></span>',
                'title'=>'Investment Declined'
            ];
        }elseif($this->isPaid){
            return [
                'body'=>'Your ₦'.number_format($this->inv->amount_invested).' purchase of '.$this->inv->units.' units with '.ucwords($this->inv->farm->title).' farm has been paid to your wallet.',
                'icon'=>'<span class="dropdown-item-icon bg-success text-white"> <i class="fas fa-chart-line"></i></span>',
                'title'=>'Investment Paid'
            ];
        }elseif($this->isMatured){
            return [
                'body'=>'Your ₦'.number_format($this->inv->amount_invested).' purchase of '.$this->inv->units.' units with '.ucwords($this->inv->farm->title).' farm has matured for payouts.',
                'icon'=>'<span class="dropdown-item-icon bg-info text-white"> <i class="fas fa-chart-line"></i></span>',
                'title'=>'Investment Matured'
            ];
        }elseif($this->isActive){
            return [
                'body'=>'Your ₦'.number_format($this->inv->amount_invested).' purchase of '.$this->inv->units.' units with '.ucwords($this->inv->farm->title).' farm is now active.',
                'icon'=>'<span class="dropdown-item-icon bg-primary text-white"> <i class="fas fa-chart-line"></i></span>',
                'title'=>'Investment Approved'
            ];
        }elseif($this->isPending){
            return [
                'body'=>'Your investment has been created successfully. <br><br>Here is your investment overview below: <br>Amount Invested: N'. number_format($this->inv->amount_invested,2).'<br>Units Purchased: '.$this->inv->units.'<br>Farm: '.$this->inv->farm->title.'.',
                'icon'=>'<span class="dropdown-item-icon bg-warning text-white"> <i class="fas fa-chart-line"></i></span>',
                'title'=>'Investment Created'
            ];
        }

    }
}
