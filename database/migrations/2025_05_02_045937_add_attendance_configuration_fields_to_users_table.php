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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remote_attendance')) {
                $table->dropColumn('remote_attendance');
            }

            $table->string('attendance_type')->nullable()->after('attendance_config');
            $table->integer('hours_required')->nullable()->after('attendance_type');
            $table->json('work_days')->nullable()->after('hours_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('remote_attendance')->default(false)->after('attendance_config');

            $table->dropColumn(['attendance_type', 'hours_required', 'work_days']);
        });
    }
};
