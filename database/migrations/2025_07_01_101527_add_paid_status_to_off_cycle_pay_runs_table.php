<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('off_cycle_pay_runs', function (Blueprint $table) {
            $table->boolean('paid')->default(false)->after('status');
            $table->date('paid_date')->nullable()->after('paid');
        });
    }


    public function down(): void
    {
        Schema::table('off_cycle_pay_runs', function (Blueprint $table) {
            $table->dropColumn(['paid', 'paid_date']);
        });
    }
};
