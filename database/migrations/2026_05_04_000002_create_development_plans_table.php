<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ipcr_id')->constrained('ipcrs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('office_id')->constrained('offices')->cascadeOnDelete();
            $table->foreignId('performance_period_id')->constrained('performance_periods')->cascadeOnDelete();
            $table->decimal('source_score', 8, 2);
            $table->string('source_rating', 50);
            $table->string('status', 50)->default('draft');
            $table->text('pmt_remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('submitted_to_ld_at')->nullable();
            $table->timestamps();

            $table->unique(['ipcr_id', 'performance_period_id'], 'development_plans_ipcr_period_unique');
            $table->index(['performance_period_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_plans');
    }
};
