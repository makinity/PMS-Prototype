<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unit_work_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('office_id')->constrained('offices');
            $table->foreignId('performance_period_id')->constrained('performance_periods');
            $table->foreignId('created_by')->constrained('users'); // supervisor

            $table->enum('status', ['draft', 'submitted', 'endorsed', 'pmt_approved'])->default('draft');

            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('endorsed_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('locked_at')->nullable();

            $table->timestamps();

            // One UWP per office per period
            $table->unique(['office_id', 'performance_period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_work_plans');
    }
};
