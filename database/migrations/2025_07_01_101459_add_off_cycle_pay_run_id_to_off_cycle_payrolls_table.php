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
        Schema::table('off_cycle_payrolls', function (Blueprint $table) {
            $table->foreignId('off_cycle_pay_run_id')->nullable()->constrained('off_cycle_pay_runs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('off_cycle_payrolls', function (Blueprint $table) {
            $table->dropForeign(['off_cycle_pay_run_id']);
            $table->dropColumn('off_cycle_pay_run_id');
        });
    }
};
