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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'google2fa_secret')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('google2fa_secret')->nullable()->after('is_disable');
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'google2fa_enable')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('google2fa_enable')->default(0)->after('google2fa_secret');
            });
        }

            //

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
