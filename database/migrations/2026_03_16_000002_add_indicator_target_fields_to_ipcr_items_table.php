<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ipcr_items')) {
            return;
        }

        Schema::table('ipcr_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ipcr_items', 'uwp_success_indicator_id')) {
                $table->foreignId('uwp_success_indicator_id')
                    ->nullable()
                    ->after('uwp_function_id')
                    ->constrained('uwp_success_indicators')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('ipcr_items', 'target_quantity')) {
                $table->unsignedInteger('target_quantity')->nullable()->after('indicator_text');
            }

            if (!Schema::hasColumn('ipcr_items', 'target_timeline')) {
                $table->text('target_timeline')->nullable()->after('target_quantity');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ipcr_items')) {
            return;
        }

        Schema::table('ipcr_items', function (Blueprint $table) {
            if (Schema::hasColumn('ipcr_items', 'target_timeline')) {
                $table->dropColumn('target_timeline');
            }

            if (Schema::hasColumn('ipcr_items', 'target_quantity')) {
                $table->dropColumn('target_quantity');
            }
        });

        if (!Schema::hasTable('ipcr_items') || !Schema::hasColumn('ipcr_items', 'uwp_success_indicator_id')) {
            return;
        }

        try {
            Schema::table('ipcr_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('uwp_success_indicator_id');
            });
        } catch (\Throwable) {
            Schema::table('ipcr_items', function (Blueprint $table) {
                $table->dropForeign(['uwp_success_indicator_id']);
                $table->dropColumn('uwp_success_indicator_id');
            });
        }
    }
};
