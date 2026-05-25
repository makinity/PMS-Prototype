<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $table = 'ipcrs';

    private function indexExists(string $indexName): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return collect(DB::select("PRAGMA index_list('{$this->table}')"))
                ->pluck('name')
                ->contains($indexName);
        }

        $dbName = DB::getDatabaseName();

        $count = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', $this->table)
            ->where('index_name', $indexName)
            ->count();

        return $count > 0;
    }

    private function foreignKeyExists(string $constraintName): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return false;
        }

        $dbName = DB::getDatabaseName();

        $count = DB::table('information_schema.table_constraints')
            ->where('constraint_schema', $dbName)
            ->where('table_name', $this->table)
            ->where('constraint_name', $constraintName)
            ->where('constraint_type', 'FOREIGN KEY')
            ->count();

        return $count > 0;
    }

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            if (!Schema::hasColumn($this->table, 'finalized_from_qar_header_id')) {
                $table->unsignedBigInteger('finalized_from_qar_header_id')->nullable()->after('locked_at');
            }
            if (!Schema::hasColumn($this->table, 'finalized_at')) {
                $table->timestamp('finalized_at')->nullable()->after('finalized_from_qar_header_id');
            }
            if (!Schema::hasColumn($this->table, 'final_score')) {
                $table->decimal('final_score', 5, 2)->nullable()->after('finalized_at');
            }
            if (!Schema::hasColumn($this->table, 'adjectival_rating')) {
                $table->string('adjectival_rating', 50)->nullable()->after('final_score');
            }
        });

        // Add index only if missing
        if (!$this->indexExists('ipcrs_finalized_from_qar_header_id_idx')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->index('finalized_from_qar_header_id', 'ipcrs_finalized_from_qar_header_id_idx');
            });
        }

        // Add FK only if missing
        if (DB::getDriverName() !== 'sqlite' && !$this->foreignKeyExists('ipcrs_finalized_from_qar_header_fk')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->foreign('finalized_from_qar_header_id', 'ipcrs_finalized_from_qar_header_fk')
                    ->references('id')
                    ->on('qar_headers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Drop FK if it exists
        if (DB::getDriverName() !== 'sqlite' && $this->foreignKeyExists('ipcrs_finalized_from_qar_header_fk')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropForeign('ipcrs_finalized_from_qar_header_fk');
            });
        }

        // Drop index if it exists
        if ($this->indexExists('ipcrs_finalized_from_qar_header_id_idx')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropIndex('ipcrs_finalized_from_qar_header_id_idx');
            });
        }

        Schema::table($this->table, function (Blueprint $table) {
            $drop = [];
            foreach (['finalized_from_qar_header_id', 'finalized_at', 'final_score', 'adjectival_rating'] as $col) {
                if (Schema::hasColumn($this->table, $col)) {
                    $drop[] = $col;
                }
            }

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
