<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoUwpReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $offices = [
            1 => 'Revenue Collection Unit',
            2 => 'Records Management Unit',
            3 => 'Administrative Services Unit',
            4 => 'Human Resource Management Unit',
            5 => 'General Services Unit',
            6 => 'Planning and Development Unit',
        ];

        foreach ($offices as $id => $name) {
            DB::table('offices')->updateOrInsert(
                ['id' => $id],
                ['name' => $name, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        DB::table('performance_periods')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'January - June 2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-06-30',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('performance_periods')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'July - December 2026',
                'start_date' => '2026-07-01',
                'end_date' => '2026-12-31',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
