<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('opcrs', function (Blueprint $table) {
            $table->decimal('final_score', 8, 2)->nullable();
            $table->string('adjectival_rating')->nullable();
            
            $table->decimal('pmt_adjusted_score', 8, 2)->nullable();
            $table->string('pmt_adjusted_rating')->nullable();
            $table->text('pmt_adjustment_reason')->nullable();
            $table->text('pmt_remarks')->nullable();
            $table->foreignId('pmt_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('pmt_reviewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opcrs', function (Blueprint $table) {
            $table->dropForeign(['pmt_reviewed_by']);
            $table->dropColumn([
                'final_score',
                'adjectival_rating',
                'pmt_adjusted_score',
                'pmt_adjusted_rating',
                'pmt_adjustment_reason',
                'pmt_remarks',
                'pmt_reviewed_by',
                'pmt_reviewed_at',
            ]);
        });
    }
};
