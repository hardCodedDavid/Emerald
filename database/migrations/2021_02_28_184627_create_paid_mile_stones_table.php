<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaidMileStonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paid_mile_stones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('amount');
            $table->unsignedBigInteger('milestone_investment_id');
            $table->integer('milestone');
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
        Schema::dropIfExists('paid_mile_stones');
    }
}
