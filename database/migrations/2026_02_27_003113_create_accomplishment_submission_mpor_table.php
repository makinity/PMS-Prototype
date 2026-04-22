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
        Schema::create('accomplishment_submission_mpor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accomplishment_submission_id');
            $table->unsignedBigInteger('mpor_id');

            $table->foreign('accomplishment_submission_id', 'acc_sub_mpor_sub_fk')
                ->references('id')->on('accomplishment_submissions')
                ->onDelete('cascade');

            $table->foreign('mpor_id', 'acc_sub_mpor_mpor_fk')
                ->references('id')->on('mpors')
                ->onDelete('cascade');

            $table->unique(['accomplishment_submission_id', 'mpor_id'], 'acc_sub_mpor_uq');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accomplishment_submission_mpor');
    }
};
