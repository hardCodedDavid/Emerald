<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('bookings', function (Blueprint $table) {
//            $table->bigIncrements('id');
//            $table->unsignedBigInteger('user_id');
//            $table->unsignedBigInteger('category_id');
//            $table->string('status')->default('pending');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('bookings');
    }
}
