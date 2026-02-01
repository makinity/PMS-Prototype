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
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->unique()->after('id');
            $table->string('role')->default('employee')->after('email');
            $table->boolean('is_active')->default(false)->after('role');
            $table->timestamp('activated_at')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('activated_at');
            $table->string('profile_photo_path')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn([
                'employee_id',
                'role',
                'is_active',
                'activated_at',
                'last_login_at',
                'profile_photo_path',
            ]);
        });
    }
};
