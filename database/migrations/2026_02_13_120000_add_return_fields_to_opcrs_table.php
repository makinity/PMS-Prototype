<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('opcrs')) {
            Schema::table('opcrs', function (Blueprint $table) {
                if (!Schema::hasColumn('opcrs', 'returned_at')) {
                    $table->dateTime('returned_at')->nullable()->after('approved_at');
                }

                if (!Schema::hasColumn('opcrs', 'remarks')) {
                    $table->text('remarks')->nullable()->after('returned_at');
                }
            });

            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement(
                    "ALTER TABLE `opcrs` MODIFY `status` ENUM('for_dept_head_review','approved','returned') NOT NULL DEFAULT 'for_dept_head_review'"
                );
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('opcrs')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("UPDATE `opcrs` SET `status` = 'for_dept_head_review' WHERE `status` = 'returned'");
                DB::statement(
                    "ALTER TABLE `opcrs` MODIFY `status` ENUM('for_dept_head_review','approved') NOT NULL DEFAULT 'for_dept_head_review'"
                );
            }

            Schema::table('opcrs', function (Blueprint $table) {
                if (Schema::hasColumn('opcrs', 'remarks')) {
                    $table->dropColumn('remarks');
                }

                if (Schema::hasColumn('opcrs', 'returned_at')) {
                    $table->dropColumn('returned_at');
                }
            });
        }
    }
};

