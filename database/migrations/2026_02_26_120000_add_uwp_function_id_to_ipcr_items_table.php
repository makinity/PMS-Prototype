<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ipcr_items') || Schema::hasColumn('ipcr_items', 'uwp_function_id')) {
            return;
        }

        Schema::table('ipcr_items', function (Blueprint $table) {
            $table->foreignId('uwp_function_id')
                ->nullable()
                ->after('ipcr_id')
                ->index()
                ->constrained('uwp_functions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ipcr_items') || !Schema::hasColumn('ipcr_items', 'uwp_function_id')) {
            return;
        }

        try {
            Schema::table('ipcr_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('uwp_function_id');
            });
        } catch (\Throwable $e) {
            Schema::table('ipcr_items', function (Blueprint $table) {
                $table->dropForeign(['uwp_function_id']);
                $table->dropColumn('uwp_function_id');
            });
        }
    }
};
