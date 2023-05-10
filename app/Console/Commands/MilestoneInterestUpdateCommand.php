<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MilestoneFarm;

class MilestoneInterestUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'milestone:interest';

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
        MilestoneFarm::all()->each(function($farmlist) {
            $interestArr = [];
            for ($i = 0; $i < $farmlist->milestone; $i++) {
                $interestArr[] = $farmlist->interest;
            }
            $farmlist->interest = json_encode($interestArr);
            $farmlist->update();
        });
        $this->info("Interest updated");
    }
}
