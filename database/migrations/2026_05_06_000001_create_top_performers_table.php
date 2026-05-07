<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('top_performers', function (Blueprint $table) {
            $table->id();
            $table->string('performer_type', 20);
            $table->unsignedBigInteger('source_record_id');
            $table->foreignId('ipcr_id')->nullable()->constrained('ipcrs')->nullOnDelete();
            $table->foreignId('opcr_id')->nullable()->constrained('opcrs')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->foreignId('performance_period_id')->constrained('performance_periods')->cascadeOnDelete();
            $table->unsignedInteger('rank')->nullable();
            $table->string('performer_name')->nullable();
            $table->string('surname')->nullable();
            $table->string('given_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->string('designation')->nullable();
            $table->string('office_name')->nullable();
            $table->string('department_head_name')->nullable();
            $table->decimal('official_score', 8, 2);
            $table->string('official_rating', 50);
            $table->string('remarks', 255)->nullable();
            $table->dateTime('released_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['performer_type', 'source_record_id', 'performance_period_id'],
                'top_performers_source_period_unique'
            );
            $table->index(['performance_period_id', 'performer_type', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_performers');
    }
};
