<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftLogsTable extends Migration
{
    public function up()
    {
        Schema::create('shift_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id'); // reference to the shifts table
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('name');
            $table->integer('short_leave')->nullable();
            $table->time('starting_time');
            $table->time('ending_time');
            $table->time('break_start');
            $table->time('break_end');
            $table->time('half_day_check_in');
            $table->time('half_day_check_out');
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shift_logs');
    }
}
