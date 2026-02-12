<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uwp_indicator_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('uwp_success_indicator_id')
                ->constrained('uwp_success_indicators')
                ->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('assigned_at')->nullable();

            $table->timestamps();

            $table->unique(['uwp_success_indicator_id', 'employee_id'], 'uq_uwp_indicator_employee');
            $table->index(['employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uwp_indicator_assignments');
    }
};
