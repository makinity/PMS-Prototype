<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qar_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qar_header_id')->constrained('qar_headers')->cascadeOnDelete();
            $table->string('ppa_code', 50);
            $table->string('mfo_title');
            $table->text('indicator_text');
            $table->string('target_timeline')->nullable();
            $table->decimal('actual_performance', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['qar_header_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qar_rows');
    }
};

