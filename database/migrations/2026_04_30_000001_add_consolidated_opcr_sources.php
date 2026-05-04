<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('opcr_unit_work_plan')) {
            Schema::create('opcr_unit_work_plan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('opcr_id')->constrained('opcrs')->cascadeOnDelete();
                $table->foreignId('unit_work_plan_id')->constrained('unit_work_plans')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['opcr_id', 'unit_work_plan_id'], 'opcr_uwp_unique');
                $table->index('unit_work_plan_id', 'opcr_uwp_uwp_idx');
            });
        }

        if (Schema::hasTable('unit_work_plans') && Schema::hasColumn('unit_work_plans', 'status')) {
            $driver = Schema::getConnection()->getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement("ALTER TABLE unit_work_plans MODIFY status ENUM('draft','submitted','consolidated','endorsed','pmt_approved','returned') NOT NULL DEFAULT 'draft'");
            }
        }

        if (Schema::hasTable('opcrs')) {
            try {
                Schema::table('opcrs', function (Blueprint $table) {
                    if (Schema::hasColumn('opcrs', 'office_id') && Schema::hasColumn('opcrs', 'performance_period_id')) {
                        $table->unique(['office_id', 'performance_period_id'], 'opcrs_office_period_unique');
                    }
                });
            } catch (\Throwable $e) {
                // Constraint may already exist or duplicate legacy data may need manual cleanup.
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('opcrs')) {
            try {
                Schema::table('opcrs', function (Blueprint $table) {
                    $table->dropUnique('opcrs_office_period_unique');
                });
            } catch (\Throwable $e) {
                // Ignore rollback from partial schema states.
            }
        }

        if (Schema::hasTable('unit_work_plans') && Schema::hasColumn('unit_work_plans', 'status')) {
            $driver = Schema::getConnection()->getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement("UPDATE unit_work_plans SET status = 'submitted' WHERE status = 'consolidated'");
                DB::statement("ALTER TABLE unit_work_plans MODIFY status ENUM('draft','submitted','endorsed','pmt_approved','returned') NOT NULL DEFAULT 'draft'");
            }
        }

        Schema::dropIfExists('opcr_unit_work_plan');
    }
};
