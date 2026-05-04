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
            $table->string('status', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opcrs', function (Blueprint $table) {
            // Reverting to enum might be tricky depending on the DB driver, 
            // but we'll try to restore the original two.
            $table->enum('status', ['for_dept_head_review', 'approved'])->default('for_dept_head_review')->change();
        });
    }
};
