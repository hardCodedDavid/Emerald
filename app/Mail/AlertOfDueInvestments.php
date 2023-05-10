<?php

namespace App\Mail;

use App\Admin;
use App\Exports\DownloadInvestments;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Excel;

class AlertOfDueInvestments extends Mailable
{
    use Queueable, SerializesModels;

    public $investments;
    public $title,$name,$content,$button,$button_text,$button_link,$subjcet;

    /**
     * Create a new message instance.
     *
     * @param $investments
     */
    public function __construct($investments)
    {
        $this->investments = $investments;
        $this->title = '';
        $this->name = Admin::first()->name;
        $this->content = 'Here are the investment payouts due for today.';
        $this->button = false;
        $this->button_text = '';
        $this->subject = "New Investment Created";
        $this->button_link = '';
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $attachment = (app()->make(Excel::class))->raw(new DownloadInvestments($this->investments), Excel::XLSX);
        return $this->subject('Due Investments')
                    ->view('emails.short-investments')
                    ->attachData($attachment,'Due Investments.xlsx');
    }
}
