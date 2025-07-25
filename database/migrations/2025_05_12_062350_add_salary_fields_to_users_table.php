<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_number')->nullable()->after('shift_id');
            $table->string('salary_currency', 3)->nullable()->after('account_number');
            $table->decimal('base_salary', 10, 2)->nullable()->after('salary_currency');
            $table->decimal('probation_salary', 10, 2)->nullable()->after('base_salary');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'account_number',
                'salary_currency',
                'base_salary',
                'probation_salary',
            ]);
        });
    }
};
