<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class MporExportController extends Controller
{
    public function exportPdf()
    {
        $mpor = $this->buildMporDemoData();

        $pdf = Pdf::loadView('pdf.stage-two.mpor', compact('mpor'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('MPOR_January_2026_Preview.pdf');
    }

    public function preview()
    {
        $mpor = $this->buildMporDemoData();

        $pdf = Pdf::loadView('pdf.stage-two.mpor', compact('mpor'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('MPOR_January_2026_Preview.pdf');
    }

    /**
     * Stage II DEMO LOCKED DATASET — derived from Submitted (Locked) + Supervisor-rated ORS entries.
     *
     * Employee entries (Week 1 only):
     * 1) Processing of Over-the-Counter Revenue Transactions
     *    - Qty: 12 transactions
     *    - Q=5, T=5 => Q points=60, T points=60
     * 2) E-Bank Scanning and Encoding of Revenue Transactions
     *    - Qty: 1 daily batch
     *    - Q=5, T=5 => Q points=5, T points=5
     */
    private function buildMporDemoData(): array
    {
        $employee = 'Ramon Reyes';
        $office = 'Revenue Collection Unit';
        $month = 'January 2026';

        $supervisor = 'Carlo D. Beray';

        // Locked supervisor ratings (demo rule)
        $qualityRating = 5;
        $timelinessRating = 5;

        // CORE FUNCTIONS rows
        $core = [
            $this->makeRow(
                'Processing of Over-the-Counter Revenue Transactions',
                qtyWeek1: 12,
                qualityRating: $qualityRating,
                timelinessRating: $timelinessRating
            ),
            $this->makeRow(
                'E-Bank Scanning and Encoding of Revenue Transactions',
                qtyWeek1: 1,
                qualityRating: $qualityRating,
                timelinessRating: $timelinessRating
            ),
        ];

        // SUPPORT FUNCTIONS rows (all zeros for demo)
        $support = [
            $this->makeRow(
                'Maintenance of Revenue Records Filing System',
                qtyWeek1: 0,
                qualityRating: $qualityRating,
                timelinessRating: $timelinessRating
            ),
        ];

        $coreTotals = $this->computeSectionTotals($core);
        $supportTotals = $this->computeSectionTotals($support);

        $grandTotals = [
            'qty' => $this->sumBands($coreTotals['qty'], $supportTotals['qty']),
            'qual' => $this->sumBands($coreTotals['qual'], $supportTotals['qual']),
            'time' => $this->sumBands($coreTotals['time'], $supportTotals['time']),
        ];

        return [
            'employee' => $employee,
            'office' => $office,
            'month' => $month,
            'supervisor_name' => $supervisor,

            // Rows
            'core' => $core,
            'support' => $support,

            // Totals (Quantity + Points)
            'totals' => [
                'core' => $coreTotals,
                'support' => $supportTotals,
                'grand' => $grandTotals,
            ],

            // Attendance placeholders (demo)
            'attendance' => [
                'absence' => ['week1' => 0, 'week2' => 0, 'week3' => 0, 'week4' => 0, 'total' => 0],
                'tardiness' => ['week1' => 0, 'week2' => 0, 'week3' => 0, 'week4' => 0, 'total' => 0],
            ],

            // Notes
            'rating_note' => 'Ratings are completed during Stage III – Performance Review.',
            'footer_note' => 'This is a system-generated monitoring report derived from supervisor-rated ORS. Validation, SMPOR generation, and performance rating occur in Stage III.',
        ];
    }

    /**
     * Build one MPOR row with 3 bands:
     * - qty  (Efficiency/Quantity)
     * - qual (Quality points = qty * qualityRating)
     * - time (Timeliness points = qty * timelinessRating)
     */
    private function makeRow(string $output, int $qtyWeek1, int $qualityRating, int $timelinessRating): array
    {
        $qty = [
            'week1' => $qtyWeek1,
            'week2' => 0,
            'week3' => 0,
            'week4' => 0,
        ];
        $qty['total'] = $qty['week1'] + $qty['week2'] + $qty['week3'] + $qty['week4'];

        $qual = [
            'week1' => $qtyWeek1 * $qualityRating,
            'week2' => 0,
            'week3' => 0,
            'week4' => 0,
        ];
        $qual['total'] = $qual['week1'] + $qual['week2'] + $qual['week3'] + $qual['week4'];

        $time = [
            'week1' => $qtyWeek1 * $timelinessRating,
            'week2' => 0,
            'week3' => 0,
            'week4' => 0,
        ];
        $time['total'] = $time['week1'] + $time['week2'] + $time['week3'] + $time['week4'];

        return [
            'output' => $output,
            'qty' => $qty,
            'qual' => $qual,
            'time' => $time,

            // For reference/debug (optional for Blade)
            'ratings' => [
                'quality' => $qualityRating,
                'timeliness' => $timelinessRating,
            ],
        ];
    }

    /**
     * Compute totals for a section (core/support) across all rows.
     */
    private function computeSectionTotals(array $rows): array
    {
        $totals = [
            'qty' => ['week1' => 0, 'week2' => 0, 'week3' => 0, 'week4' => 0, 'total' => 0],
            'qual' => ['week1' => 0, 'week2' => 0, 'week3' => 0, 'week4' => 0, 'total' => 0],
            'time' => ['week1' => 0, 'week2' => 0, 'week3' => 0, 'week4' => 0, 'total' => 0],
        ];

        foreach ($rows as $row) {
            foreach (['qty', 'qual', 'time'] as $band) {
                $totals[$band]['week1'] += (int)($row[$band]['week1'] ?? 0);
                $totals[$band]['week2'] += (int)($row[$band]['week2'] ?? 0);
                $totals[$band]['week3'] += (int)($row[$band]['week3'] ?? 0);
                $totals[$band]['week4'] += (int)($row[$band]['week4'] ?? 0);
                $totals[$band]['total'] += (int)($row[$band]['total'] ?? 0);
            }
        }

        return $totals;
    }

    /**
     * Sum two totals arrays of the same structure (week1..week4,total).
     */
    private function sumBands(array $a, array $b): array
    {
        return [
            'week1' => (int)($a['week1'] ?? 0) + (int)($b['week1'] ?? 0),
            'week2' => (int)($a['week2'] ?? 0) + (int)($b['week2'] ?? 0),
            'week3' => (int)($a['week3'] ?? 0) + (int)($b['week3'] ?? 0),
            'week4' => (int)($a['week4'] ?? 0) + (int)($b['week4'] ?? 0),
            'total' => (int)($a['total'] ?? 0) + (int)($b['total'] ?? 0),
        ];
    }
}
