<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ors_entries', function (Blueprint $table) {
            $table->foreignId('mpor_id')
                ->nullable()
                ->after('ipcr_item_id')
                ->constrained('mpors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ors_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('mpor_id');
        });
    }
};
