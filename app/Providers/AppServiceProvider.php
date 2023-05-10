<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Investment;
use App\MilestoneInvestment;
use App\PaidMileStone;
use App\Transaction;
use App\Wallet;
use App\Observers\InvestmentObserver;
use App\Observers\MilestoneInvestmentObserver;
use App\Observers\PaidMilestoneObserver;
use App\Observers\TransactionObserver;
use App\Observers\WalletObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
        Investment::observe(InvestmentObserver::class);
        MilestoneInvestment::observe(MilestoneInvestmentObserver::class);
        PaidMileStone::observe(PaidMilestoneObserver::class);
        Transaction::observe(TransactionObserver::class);
        Wallet::observe(WalletObserver::class);
    }
}
