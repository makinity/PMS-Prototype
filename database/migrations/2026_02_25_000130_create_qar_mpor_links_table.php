<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qar_mpor_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qar_header_id')->constrained('qar_headers')->cascadeOnDelete();
            $table->foreignId('mpor_id')->constrained('mpors')->cascadeOnDelete();
            $table->string('employee_name')->nullable();
            $table->string('month_label')->nullable();
            $table->string('status_label')->nullable();
            $table->timestamps();

            $table->unique(['qar_header_id', 'mpor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qar_mpor_links');
    }
};

