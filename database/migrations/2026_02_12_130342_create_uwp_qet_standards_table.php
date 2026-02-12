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

            $table->enum('dimension', ['q', 'e', 't']);
            $table->unsignedTinyInteger('rating'); // 1..5
            $table->text('standard_text')->nullable();

            $table->timestamps();

            $table->unique(['uwp_success_indicator_id', 'dimension', 'rating'], 'uq_indicator_dimension_rating');
            $table->index(['uwp_success_indicator_id', 'dimension']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uwp_qet_standards');
    }
};
