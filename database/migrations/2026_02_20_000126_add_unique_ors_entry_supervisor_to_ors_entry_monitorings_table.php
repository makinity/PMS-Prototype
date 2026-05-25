<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ors_entry_monitorings')) {
            return;
        }

        $indexNames = $this->indexNames();

        if ($indexNames->contains('ors_entry_monitorings_ors_entry_id_unique')) {
            if (!$indexNames->contains('ors_entry_monitorings_ors_entry_id_index')) {
                Schema::table('ors_entry_monitorings', function (Blueprint $table) {
                    $table->index('ors_entry_id', 'ors_entry_monitorings_ors_entry_id_index');
                });
            }

            Schema::table('ors_entry_monitorings', function (Blueprint $table) {
                $table->dropUnique('ors_entry_monitorings_ors_entry_id_unique');
            });
        }

        if (!$indexNames->contains('ors_entry_monitorings_ors_entry_id_supervisor_id_unique')) {
            Schema::table('ors_entry_monitorings', function (Blueprint $table) {
                $table->unique(['ors_entry_id', 'supervisor_id'], 'ors_entry_monitorings_ors_entry_id_supervisor_id_unique');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('ors_entry_monitorings')) {
            return;
        }

        $indexNames = $this->indexNames();

        if (!$indexNames->contains('ors_entry_monitorings_ors_entry_id_index')) {
            Schema::table('ors_entry_monitorings', function (Blueprint $table) {
                $table->index('ors_entry_id', 'ors_entry_monitorings_ors_entry_id_index');
            });
        }

        if ($indexNames->contains('ors_entry_monitorings_ors_entry_id_supervisor_id_unique')) {
            Schema::table('ors_entry_monitorings', function (Blueprint $table) {
                $table->dropUnique('ors_entry_monitorings_ors_entry_id_supervisor_id_unique');
            });
        }

        if (!$indexNames->contains('ors_entry_monitorings_ors_entry_id_unique')) {
            Schema::table('ors_entry_monitorings', function (Blueprint $table) {
                $table->unique('ors_entry_id', 'ors_entry_monitorings_ors_entry_id_unique');
            });
        }

        $indexNames = $this->indexNames();

        if ($indexNames->contains('ors_entry_monitorings_ors_entry_id_index')) {
            Schema::table('ors_entry_monitorings', function (Blueprint $table) {
                $table->dropIndex('ors_entry_monitorings_ors_entry_id_index');
            });
        }
    }

    private function indexNames()
    {
        if (DB::getDriverName() === 'sqlite') {
            return collect(DB::select("PRAGMA index_list('ors_entry_monitorings')"))
                ->pluck('name')
                ->unique()
                ->values();
        }

        return collect(DB::select('SHOW INDEX FROM ors_entry_monitorings'))
            ->pluck('Key_name')
            ->unique()
            ->values();
    }
};
