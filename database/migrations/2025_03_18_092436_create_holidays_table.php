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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->date('starting_date');
            $table->date('ending_date');
            $table->integer('type')->comment('1 => Weekend, 2 => Religious Day, 3 => National Day, 4 => Other');
            $table->text('remarks')->nullable();
            $table->enum('apply', ['shift', 'department', 'user', 'all'])->comment('Determines whether the holiday applies to a shift, department, or user');
            $table->json('departments')->nullable()->comment('Applicable department IDs');
            $table->json('users')->nullable()->comment('Applicable user IDs');
            $table->json('shifts')->nullable()->comment('Applicable shift IDs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
