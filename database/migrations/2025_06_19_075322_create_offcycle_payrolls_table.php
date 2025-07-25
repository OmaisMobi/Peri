<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('off_cycle_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->decimal('base_salary', 15);
            $table->string('payment_mode');
            $table->string('account_number')->nullable();
            $table->json('earnings')->nullable();
            $table->json('deductions')->nullable();
            $table->decimal('tax', 15)->default(0);
            $table->decimal('net_pay', 15);
            $table->string('status')->default('pending_approval');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('off_cycle_payrolls');
    }
};
