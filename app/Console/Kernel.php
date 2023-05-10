<?php

namespace App\Console;

use App\Http\Controllers\BookingController;
use App\Http\Controllers\FarmlistController;
use App\Investment;
use App\MilestoneInvestment;
use App\Notifications\InvestmentNotification;
use App\Notifications\MilestoneInvestmentNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Notifications\BirthdayNotification;
use App\User;
use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('backup:clean')->twiceDaily(0, 12);
        $schedule->command('backup:run --only-db')->twiceDaily(0, 12);
        $schedule->call(function(){

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

            });

        })->daily();

        $schedule->call(function(){
            $investments = Investment::where('maturity_status', 'pending')->where('maturity_date', '>', date('Y-m-d H:i:s'))->get();
            foreach ($investments as $investment)
            {
                $user = User::find($investment->user_id);
                if($user){
                    $user->notify(new InvestmentNotification(explode(' ', $user->name)[0], $investment, false, false, true, false, false));
                }
            }
        })->everyMinute();

        $schedule->call(function(){
            (new FarmlistController())->checkForFarmStartDateAndMarkFarmAsOpen();
        })->everyMinute()->name('farm:open')->withoutOverlapping();

        $schedule->call(function(){
            (new FarmlistController())->checkForFarmCloseDateAndStartInvestmentCountdown();
        })->everyMinute()->name('investment:start')->withoutOverlapping();

        $schedule->call(function(){
            (new FarmlistController())->checkForMaturityAndMarkClosed();
        })->everyMinute()->name('investment:close')->withoutOverlapping();

        $schedule->call(function(){
          //todo add cron for payout
        })->dailyAt('00:02');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
