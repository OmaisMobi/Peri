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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('name');
            $table->enum('duration', ['annual', '1 month', '3 months', '4 months', '6 months']);
            $table->enum('apply_on', ['all', 'male_unmarried', 'male_married', 'female_unmarried', 'female_married']);
            $table->integer('leaves_count');
            $table->timestamps();

            // If you have a users table, you might want to add a foreign key constraint:
            // $table->foreign('team_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
