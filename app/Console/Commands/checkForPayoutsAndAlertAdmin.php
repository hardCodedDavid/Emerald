<?php

namespace App\Console\Commands;

use App\Investment;
use App\Mail\AlertOfDueInvestments;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class checkForPayoutsAndAlertAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investments:check-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     */
    public function handle()
    {
        $investments = Investment::all()->filter(function($investment){
                            if($investment->isMature() && $investment->maturity_date->startOfDay()->eq(now()->startOfDay())){
                                return true;
                            }
                            return false;
                        });

        if($investments->count()){
            Mail::to('transactions@emeraldfarms.ng')->send(new AlertOfDueInvestments($investments));
        }

    }
}
