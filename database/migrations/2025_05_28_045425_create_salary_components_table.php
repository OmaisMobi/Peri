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
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->comment('References the admin user who owns this component record'); // Or your specific admin/company table
            $table->string('title');
            $table->enum('component_type', ['earning', 'deduction']);
            $table->enum('value_type', ['number', 'percentage']);
            $table->decimal('amount', 15, 2); // Amount or percentage value
            $table->enum('tax_status', ['taxable', 'non-taxable'])->default('taxable')->comment('Taxable: affects taxable income. Non-taxable: applied post-tax or is tax-exempt.');
            $table->boolean('is_active')->default(true);
            $table->boolean('apply_to_all')->default(false)->comment('Apply to all users under this team_id if user_id and department_id are null');
            $table->timestamps();
            $table->index(['team_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
