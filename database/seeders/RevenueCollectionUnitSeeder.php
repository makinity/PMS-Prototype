<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RevenueCollectionUnitSeeder extends Seeder
{
    public function run(): void
    {
        // Create Revenue Collection Unit office
        $revenueOffice = Office::firstOrCreate(
            ['name' => 'Revenue Collection Unit'],
            [
                'code' => 'RCU',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("✅ Created/Found Revenue Collection Unit (ID: {$revenueOffice->id})");

        // Find or create Carlo D. Beray
        $carlo = User::firstOrCreate(
            ['email' => 'carloberay@gmail.com'],
            [
                'name' => 'Carlo D. Beray',
                'password' => Hash::make('password'),
                'role' => 'supervisor',
                'employee_id' => 'EMP-2026-00002',
                'is_active' => true,
                'activated_at' => now(),
                'position' => 'Office Supervisor',
                'office_id' => $revenueOffice->id,
            ]
        );

        // Update Carlo if he already exists
        if (!$carlo->wasRecentlyCreated) {
            $carlo->update([
                'role' => 'supervisor',
                'position' => 'Office Supervisor',
                'office_id' => $revenueOffice->id,
                'is_active' => true,
            ]);
        }

        $this->command->info("✅ Updated/Created Carlo D. Beray (ID: {$carlo->id})");

        // Make Carlo the head of Revenue Collection Unit
        $revenueOffice->head_id = $carlo->id;
        $revenueOffice->save();

        $this->command->info("✅ Made {$carlo->name} the head of {$revenueOffice->name}");

        // Verify the setup
        $this->command->line('');
        $this->command->info('=== VERIFICATION ===');
        $this->command->line("Office: {$revenueOffice->name} (ID: {$revenueOffice->id})");
        $this->command->line("Head: " . ($revenueOffice->head->name ?? 'None'));
        $this->command->line("Supervisor: {$carlo->name}");
        $this->command->line("Supervisor Office ID: {$carlo->office_id}");
        $this->command->line("Supervisor Role: {$carlo->role}");
        $this->command->line("Supervisor Position: {$carlo->position}");
        $this->command->line('');
    }
}
