<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('opcrs')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE `opcrs` MODIFY `status` ENUM('submitted','for_dept_head_review','endorsed','approved','returned') NOT NULL DEFAULT 'submitted'"
            );
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('opcrs')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE `opcrs` SET `status` = 'for_dept_head_review' WHERE `status` = 'submitted'");
            DB::statement("UPDATE `opcrs` SET `status` = 'approved' WHERE `status` = 'endorsed'");
            DB::statement(
                "ALTER TABLE `opcrs` MODIFY `status` ENUM('for_dept_head_review','approved','returned') NOT NULL DEFAULT 'for_dept_head_review'"
            );
        }
    }
};

