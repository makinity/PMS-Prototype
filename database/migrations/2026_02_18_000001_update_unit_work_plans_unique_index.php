<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('unit_work_plans', function (Blueprint $table) {
            $table->index('office_id', 'unit_work_plans_office_id_idx');
            $table->index('performance_period_id', 'unit_work_plans_performance_period_id_idx');
            $table->dropUnique('unit_work_plans_office_id_performance_period_id_unique');
            $table->unique(['office_id', 'performance_period_id', 'created_by'], 'unit_work_plans_office_period_creator_unique');
        });
    }

    public function down(): void
    {
        Schema::table('unit_work_plans', function (Blueprint $table) {
            $table->dropUnique('unit_work_plans_office_period_creator_unique');
            $table->unique(['office_id', 'performance_period_id'], 'unit_work_plans_office_id_performance_period_id_unique');
        });
    }
};
