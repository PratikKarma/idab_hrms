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
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');
            $table->enum('type', ['clock_in', 'clock_out']);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_requests');
    }
};
