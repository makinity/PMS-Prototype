<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ors_entries')) {
            return;
        }

        $dropColumns = [];
        if (Schema::hasColumn('ors_entries', 'client_request_id')) {
            $dropColumns[] = 'client_request_id';
        }
        if (Schema::hasColumn('ors_entries', 'output_type')) {
            $dropColumns[] = 'output_type';
        }

        if (!empty($dropColumns)) {
            Schema::table('ors_entries', function (Blueprint $table) use ($dropColumns): void {
                $table->dropColumn($dropColumns);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('ors_entries')) {
            return;
        }

        Schema::table('ors_entries', function (Blueprint $table): void {
            if (!Schema::hasColumn('ors_entries', 'client_request_id')) {
                $table->string('client_request_id', 100)->nullable();
            }
            if (!Schema::hasColumn('ors_entries', 'output_type')) {
                $table->string('output_type')->nullable();
            }
        });
    }
};
