<?php

namespace App\Http\Controllers\StageTwo\Planning;

use App\Exports\StageTwo\MporExcelExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class MporExcelExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $payload = $this->buildPayload();

        return Excel::download(
            new MporExcelExport($payload),
            $this->buildFilename($payload, false)
        );
    }

    public function previewExcel(Request $request)
    {
        $payload = $this->buildPayload();

        return Excel::download(
            new MporExcelExport($payload),
            $this->buildFilename($payload, true)
        );
    }

    private function buildPayload(): array
    {
        $qualityRating = 5;
        $timelinessRating = 5;

        return [
            'employee' => 'Ramon Reyes',
            'office' => 'Revenue Collection Unit',
            'month_year' => 'January 2026',
            'supervisor' => 'Carlo D. Beray',
            'core' => [
                $this->makeOutputRow(
                    'Processing of Over-the-Counter Revenue Transactions',
                    12,
                    $qualityRating,
                    $timelinessRating,
                    1
                ),
                $this->makeOutputRow(
                    'E-Bank Scanning and Encoding of Revenue Transactions',
                    1,
                    $qualityRating,
                    $timelinessRating,
                    1
                ),
            ],
            'support' => [
                $this->makeOutputRow(
                    'Maintenance of Revenue Records Filing System',
                    0,
                    $qualityRating,
                    $timelinessRating,
                    1
                ),
            ],
            'attendance' => [
                'absence' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
                'tardiness' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
            ],
        ];
    }

    private function makeOutputRow(
        string $label,
        int $quantity,
        int $qualityRating,
        int $timelinessRating,
        int $week
    ): array {
        $weeks = [];

        foreach ([1, 2, 3, 4] as $weekNumber) {
            $weekQty = $weekNumber === $week ? $quantity : 0;

            $weeks[$weekNumber] = [
                'qty' => $weekQty,
                'q_points' => $weekQty * $qualityRating,
                't_points' => $weekQty * $timelinessRating,
            ];
        }

        return [
            'label' => $label,
            'weeks' => $weeks,
        ];
    }

    private function buildFilename(array $payload, bool $preview): string
    {
        $office = Str::slug((string) ($payload['office'] ?? 'Office'), '_');
        $monthYear = Str::slug((string) ($payload['month_year'] ?? 'Month_Year'), '_');
        $suffix = $preview ? '_Preview' : '';

        return "MPOR_{$office}_{$monthYear}{$suffix}.xlsx";
    }
}

