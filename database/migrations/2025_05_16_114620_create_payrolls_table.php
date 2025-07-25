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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            // Foreign key linking to the users table (employees)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pay_run_id')->constrained()->cascadeOnDelete();
            // Date range for the payroll period
            $table->date('date_range_start');
            $table->date('date_range_end');
            // Indicator for the month within a year cycle (e.g., 12 for Jan, 11 for Feb... 1 for Dec)
            $table->integer('month_indicator');
            // Base salary used for calculation in this period
            $table->decimal('base_salary', 15, 2);

            // JSON columns to store detailed data arrays/objects
            $table->json('earnings_data')->nullable(); // Custom earnings details
            $table->json('deductions_data')->nullable(); // Custom deductions details
            $table->json('attendance_data')->nullable(); // Attendance summary, rates, and calculated amounts
            $table->json('tax_data')->nullable(); // Tax calculation details and applied slabs

            // Final calculated net pay
            $table->decimal('net_payable_salary', 15, 2);
            // Amount of increment applied in this payroll generation (if any)
            $table->decimal('applied_increment_amount', 15, 2)->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
