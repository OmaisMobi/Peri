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
        Schema::table('company_details', function (Blueprint $table) {
            // 1. Add the new country_id column
            $table->unsignedBigInteger('country_id')->nullable()->after('company_name');

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');

            $table->dropColumn('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            // Re-add the 'country' column if you roll back
            $table->string('country')->nullable()->after('country_id'); // Adjust type and length as per original

            // Remove foreign key constraint
            $table->dropForeign(['country_id']);

            // Drop the new 'country_id' column
            $table->dropColumn('country_id');
        });
    }
};
