<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('my_tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ors_entry_id')->unique()->constrained('ors_entries')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->foreignId('performance_period_id')->constrained('performance_periods')->cascadeOnDelete();
            $table->foreignId('ipcr_id')->constrained('ipcrs')->cascadeOnDelete();
            $table->foreignId('ipcr_item_id')->constrained('ipcr_items')->cascadeOnDelete();

            $table->date('work_date')->index();
            $table->string('client_request_id')->nullable();
            $table->string('output_type')->nullable();
            $table->text('notes')->nullable();
            $table->string('quantity')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->unsignedInteger('total_seconds')->default(0);

            $table->string('status')->default('draft')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->boolean('has_evidence')->default(false);

            $table->timestamps();

            $table->index(['employee_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('my_tasks');
    }
};
