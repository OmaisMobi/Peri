<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'account_number',
                'base_salary',
                'probation_salary',
                'account_holder_name',
                'bank_name',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->string('account_number')->nullable();
            $table->decimal('base_salary', 10, 2)->nullable();
            $table->decimal('probation_salary', 10, 2)->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable();
        });
    }
};
