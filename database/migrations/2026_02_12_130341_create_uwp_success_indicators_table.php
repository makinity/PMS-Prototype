<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uwp_success_indicators', function (Blueprint $table) {
            $table->id();

            $table->foreignId('uwp_mfo_id')->constrained('uwp_mfos')->cascadeOnDelete();

            $table->text('indicator_text');
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('uwp_mfo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uwp_success_indicators');
    }
};
