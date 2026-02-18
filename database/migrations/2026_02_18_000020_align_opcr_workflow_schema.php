<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('opcrs')) {
            Schema::create('opcrs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('unit_work_plan_id')->constrained('unit_work_plans');
                $table->foreignId('office_id')->constrained('offices');
                $table->foreignId('performance_period_id')->constrained('performance_periods');
                $table->foreignId('generated_by')->constrained('users');
                $table->string('status', 20)->default('draft');
                $table->dateTime('submitted_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('returned_at')->nullable();
                $table->text('remarks')->nullable();
                $table->dateTime('locked_at')->nullable();
                $table->timestamps();

                $table->unique('unit_work_plan_id');
                $table->index('status', 'opcrs_status_idx');
                $table->index('office_id', 'opcrs_office_id_idx');
                $table->index('performance_period_id', 'opcrs_period_id_idx');
            });
            return;
        }

        Schema::table('opcrs', function (Blueprint $table) {
            if (!Schema::hasColumn('opcrs', 'office_id')) {
                $table->foreignId('office_id')->nullable()->after('unit_work_plan_id');
            }

            if (!Schema::hasColumn('opcrs', 'performance_period_id')) {
                $table->foreignId('performance_period_id')->nullable()->after('office_id');
            }

            if (!Schema::hasColumn('opcrs', 'submitted_at')) {
                $table->dateTime('submitted_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('opcrs', 'returned_at')) {
                $table->dateTime('returned_at')->nullable()->after('approved_at');
            }

            if (!Schema::hasColumn('opcrs', 'remarks')) {
                $table->text('remarks')->nullable()->after('returned_at');
            }
        });

        DB::table('opcrs')
            ->join('unit_work_plans', 'opcrs.unit_work_plan_id', '=', 'unit_work_plans.id')
            ->where(function ($query) {
                $query->whereNull('opcrs.office_id')
                    ->orWhereNull('opcrs.performance_period_id');
            })
            ->update([
                'opcrs.office_id' => DB::raw('unit_work_plans.office_id'),
                'opcrs.performance_period_id' => DB::raw('unit_work_plans.performance_period_id'),
            ]);

        DB::table('opcrs')
            ->where('status', 'for_dept_head_review')
            ->update(['status' => 'submitted']);

        DB::table('opcrs')
            ->where('status', 'endorsed')
            ->update(['status' => 'approved']);

        DB::table('opcrs')
            ->whereNotIn('status', ['draft', 'submitted', 'returned', 'approved'])
            ->update(['status' => 'draft']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `opcrs` MODIFY `status` VARCHAR(20) NOT NULL DEFAULT 'draft'");
        }

        try {
            Schema::table('opcrs', function (Blueprint $table) {
                if (!Schema::hasColumn('opcrs', 'office_id') || !Schema::hasColumn('opcrs', 'performance_period_id')) {
                    return;
                }

                $table->index('status', 'opcrs_status_idx');
                $table->index('office_id', 'opcrs_office_id_idx');
                $table->index('performance_period_id', 'opcrs_period_id_idx');
            });
        } catch (\Throwable $e) {
            // Indexes may already exist in older schema states.
        }

        try {
            Schema::table('opcrs', function (Blueprint $table) {
                if (!Schema::hasColumn('opcrs', 'office_id') || !Schema::hasColumn('opcrs', 'performance_period_id')) {
                    return;
                }

                $table->foreign('office_id', 'opcrs_office_id_fk')->references('id')->on('offices');
                $table->foreign('performance_period_id', 'opcrs_period_id_fk')->references('id')->on('performance_periods');
            });
        } catch (\Throwable $e) {
            // Foreign keys may already exist in older schema states.
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('opcrs')) {
            return;
        }

        try {
            Schema::table('opcrs', function (Blueprint $table) {
                if (Schema::hasColumn('opcrs', 'office_id')) {
                    $table->dropForeign('opcrs_office_id_fk');
                    $table->dropIndex('opcrs_office_id_idx');
                }

                if (Schema::hasColumn('opcrs', 'performance_period_id')) {
                    $table->dropForeign('opcrs_period_id_fk');
                    $table->dropIndex('opcrs_period_id_idx');
                }

                if (Schema::hasColumn('opcrs', 'status')) {
                    $table->dropIndex('opcrs_status_idx');
                }
            });
        } catch (\Throwable $e) {
            // Ignore when indexes/FKs are not present in rollback target state.
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE `opcrs` SET `status` = 'for_dept_head_review' WHERE `status` = 'submitted'");
            DB::statement("UPDATE `opcrs` SET `status` = 'submitted' WHERE `status` = 'draft'");
            DB::statement("ALTER TABLE `opcrs` MODIFY `status` ENUM('submitted','for_dept_head_review','endorsed','approved','returned') NOT NULL DEFAULT 'submitted'");
        }
    }
};
