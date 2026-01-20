<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class MporExportController extends Controller
{
    public function exportPdf()
    {
        // Stage II monitoring copy; derived from submitted ORS entries
        $coreEntries = [
            [
                'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'week1' => 0,
                'week2' => 0,
                'week3' => 1, // Jan 19, 2026
                'week4' => 0,
            ],
            [
                'output' => 'Processing of Over-the-Counter Revenue Transactions',
                'week1' => 1, // Jan 3, 2026
                'week2' => 0,
                'week3' => 0,
                'week4' => 0,
            ],
        ];

        $supportEntries = [
            [
                'output' => 'Maintenance of Revenue Records Filing System',
                'week1' => 0,
                'week2' => 0,
                'week3' => 0,
                'week4' => 0,
            ],
        ];

        $computeTotals = function (&$entries) {
            foreach ($entries as &$entry) {
                $entry['total'] =
                    ($entry['week1'] ?? 0) +
                    ($entry['week2'] ?? 0) +
                    ($entry['week3'] ?? 0) +
                    ($entry['week4'] ?? 0);
            }
            return $entries;
        };

        $coreEntries = $computeTotals($coreEntries);
        $supportEntries = $computeTotals($supportEntries);

        $summary = [
            'core_total' => array_sum(array_column($coreEntries, 'total')), // 2
            'support_total' => array_sum(array_column($supportEntries, 'total')), // 0
        ];
        $summary['overall_total'] = $summary['core_total'] + $summary['support_total']; // 2

        $mpor = [
            'employee' => 'Ramon Reyes',
            'office' => 'Revenue Collection Unit',
            'month' => 'January 2026',
            'core' => $coreEntries,
            'support' => $supportEntries,
            'summary' => $summary,
            'attendance' => [
                'absence' => ['week1' => 0, 'week2' => 0, 'week3' => 0, 'week4' => 0],
                'tardiness' => ['week1' => 0, 'week2' => 0, 'week3' => 0, 'week4' => 0],
            ],
            'rating_note' => 'Ratings are completed during Stage III – Performance Review.',
            'footer_note' => 'This is a system-generated monitoring report derived from ORS. Validation, SMPOR generation, and performance rating occur in Stage III.',
        ];

        $pdf = Pdf::loadView('pdf.stage-two.mpor', compact('mpor'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('MPOR_January_2026.pdf');
    }
}
