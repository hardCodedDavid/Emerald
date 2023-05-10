<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_payouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('batch')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('units')->nullable();
            $table->string('amount_invested')->nullable();
            $table->string('expected_returns')->nullable();
            $table->string('farm_cycle')->nullable();
            $table->string('payment_date')->nullable();
            $table->string('queue')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batch_payouts');
    }
}
