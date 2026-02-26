<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smpor_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('smpor_id')
                ->constrained('smpors')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('overall_score', 5, 2)->nullable();
            $table->decimal('quality_avg', 5, 2)->nullable();
            $table->decimal('timeliness_avg', 5, 2)->nullable();
            $table->string('adjectival_rating', 50)->nullable();

            $table->timestamps();

            $table->unique(['smpor_id', 'employee_id'], 'smpor_items_smpor_employee_uq');
            $table->index('employee_id', 'smpor_items_employee_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smpor_items');
    }
};
