<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uwp_mfos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_work_plan_id')->constrained('unit_work_plans')->cascadeOnDelete();
            $table->foreignId('function_id')->constrained('functions');

            $table->string('title');
            $table->text('target_summary')->nullable(); // e.g., Daily; same working day
            $table->decimal('weight', 5, 2)->default(0.00);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['unit_work_plan_id', 'function_id']);
            $table->unique(['unit_work_plan_id', 'title']); // optional but clean for demo
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uwp_mfos');
    }
};
