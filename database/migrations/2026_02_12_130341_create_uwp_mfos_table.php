<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uwp_mfos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('uwp_function_id')->constrained('uwp_functions')->cascadeOnDelete();

            $table->text('title');
            $table->text('target_timeline')->nullable();
            $table->decimal('weight_percent', 5, 2)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['uwp_function_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uwp_mfos');
    }
};
