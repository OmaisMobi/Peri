<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('users')->onDelete('cascade');

            // Late Minutes Policy
            $table->boolean('late_policy_enabled')->default(false);
            $table->boolean('enable_late_come')->default(false);
            $table->boolean('enable_early_leave')->default(false);
            $table->boolean('time_offset_allowance')->default(false);
            $table->boolean('half_day_late_policy')->default(false);

            // Single Biometric Policy
            $table->boolean('single_biometric_policy_enabled')->default(false);
            $table->enum('single_biometric_behavior', ['half_day', 'biometric_missing', 'nothing'])->nullable();

            // Grace Minutes Policy
            $table->boolean('grace_policy_enabled')->default(false);
            $table->integer('days_counter')->nullable();
            $table->integer('late_penalty')->nullable();
            $table->enum('grace_duration', ['day', 'month'])->default('day')->nullable();

            // Sandwich Rule Policy
            $table->boolean('sandwich_rule_policy_enabled')->default(false);
            $table->enum('leaves_policy_option', [
                'before',
                'after',
                'after_and_before',
                'after_or_before'
            ])->nullable();

            // Consecutive leaves Policy
            $table->boolean('consecutive_leaves_policy_enabled')->default(false);
            $table->integer('consecutive_leave_gap_days')->nullable();

            // Overtime Policy
            $table->boolean('overtime_policy_enabled')->default(false);
            $table->integer('overtime_start_delay')->nullable();
            $table->integer('overtime_max_minutes')->nullable();
            $table->enum('overtime_duration', ['per_day', 'per_month'])->default('per_day')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_policies');
    }
};
