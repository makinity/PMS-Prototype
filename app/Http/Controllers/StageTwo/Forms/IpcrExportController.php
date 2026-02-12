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
        $data = $this->buildIpcrData();

        $pdf = Pdf::loadView('pdf.stage-two.ipcr', $data)
            ->setPaper('legal', 'landscape');

        return $pdf->download('Employee_IPCR_Jan-Jun_2026.pdf');
    }

    public function preview()
    {
        $data = $this->buildIpcrData();

        $pdf = Pdf::loadView('pdf.stage-two.ipcr', $data)
            ->setPaper('legal', 'landscape');

        return $pdf->stream('Employee_IPCR_Jan-Jun_2026.pdf');
    }

    private function buildIpcrData(): array
    {
        return [
            'employeeName' => 'Ramon Reyes',
            'officeDivision' => 'Revenue Collection Unit',
            'performancePeriod' => 'January–June 2026',
            'coreFunctions' => [
                [
                    'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                    'success_indicators' => [
                        'All e-bank transactions scanned and encoded daily',
                        'Indexing complete with no missing pages',
                        'Audit trail maintained within 24 hours',
                    ],
                    'standard' => '5 - Stretch target',
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
                    'standard' => '5 - Stretch target',
                    'accomplishment' => 'Same-day verification of over-the-counter revenue transactions completed based on submitted ORS entry.',
                    'remarks' => '',
                ],
            ],
            'supportFunctions' => [
                [
                    'output' => 'Maintenance of Revenue Records Filing System',
                    'success_indicators' => [
                        'Weekly filing updated and retrievable',
                        'Digital backups synced monthly',
                        'Retrieval logs maintained for audits',
                    ],
                    'standard' => '5 - Stretch target',
                    'accomplishment' => '0',
                    'remarks' => 'No output logged for the period',
                ],
            ],
        ];
    }
}
