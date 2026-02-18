<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Exports\StageTwo\SmporExcelExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SmporExcelExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $payload = $this->buildPayload();

        return Excel::download(
            new SmporExcelExportController($payload),
            $this->buildFilename($payload, false)
        );
    }

    public function previewExcel(Request $request)
    {
        $payload = $this->buildPayload();

        return Excel::download(
            new SmporExcelExport($payload),
            $this->buildFilename($payload, true)
        );
    }

    private function buildPayload(): array
    {
        return [
            'name' => 'Juan Dela Cruz',
            'office' => 'Revenue Collection Unit',
            'semestral_period' => 'January-June 2026',
            'supervisor' => 'Carlo D. Beray',
            'department_head' => 'Maria Teresa M. Lopez',
            'employee' => 'Juan Dela Cruz',

            'core' => [
                $this->makeOutputRow(
                    'E-Bank Scanning and Encoding of Revenue Transactions',
                    [
                        'jan' => ['qty' => 12, 'q_points' => 60, 't_points' => 60],
                    ]
                ),
                $this->makeOutputRow(
                    'Processing of Over-the-Counter Revenue Transactions',
                    [
                        'jan' => ['qty' => 1, 'q_points' => 5, 't_points' => 5],
                    ]
                ),
            ],

            'support' => [
                $this->makeOutputRow(
                    'Maintenance of Revenue Records Filing System',
                    []
                ),
            ],

            'attendance' => [
                'absence' => [
                    'jan' => 0,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                    'total' => 0,
                ],
                'tardiness' => [
                    'jan' => 0,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                    'total' => 0,
                ],
            ],
        ];
    }

    /**
     * $monthValues supports either:
     *  - ['jan' => 12]  (qty only)
     *  - ['jan' => ['qty'=>12,'q_points'=>60,'t_points'=>60]] (explicit per-band values)
     */
    private function makeOutputRow(string $label, array $monthValues): array
    {
        $months = [];
        $keys = ['jan', 'feb', 'mar', 'apr', 'may', 'jun'];

        foreach ($keys as $key) {
            $value = $monthValues[$key] ?? 0;

            if (is_array($value)) {
                $qty = (int) ($value['qty'] ?? 0);
                $qPoints = (int) ($value['q_points'] ?? 0);
                $tPoints = (int) ($value['t_points'] ?? 0);
            } else {
                $qty = (int) $value;
                $qPoints = 0;
                $tPoints = 0;
            }

            $months[$key] = [
                'qty' => $qty,
                'q_points' => $qPoints,
                't_points' => $tPoints,
            ];
        }

        return [
            'label' => $label,
            'months' => $months,
        ];
    }

    private function buildFilename(array $payload, bool $preview): string
    {
        $office = Str::slug((string) ($payload['office'] ?? 'Office'), '_');
        $period = Str::slug((string) ($payload['semestral_period'] ?? 'Semestral_Period'), '_');
        $suffix = $preview ? '_Preview' : '';

        return "SMPOR_{$office}_{$period}{$suffix}.xlsx";
    }
}
