<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns to offices table
        Schema::table('offices', function (Blueprint $table) {
            if (!Schema::hasColumn('offices', 'head_id')) {
                $table->foreignId('head_id')
                    ->nullable()
                    ->after('name')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('offices', 'code')) {
                $table->string('code')->nullable()->after('name');
            }
        });

        // Only add position to users if office_id exists
        if (Schema::hasColumn('users', 'office_id')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'position')) {
                    $table->string('position')->nullable()->after('office_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            if (Schema::hasColumn('offices', 'head_id')) {
                $table->dropForeign(['head_id']);
                $table->dropColumn('head_id');
            }

            if (Schema::hasColumn('offices', 'code')) {
                $table->dropColumn('code');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'position')) {
                $table->dropColumn('position');
            }
        });
    }
};
