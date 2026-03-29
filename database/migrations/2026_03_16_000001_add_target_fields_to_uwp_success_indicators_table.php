<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('uwp_success_indicators')) {
            return;
        }

        Schema::table('uwp_success_indicators', function (Blueprint $table) {
            if (!Schema::hasColumn('uwp_success_indicators', 'target_quantity')) {
                $table->unsignedInteger('target_quantity')->nullable()->after('indicator_text');
            }

            if (!Schema::hasColumn('uwp_success_indicators', 'target_timeline')) {
                $table->text('target_timeline')->nullable()->after('target_quantity');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('uwp_success_indicators')) {
            return;
        }

        Schema::table('uwp_success_indicators', function (Blueprint $table) {
            if (Schema::hasColumn('uwp_success_indicators', 'target_timeline')) {
                $table->dropColumn('target_timeline');
            }

            if (Schema::hasColumn('uwp_success_indicators', 'target_quantity')) {
                $table->dropColumn('target_quantity');
            }
        });
    }
};
