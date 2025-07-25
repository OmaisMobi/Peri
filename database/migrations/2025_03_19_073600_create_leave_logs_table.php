<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('leave_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_id');
            $table->unsignedBigInteger('role_id'); // The role that acted at this step
            $table->unsignedInteger('level'); // The approval level number (e.g. 1, 2, ...)
            $table->string('status'); // 'approved' or 'rejected'
            $table->text('remarks')->nullable();
            $table->timestamps(); // includes a created_at timestamp

            // Foreign key constraint (optional, adjust as needed)
            $table->foreign('leave_id')->references('id')->on('leaves')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_logs');
    }
};
