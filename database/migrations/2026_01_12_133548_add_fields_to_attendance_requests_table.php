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
        Schema::table('attendance_requests', function (Blueprint $table) {
            $table->time('total_rest')->default('00:00:00')->after('status');
            $table->json('breaks')->nullable()->after('total_rest');
            $table->time('total_break')->default('00:00:00')->after('breaks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            $table->dropColumn('total_rest');
            $table->dropColumn('breaks');
            $table->dropColumn('total_break');
        });
    }
};
