<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_component_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade'); // Optional: If you're scoping by admin
            $table->timestamps();
        });

        // Add the foreign key to salary_components table
        Schema::table('salary_components', function (Blueprint $table) {
            $table->foreignId('salary_component_category_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            $table->dropForeign(['salary_component_category_id']);
            $table->dropColumn('salary_component_category_id');
        });

        Schema::dropIfExists('salary_component_categories');
    }
};
