<?php

namespace App\Console\Commands;

use App\Services\HmsUserImportService;
use Illuminate\Console\Command;

class ImportHmsUsers extends Command
{
    protected $signature = 'pms:import-hms-users {--demo : Use demo HMS payload}';

    protected $description = 'Import HMS-provided users and email their employee IDs.';

    public function __construct(
        protected HmsUserImportService $importService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->option('demo')) {
            $this->warn('No payload provided. Pass --demo to run the demo import.');
            return 0;
        }

        $payload = [
            [
                'employee_id' => 'EMP-2026-00001',
                'name' => 'Ramon Reyes',
                'email' => 'ramonreyes@gmail.com',
                'role' => 'employee',
            ],
            [
                'employee_id' => 'EMP-2026-00002',
                'name' => 'Carlo D. Beray',
                'email' => 'carloberay@gmail.com',
                'role' => 'supervisor',
            ],
        ];

        $summary = $this->importService->import($payload);

        $this->line("Processed: {$summary['total_processed']}");
        $this->line("Created: {$summary['total_created']}");
        $this->line("Updated: {$summary['total_updated']}");
        $this->line("Emailed: {$summary['total_emailed']}");

        if (! empty($summary['failures'])) {
            $this->line('Failures:');
            foreach ($summary['failures'] as $failure) {
                $employeeId = $failure['employee_id'] ?? 'N/A';
                $email = $failure['email'] ?? 'N/A';
                $message = $failure['message'] ?? 'Unknown error';
                $this->line("  - [{$employeeId} / {$email}] {$message}");
            }
        }

        $this->info('HMS import complete.');

        return 0;
    }
}
