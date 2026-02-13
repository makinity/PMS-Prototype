<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class OfficeRelationshipSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Setting up office relationships...');

        // Check which columns exist in users table
        $userColumns = Schema::getColumnListing('users');
        $hasPosition = in_array('position', $userColumns);
        $hasEmployeeId = in_array('employee_id', $userColumns);
        $hasOfficeId = in_array('office_id', $userColumns);
        $hasIsActive = in_array('is_active', $userColumns);

        $this->command->info('User columns available: ' . implode(', ', $userColumns));

        // Get or create offices
        $offices = Office::all();

        if ($offices->isEmpty()) {
            $this->command->warn('No offices found. Creating sample offices...');

            $officeData = [
                ['name' => 'Records Management Unit', 'code' => 'RMU'],
                ['name' => 'Human Resources Division', 'code' => 'HRD'],
                ['name' => 'Finance Division', 'code' => 'FIN'],
                ['name' => 'Administrative Office', 'code' => 'ADM'],
                ['name' => 'Information Technology Unit', 'code' => 'ITU'],
            ];

            foreach ($officeData as $data) {
                $office = Office::create($data);
                $offices->push($office);
                $this->command->info("Created office: {$office->name}");
            }
        } else {
            $this->command->info('Found ' . $offices->count() . ' existing offices.');
        }

        // Get or create supervisors
        $supervisors = User::where('role', 'supervisor')->get();

        if ($supervisors->isEmpty()) {
            $this->command->warn('No supervisors found. Creating sample supervisors...');

            foreach ($offices as $index => $office) {
                $userData = [
                    'name' => 'Supervisor ' . ($index + 1),
                    'email' => 'supervisor' . ($index + 1) . '@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'supervisor',
                    'office_id' => $office->id,
                ];

                // Only add these columns if they exist
                if ($hasEmployeeId) {
                    $userData['employee_id'] = 'SUP-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                }

                if ($hasIsActive) {
                    $userData['is_active'] = true;
                }

                if ($hasPosition) {
                    $userData['position'] = 'Office Supervisor';
                }

                $supervisor = User::create($userData);
                $supervisors->push($supervisor);
                $this->command->info("Created supervisor: {$supervisor->name} for {$office->name}");
            }
        } else {
            $this->command->info('Found ' . $supervisors->count() . ' existing supervisors.');

            // Assign existing supervisors to offices if they don't have one
            foreach ($supervisors as $index => $supervisor) {
                if (!$supervisor->office_id && $offices->count() > 0) {
                    $officeIndex = $index % $offices->count();
                    $office = $offices[$officeIndex];

                    $supervisor->office_id = $office->id;

                    if ($hasPosition && !$supervisor->position) {
                        $supervisor->position = 'Office Supervisor';
                    }

                    $supervisor->save();

                    $this->command->info("Assigned {$supervisor->name} to {$office->name}");
                }
            }
        }

        // Set office heads (first supervisor of each office)
        foreach ($offices as $office) {
            if (!$office->head_id) {
                $officeSupervisor = User::where('office_id', $office->id)
                    ->where('role', 'supervisor')
                    ->first();

                if ($officeSupervisor) {
                    $office->head_id = $officeSupervisor->id;
                    $office->save();

                    $this->command->info("Set {$officeSupervisor->name} as head of {$office->name}");
                }
            }
        }

        // Create department head
        $deptHead = User::where('role', 'dept-head')->first();
        if (!$deptHead) {
            $userData = [
                'name' => 'Department Head',
                'email' => 'dept.head@example.com',
                'password' => Hash::make('password'),
                'role' => 'dept-head',
            ];

            if ($hasEmployeeId) {
                $userData['employee_id'] = 'DH-001';
            }

            if ($hasIsActive) {
                $userData['is_active'] = true;
            }

            if ($hasPosition) {
                $userData['position'] = 'Department Head';
            }

            $deptHead = User::create($userData);
            $this->command->info("Created department head: {$deptHead->name}");
        }

        // Create admin if none exists
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $userData = [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ];

            if ($hasEmployeeId) {
                $userData['employee_id'] = 'ADMIN-001';
            }

            if ($hasIsActive) {
                $userData['is_active'] = true;
            }

            if ($hasPosition) {
                $userData['position'] = 'System Administrator';
            }

            $admin = User::create($userData);
            $this->command->info("Created admin: {$admin->name}");
        }

        $this->command->info('Office relationships setup completed!');
    }
}
