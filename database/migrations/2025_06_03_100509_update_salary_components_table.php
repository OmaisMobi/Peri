<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            // Drop foreign key constraints first
            if (Schema::hasColumn('salary_components', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('salary_components', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }

            // Change default of apply_to_all to true
            $table->boolean('apply_to_all')->default(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            // Restore the columns with their foreign keys
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->boolean('apply_to_all')->default(false)->change();
        });
    }
};
