<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'ramonreyes@gmail.com'],
            [
                'employee_id' => 'EMP-2026-00001',
                'name' => 'Ramon Reyes',
                'role' => 'employee',
                'password' => null,          // not activated yet
                'is_active' => false,
                'activated_at' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'carloberay@gmail.com'],
            [
                'employee_id' => 'EMP-2026-00002',
                'name' => 'Carlo D. Beray',
                'role' => 'supervisor',
                'password' => null,          // not activated yet
                'is_active' => false,
                'activated_at' => null,
            ]
        );
    }
}
