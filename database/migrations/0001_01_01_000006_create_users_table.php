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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_super_admin')->default(false);
            $table->foreignId('latest_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('role')->default('Admin');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('father_name')->nullable();
            $table->string('blood_group')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('cnic')->nullable();
            $table->string('martial_status')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('emergency_person')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('probation')->nullable();
            $table->string('designation')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->text('address')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('attendance_config')->default(false);
            $table->boolean('remote_attendance')->default(false);
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->json('devices')->nullable();
            $table->json('documents')->nullable();
            $table->boolean('resigned')->default(false);
            $table->date('resign_date')->nullable();
            $table->string('remarks')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
