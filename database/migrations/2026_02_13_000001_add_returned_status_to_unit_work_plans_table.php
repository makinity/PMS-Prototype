<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE unit_work_plans MODIFY status ENUM('draft','submitted','endorsed','pmt_approved','returned') NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE unit_work_plans MODIFY status ENUM('draft','submitted','endorsed','pmt_approved') NOT NULL DEFAULT 'draft'");
        }
    }
};
