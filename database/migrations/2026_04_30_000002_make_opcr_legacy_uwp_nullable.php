<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('opcrs') || !Schema::hasColumn('opcrs', 'unit_work_plan_id')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        try {
            Schema::table('opcrs', function (Blueprint $table) {
                $table->dropForeign('opcrs_unit_work_plan_id_foreign');
            });
        } catch (\Throwable $e) {
            // Older or partially migrated databases may not have the FK under this name.
        }

        DB::statement('ALTER TABLE opcrs MODIFY unit_work_plan_id BIGINT UNSIGNED NULL');

        try {
            Schema::table('opcrs', function (Blueprint $table) {
                $table->foreign('unit_work_plan_id', 'opcrs_unit_work_plan_id_foreign')
                    ->references('id')
                    ->on('unit_work_plans')
                    ->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // If the FK exists under another name, leave the nullable column in place.
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('opcrs') || !Schema::hasColumn('opcrs', 'unit_work_plan_id')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::table('opcrs')
            ->whereNull('unit_work_plan_id')
            ->delete();

        try {
            Schema::table('opcrs', function (Blueprint $table) {
                $table->dropForeign('opcrs_unit_work_plan_id_foreign');
            });
        } catch (\Throwable $e) {
            // Ignore rollback from partial schema states.
        }

        DB::statement('ALTER TABLE opcrs MODIFY unit_work_plan_id BIGINT UNSIGNED NOT NULL');

        try {
            Schema::table('opcrs', function (Blueprint $table) {
                $table->foreign('unit_work_plan_id', 'opcrs_unit_work_plan_id_foreign')
                    ->references('id')
                    ->on('unit_work_plans');
            });
        } catch (\Throwable $e) {
            // Ignore rollback from partial schema states.
        }
    }
};
