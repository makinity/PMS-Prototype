<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $duplicates = DB::table('users')
            ->selectRaw('LOWER(TRIM(name)) as normalized_name, COUNT(*) as aggregate_count')
            ->groupBy('normalized_name')
            ->havingRaw('COUNT(*) > 1')
            ->limit(1)
            ->exists();

        if ($duplicates) {
            throw new RuntimeException('Cannot add unique login-name index: duplicate normalized user names exist. Resolve duplicates first.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('name', 'users_name_login_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_name_login_unique');
        });
    }
};
