<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ors_entry_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ors_entry_id')
                ->unique()
                ->constrained('ors_entries')
                ->cascadeOnDelete();
            $table->foreignId('supervisor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('quality_rating')->nullable();
            $table->unsignedTinyInteger('timeliness_rating')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('rated_at')->nullable();
            $table->timestamps();

            $table->index(['supervisor_id', 'rated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ors_entry_monitorings');
    }
};

