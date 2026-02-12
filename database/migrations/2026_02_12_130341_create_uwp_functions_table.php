<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uwp_functions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_work_plan_id')->constrained('unit_work_plans')->cascadeOnDelete();
            $table->string('name'); // e.g. Core Functions
            $table->enum('function_type', ['core', 'support', 'custom'])->default('custom');
            $table->decimal('weight_percent', 5, 2)->default(0.00);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['unit_work_plan_id', 'function_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uwp_functions');
    }
};
