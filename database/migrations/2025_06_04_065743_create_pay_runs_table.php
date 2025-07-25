<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_pay_runs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pay_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month'); // 1-12
            $table->year('year');
            $table->date('pay_period_start_date');
            $table->date('pay_period_end_date');
            $table->string('status')->default('draft'); // draft, pending_approval, finalized, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pay_runs');
    }
};
