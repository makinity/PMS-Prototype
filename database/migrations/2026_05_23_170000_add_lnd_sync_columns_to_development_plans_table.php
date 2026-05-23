<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('development_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('development_plans', 'lnd_sync_status')) {
                $table->string('lnd_sync_status', 30)->default('not_sent')->after('submitted_to_ld_at');
            }
            if (!Schema::hasColumn('development_plans', 'lnd_reference_id')) {
                $table->string('lnd_reference_id')->nullable()->after('lnd_sync_status');
            }
            if (!Schema::hasColumn('development_plans', 'lnd_synced_at')) {
                $table->timestamp('lnd_synced_at')->nullable()->after('lnd_reference_id');
            }
            if (!Schema::hasColumn('development_plans', 'lnd_last_error')) {
                $table->text('lnd_last_error')->nullable()->after('lnd_synced_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('development_plans', function (Blueprint $table) {
            $drop = [];
            foreach (['lnd_last_error', 'lnd_synced_at', 'lnd_reference_id', 'lnd_sync_status'] as $column) {
                if (Schema::hasColumn('development_plans', $column)) {
                    $drop[] = $column;
                }
            }
            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};

