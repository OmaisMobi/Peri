<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tax_slabs', function (Blueprint $table) {
            $table->date('financial_year_start')->nullable()->after('salary_currency');
            $table->date('financial_year_end')->nullable()->after('financial_year_start');
        });
    }

    public function down(): void
    {
        Schema::table('tax_slabs', function (Blueprint $table) {
            $table->dropColumn(['financial_year_start', 'financial_year_end']);
        });
    }
};
