<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ipcrs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('opcr_id')->constrained('opcrs')->cascadeOnDelete();

            // Optional but useful for filtering (still traceable through opcr)
            $table->foreignId('unit_work_plan_id')->constrained('unit_work_plans');

            $table->foreignId('employee_id')->constrained('users');

            $table->enum('status', ['generated', 'committed'])->default('generated');
            $table->dateTime('committed_at')->nullable();
            $table->dateTime('locked_at')->nullable();

            $table->timestamps();

            // One IPCR per employee per OPCR
            $table->unique(['opcr_id', 'employee_id'], 'uq_opcr_employee');
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipcrs');
    }
};
