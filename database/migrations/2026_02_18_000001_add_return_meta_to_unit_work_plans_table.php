<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_work_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('unit_work_plans', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('locked_at');
            }
            if (!Schema::hasColumn('unit_work_plans', 'returned_by')) {
                $table->unsignedBigInteger('returned_by')->nullable()->after('returned_at');
                $table->index('returned_by');
            }
            if (!Schema::hasColumn('unit_work_plans', 'returned_by_role')) {
                $table->string('returned_by_role', 30)->nullable()->after('returned_by');
                $table->index('returned_by_role');
            }
            if (!Schema::hasColumn('unit_work_plans', 'return_remarks')) {
                $table->text('return_remarks')->nullable()->after('returned_by_role');
            }

            // FK (only add if column exists and FK not present)
            // NOTE: Laravel cannot "if fk exists" reliably; create a safe try/catch if needed.
        });

        // Add FK in a separate Schema::table to avoid issues on some MySQL setups
        Schema::table('unit_work_plans', function (Blueprint $table) {
            // If you already have this FK, comment this out.
            try {
                $table->foreign('returned_by')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            } catch (\Throwable $e) {
                // no-op
            }
        });
    }

    public function down(): void
    {
        Schema::table('unit_work_plans', function (Blueprint $table) {
            // Drop FK first if it exists
            try { $table->dropForeign(['returned_by']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['returned_by']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['returned_by_role']); } catch (\Throwable $e) {}

            $table->dropColumn([
                'returned_at',
                'returned_by',
                'returned_by_role',
                'return_remarks',
            ]);
        });
    }
};
