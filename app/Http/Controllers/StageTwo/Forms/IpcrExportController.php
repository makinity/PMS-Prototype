<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class IpcrExportController extends Controller
{
    /**
     * Demo-only PDF export for Stage II IPCR (monitoring copy).
     */
    public function exportPdf()
    {
        $employeeName = 'Juan Dela Cruz';
        $officeDivision = 'Revenue Collection Unit';
        $performancePeriod = 'January–June 2026';

        $coreFunctions = [
            [
                'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'success_indicators' => [
                    'All e-bank transactions scanned and encoded daily',
                    'Indexing complete with no missing pages',
                    'Audit trail maintained within 24 hours',
                ],
                'accomplishment' => 'Completed daily scanning and encoding of e-bank transactions based on submitted ORS entries.',
                'remarks' => '',
            ],
            [
                'output' => 'Processing of Over-the-Counter Revenue Transactions',
                'success_indicators' => [
                    'Same-day verification of OTC transactions',
                    '95% encoded within the business day',
                    'OR validation completed daily',
                ],
                'accomplishment' => 'Same-day verification of over-the-counter revenue transactions completed based on submitted ORS entry.',
                'remarks' => '',
            ],
        ];

        $supportFunctions = [
            [
                'output' => 'Maintenance of Revenue Records Filing System',
                'success_indicators' => [
                    'Weekly filing updated and retrievable',
                    'Digital backups synced monthly',
                    'Retrieval logs maintained for audits',
                ],
                'accomplishment' => '0',
                'remarks' => 'No output logged for the period',
            ],
        ];

        $pdf = Pdf::loadView('pdf.stage-two.ipcr', [
                'employeeName' => $employeeName,
                'officeDivision' => $officeDivision,
                'performancePeriod' => $performancePeriod,
                'coreFunctions' => $coreFunctions,
                'supportFunctions' => $supportFunctions,
            ])
            ->setPaper('legal', 'landscape');

        return $pdf->download('Employee_IPCR_Jan_2026.pdf');
    }
}
