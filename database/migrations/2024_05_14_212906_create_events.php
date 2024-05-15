<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id_event');
            $table->string('title');
            $table->string('description');
            $table->string('date');
            $table->string('time');
            $table->string('location');
            $table->bigInteger('duration');
            $table->bigInteger('max_attendees');
            $table->boolean('restriction_minors_allowed');
            $table->bigInteger('id_user');
            $table->string('status');
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
        Schema::dropIfExists('events');
    }
}
