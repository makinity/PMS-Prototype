<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ipcrs')) {
            return;
        }

        $addPerformancePeriod = !Schema::hasColumn('ipcrs', 'performance_period_id');
        $addOffice = !Schema::hasColumn('ipcrs', 'office_id');
        $addGeneratedAt = !Schema::hasColumn('ipcrs', 'generated_at');

        Schema::table('ipcrs', function (Blueprint $table) use ($addPerformancePeriod, $addOffice, $addGeneratedAt) {
            if ($addPerformancePeriod) {
                $table->foreignId('performance_period_id')
                    ->nullable()
                    ->after('employee_id')
                    ->constrained('performance_periods')
                    ->nullOnDelete();
            }

            if ($addOffice) {
                $table->foreignId('office_id')
                    ->nullable()
                    ->after('performance_period_id')
                    ->constrained('offices')
                    ->nullOnDelete();
            }

            if ($addGeneratedAt) {
                $table->timestamp('generated_at')
                    ->nullable()
                    ->after('status');
            }
        });

        if (Schema::hasColumn('ipcrs', 'status') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `ipcrs` MODIFY `status` VARCHAR(40) NOT NULL DEFAULT 'for_commitment'");
        }

        if (Schema::hasColumn('ipcrs', 'status')) {
            DB::table('ipcrs')
                ->where('status', 'generated')
                ->update(['status' => 'for_commitment']);
        }

        try {
            Schema::table('ipcrs', function (Blueprint $table) {
                $table->unique(['opcr_id', 'employee_id'], 'uq_opcr_employee');
            });
        } catch (\Throwable $e) {
            // The unique constraint already exists in older schema states.
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('ipcrs')) {
            return;
        }

        if (Schema::hasColumn('ipcrs', 'status')) {
            DB::table('ipcrs')
                ->where('status', 'for_commitment')
                ->update(['status' => 'generated']);
        }

        $hasPerformancePeriod = Schema::hasColumn('ipcrs', 'performance_period_id');
        $hasOffice = Schema::hasColumn('ipcrs', 'office_id');
        $hasGeneratedAt = Schema::hasColumn('ipcrs', 'generated_at');

        if ($hasOffice) {
            try {
                Schema::table('ipcrs', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('office_id');
                });
            } catch (\Throwable $e) {
                try {
                    Schema::table('ipcrs', function (Blueprint $table) {
                        $table->dropForeign(['office_id']);
                        $table->dropColumn('office_id');
                    });
                } catch (\Throwable $e) {
                    // Ignore rollback errors for partially-applied states.
                }
            }
        }

        if ($hasPerformancePeriod) {
            try {
                Schema::table('ipcrs', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('performance_period_id');
                });
            } catch (\Throwable $e) {
                try {
                    Schema::table('ipcrs', function (Blueprint $table) {
                        $table->dropForeign(['performance_period_id']);
                        $table->dropColumn('performance_period_id');
                    });
                } catch (\Throwable $e) {
                    // Ignore rollback errors for partially-applied states.
                }
            }
        }

        if ($hasGeneratedAt) {
            Schema::table('ipcrs', function (Blueprint $table) {
                $table->dropColumn('generated_at');
            });
        }
    }
};
