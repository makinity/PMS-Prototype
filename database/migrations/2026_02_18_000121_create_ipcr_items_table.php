<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ipcr_items')) {
            return;
        }

        Schema::create('ipcr_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ipcr_id')->constrained('ipcrs')->cascadeOnDelete();
            $table->string('output_title');
            $table->string('function_type')->nullable();
            $table->text('indicator_text');
            $table->text('target_summary')->nullable();
            $table->json('standards_payload')->nullable();
            $table->timestamps();

            $table->index('ipcr_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipcr_items');
    }
};
