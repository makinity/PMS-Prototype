<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('opcrs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_work_plan_id')->constrained('unit_work_plans');
            $table->foreignId('generated_by')->constrained('users'); // admin

            $table->enum('status', ['for_dept_head_review', 'approved'])->default('for_dept_head_review');

            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('locked_at')->nullable();

            $table->timestamps();

            // One OPCR per UWP
            $table->unique(['unit_work_plan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opcrs');
    }
};
