<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaidMilestoneNotification extends Notification
{
    use Queueable;

    public $name;
    public $milestone;
    public $investment;
    public $isFinal;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $milestone, $investment, $isFinal)
    {
        $this->name = $name;
        $this->milestone = $milestone;
        $this->investment = $investment;
        $this->isFinal = $isFinal;
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
        if ($this->isFinal){
            return (new MailMessage)
                ->subject('Longterm Investment Payout')
                ->greeting('Dear '.ucwords($this->name).',')
                ->line('Your Long-term investment of <b>₦'.number_format($this->investment->amount_invested,2).'</b> in <b>'.ucwords($this->investment->farm->title).'</b> has been fully paid.')
                ->line('Investment Overview: <br>Amount Invested: ₦'.number_format($this->investment->amount_invested,2).'<br>Return on Investment: ₦'.number_format($this->investment->getTotalROI(), 2).'<br>Total Amount Received: ₦'.number_format($this->investment->getTotalROI() + $this->investment->amount_invested ,2).'<br>Duration: '.$this->investment->getPaymentDurationInDays().' days <br> Milestones: '.$this->investment->farm->milestone.' <br>')
                ->line('Should you have any questions or complaints, please kindly contact our support team.')
                ->line('Thank you for choosing Emerald Farms.')
                ->view('emails.new_custom');
        }else{
            return (new MailMessage)
                ->subject('Longterm Investment Payout')
                ->greeting('Dear '.ucwords($this->name).',')
                ->line('You just got a milestone payment of <b>₦'.number_format($this->milestone->amount,2).'</b> for your investment in <b>'.ucwords($this->investment->farm->title).'</b>')
                ->line('<table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                          <tbody>
                            <tr>
                              <td align="left" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                  <tbody>
                                    <tr>
                                      <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #2abb50; border-radius: 5px; text-align: center;"> <a href="' .url('/transactions/investments/long/'.$this->investment->id.'/show').'" target="_blank" style="display: inline-block; color: #ffffff; background-color: #2abb50; border: solid 1px #2abb50; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #2abb50;">View Investment</a> </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>')
                ->line('Should you have any questions or complaints, please kindly contact our support team.')
                ->line('Thank you for choosing Emerald Farms.')
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
        if ($this->isFinal){
            return [
                'body'=>'Your Long-term investment of <b>₦'.number_format($this->investment->amount_invested,2).'</b> in <b>'.ucwords($this->investment->farm->title).'</b> has been fully paid.
                         Investment Overview: <br>Amount Invested: ₦'.number_format($this->investment->amount_invested,2).'<br>Return on Investment: ₦'.number_format($this->investment->getTotalROI(), 2).'<br>Total Amount Received: ₦'.number_format($this->investment->getTotalROI() + $this->investment->amount_invested ,2).'<br>Duration: '.$this->investment->getPaymentDurationInDays().' days <br> Milestones: '.$this->investment->farm->milestone.' <br>
                         Should you have any questions or complaints, please kindly contact our support team.',
                'icon'=>'<span class="dropdown-item-icon bg-success text-white"> <i class="fas fa-tag"></i></span>',
                'title'=>'Longterm Investment Payout'
            ];
        }else{
            return [
                'body'=>'You just got a milestone payment of <b>₦'.number_format($this->milestone->amount,2).'</b> for your investment in <b>'.ucwords($this->investment->farm->title).'</b>
                        Should you have any questions or complaints, please kindly contact our support team.',
                'icon'=>'<span class="dropdown-item-icon bg-success text-white"> <i class="fas fa-tag"></i></span>',
                'title'=>'Longterm Investment Payout'
            ];
        }
    }
}
