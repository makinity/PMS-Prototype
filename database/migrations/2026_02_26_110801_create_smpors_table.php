<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smpors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('qar_header_id')
                ->unique()
                ->constrained('qar_headers')
                ->cascadeOnDelete();

            $table->foreignId('office_id')
                ->constrained('offices')
                ->restrictOnDelete();

            $table->foreignId('performance_period_id')
                ->constrained('performance_periods')
                ->restrictOnDelete();

            $table->string('quarter_key', 20);
            $table->timestamp('generated_at')->nullable();

            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('avg_quality', 5, 2)->nullable();
            $table->decimal('avg_timeliness', 5, 2)->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->string('adjectival_rating', 50)->nullable();

            $table->timestamps();

            $table->index(['office_id', 'performance_period_id', 'quarter_key'], 'smpors_office_period_quarter_idx');
            $table->index('performance_period_id', 'smpors_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smpors');
    }
};
