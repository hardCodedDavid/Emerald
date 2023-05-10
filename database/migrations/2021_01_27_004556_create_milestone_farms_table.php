<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMilestoneFarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milestone_farms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('slug');
            $table->dateTime('start_date');
            $table->dateTime('close_date');
            $table->string('status')->default('pending');
            $table->string('interest');
            $table->string('available_units');
            $table->string('milestone');
            $table->string('duration');
            $table->string('cover');
            $table->string('price');
            $table->string('description');
            $table->unsignedBigInteger('category_id');
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
        Schema::dropIfExists('milestone_farms');
    }
}
