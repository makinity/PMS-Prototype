<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class QarExportController extends Controller
{
    /**
     * Demo-only export for Stage II - Performance Monitoring (QAR).
     * No aggregation, validation, or persistence; static dummy data only.
     */
    public function exportPdf()
    {
        $qar = [
            'office' => 'Revenue Collection Unit',
            'period' => 'January - June 2026',
            'source' => 'MPOR - January to June 2026',
        ];

        $core = [
            [
                'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'months' => [
                    'jan' => 1,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                ],
                'total' => 1,
            ],
            [
                'output' => 'Processing of Over-the-Counter Revenue Transactions',
                'months' => [
                    'jan' => 1,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                ],
                'total' => 1,
            ],
        ];

        $support = [
            [
                'output' => 'Maintenance of Revenue Records Filing System',
                'months' => [
                    'jan' => 0,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                ],
                'total' => 0,
            ],
        ];

        $summary = [
            'total_ors_entries' => 2,
            'core_outputs_logged' => 2,
            'support_outputs_logged' => 0,
            'months_with_activity' => 'January 2026',
        ];

        $footerNote = 'This document is a system-generated quarterly monitoring report derived from MPOR. Validation, SMPOR generation, and performance rating occur in Stage III - Performance Review.';


        $pdf = Pdf::loadView('pdf.stage-two.qar', compact('qar', 'core', 'support', 'summary', 'footerNote'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('QAR_Jan-Jun_2026.pdf');

    }

    public function preview(){
        $qar = [
            'office' => 'Revenue Collection Unit',
            'period' => 'January - June 2026',
            'source' => 'MPOR - January to June 2026',
        ];

        $core = [
            [
                'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'months' => [
                    'jan' => 1,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                ],
                'total' => 1,
            ],
            [
                'output' => 'Processing of Over-the-Counter Revenue Transactions',
                'months' => [
                    'jan' => 1,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                ],
                'total' => 1,
            ],
        ];

        $support = [
            [
                'output' => 'Maintenance of Revenue Records Filing System',
                'months' => [
                    'jan' => 0,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                ],
                'total' => 0,
            ],
        ];

        $summary = [
            'total_ors_entries' => 2,
            'core_outputs_logged' => 2,
            'support_outputs_logged' => 0,
            'months_with_activity' => 'January 2026',
        ];

        $footerNote = 'This document is a system-generated quarterly monitoring report derived from MPOR. Validation, SMPOR generation, and performance rating occur in Stage III - Performance Review.';


        $pdf = Pdf::loadView('pdf.stage-two.qar', compact('qar', 'core', 'support', 'summary', 'footerNote'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('QAR_Jan-Jun_2026.pdf');
    }
}
