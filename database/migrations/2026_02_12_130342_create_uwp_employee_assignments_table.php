<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uwp_employee_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_work_plan_id')->constrained('unit_work_plans')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users');

            $table->timestamps();

            $table->unique(['unit_work_plan_id', 'employee_id'], 'uq_uwp_employee');
            $table->index(['employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uwp_employee_assignments');
    }
};
