<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->boolean('paid')->default(false);
            $table->string('leave_type')->nullable();
            $table->string('half_day_timing')->nullable();
            $table->date('starting_date');
            $table->date('ending_date')->nullable();
            $table->time('starting_time')->nullable();
            $table->time('ending_time')->nullable();
            $table->string('document')->nullable();
            $table->text('leave_reason');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
