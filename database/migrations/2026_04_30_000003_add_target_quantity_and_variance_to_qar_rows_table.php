<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qar_rows', function (Blueprint $table) {
            if (!Schema::hasColumn('qar_rows', 'target_quantity')) {
                $table->decimal('target_quantity', 12, 2)->nullable()->after('indicator_text');
            }

            if (!Schema::hasColumn('qar_rows', 'variance')) {
                $table->decimal('variance', 12, 2)->nullable()->after('actual_performance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('qar_rows', function (Blueprint $table) {
            if (Schema::hasColumn('qar_rows', 'variance')) {
                $table->dropColumn('variance');
            }

            if (Schema::hasColumn('qar_rows', 'target_quantity')) {
                $table->dropColumn('target_quantity');
            }
        });
    }
};
