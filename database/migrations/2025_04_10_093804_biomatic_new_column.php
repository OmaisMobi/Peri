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
        Schema::table('biometrics', function (Blueprint $table) {
            $table->enum('period', ['morning', 'break_start', 'break_end', 'evening'])->after('timedate');
            $table->text('reason')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biometrics', function (Blueprint $table) {
            $table->dropColumn(['period']);
            $table->text('reason')->nullable(false)->change();
        });
    }
};
