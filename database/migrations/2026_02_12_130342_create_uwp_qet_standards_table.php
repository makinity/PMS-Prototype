<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uwp_qet_standards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('uwp_success_indicator_id')
                ->constrained('uwp_success_indicators')
                ->cascadeOnDelete();

            $table->enum('dimension', ['quality', 'efficiency', 'timeliness']);
            $table->unsignedTinyInteger('rating_level'); // 1..5
            $table->text('standard');

            $table->timestamps();

            // Prevent duplicates like (indicator + quality + rating 5)
            $table->unique(['uwp_success_indicator_id', 'dimension', 'rating_level'], 'uq_indicator_dimension_level');

            $table->index(['uwp_success_indicator_id', 'dimension']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uwp_qet_standards');
    }
};
