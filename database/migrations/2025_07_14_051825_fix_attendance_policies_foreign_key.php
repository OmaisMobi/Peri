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
        Schema::table('attendance_policies', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_policies', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};