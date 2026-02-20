<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('office_id')->index();
            $table->string('month', 7)->index();
            $table->string('status')->default('draft');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpors');
    }
};
