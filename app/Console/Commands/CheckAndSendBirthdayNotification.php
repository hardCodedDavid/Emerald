<?php

namespace App\Console\Commands;

use App\Notifications\BirthdayNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Twilio\Rest\Client;

class CheckAndSendBirthdayNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and Send Birthday Notification to Sponsors';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function handle()
    {
        $sid = 'ACc97c82d7cafd672aff75af9ab4bb3057';
        $token = '24de4d4884f10ea17a18562b2213e5ca';
        $client = new Client($sid, $token);


        $users = User::whereDate('dob', Carbon::today()->toDateString())->get();

        $users->each(function($user) use ($client){
            $phone = preg_replace("/^0/", "+234", $user->phone);;

            $user->notify(new BirthdayNotification);

            $client->messages->create(
                $phone,
                [
                    'from' => 'EmeraldFarm',
                    'body' => 'Hello '. $user->name .', We at Emerald Farms wish you a very happy birthday. It is our sincere desire that the years ahead will bring you fulfillment and happy moments. With Love, Emerald Farms.'
                ]
            );

            $this->info("DONE");
        });
    }
}
