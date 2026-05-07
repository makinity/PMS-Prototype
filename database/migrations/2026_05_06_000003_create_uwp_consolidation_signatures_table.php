<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uwp_consolidation_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_work_plan_id')->constrained('unit_work_plans')->cascadeOnDelete();
            $table->foreignId('opcr_id')->nullable()->constrained('opcrs')->nullOnDelete();
            $table->foreignId('signed_by')->constrained('users')->cascadeOnDelete();
            $table->string('signature_image_path');
            $table->string('signed_excel_path');
            $table->string('signature_hash', 64);
            $table->dateTime('signed_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['unit_work_plan_id', 'signed_at']);
            $table->index(['opcr_id', 'signed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uwp_consolidation_signatures');
    }
};
