<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoPassword = Hash::make('password');
        $activatedAt = now();

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'employee_id' => 'EMP-2026-00000',
                'name' => 'System Admin',
                'role' => 'admin',
                'password' => $demoPassword,
                'is_active' => true,
                'activated_at' => $activatedAt,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pmt@gmail.com'],
            [
                'employee_id' => 'EMP-2026-00003',
                'name' => 'PMT Reviewer',
                'role' => 'pmt',
                'password' => $demoPassword,
                'is_active' => true,
                'activated_at' => $activatedAt,
            ]
        );

        User::updateOrCreate(
            ['email' => 'depthead@gmail.com'],
            [
                'employee_id' => 'EMP-2026-00004',
                'name' => 'Department Head',
                'role' => 'dept-head',
                'password' => $demoPassword,
                'is_active' => true,
                'activated_at' => $activatedAt,
            ]
        );

        User::updateOrCreate(
            ['email' => 'ramonreyes@gmail.com'],
            [
                'employee_id' => 'EMP-2026-00001',
                'name' => 'Ramon Reyes',
                'role' => 'employee',
                'password' => $demoPassword,
                'is_active' => true,
                'activated_at' => $activatedAt,
            ]
        );

        User::updateOrCreate(
            ['email' => 'carloberay@gmail.com'],
            [
                'employee_id' => 'EMP-2026-00002',
                'name' => 'Carlo D. Beray',
                'role' => 'supervisor',
                'password' => $demoPassword,
                'is_active' => true,
                'activated_at' => $activatedAt,
            ]
        );
    }
}
