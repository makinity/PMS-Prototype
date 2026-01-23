<?php

namespace App\Http\Controllers\StageOne\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class UwpExportController extends Controller
{
    public function exportPdf()
    {
        $uwp = [
            'office' => 'Revenue Collection Unit',
            'supervisor' => 'Carlo D. Beray',
            'dept_head' => 'Dept-head',
            'period' => 'January – June 2026',

            'outputs' => [
                // CORE OUTPUT 1 – 50%
                [
                    'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                    'success_indicators' => [
                        'All e-bank transactions scanned and encoded daily',
                        'Indexing complete with no missing pages',
                        'Audit trail maintained within 24 hours',
                    ],
                    'target' => 'Daily; all e-bank transactions processed within the same working day',
                    'function' => 'Core (50%)',
                    'function_type' => 'core',
                ],

                // CORE OUTPUT 2 – 30%
                [
                    'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                    'success_indicators' => [
                        'Same-day verification of OTC transactions',
                        '95% encoded within the business day',
                        'OR validation completed daily',
                    ],
                    'target' => 'Daily; 95% processed within the same working day',
                    'function' => 'Core (30%)',
                    'function_type' => 'core',
                ],

                // SUPPORT OUTPUT – 20%
                [
                    'mfo' => 'Maintenance of Revenue Records Filing System',
                    'success_indicators' => [
                        'Weekly filing updated and retrievable',
                        'Digital backups synced monthly',
                        'Retrieval logs maintained for audits',
                    ],
                    'target' => 'Quarterly validation and update',
                    'function' => 'Support (20%)',
                    'function_type' => 'support',
                ],
            ],
        ];

        $pdf = Pdf::loadView('pdf.stage-one.uwp', compact('uwp'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('UWP_Revenue_Collection_Unit_Jan-Jun_2026.pdf');
    }

    public function preview(){
        $uwp = [
            'office' => 'Revenue Collection Unit',
            'supervisor' => 'Carlo D. Beray',
            'dept_head' => 'Dept-head',
            'period' => 'January – June 2026',

            'outputs' => [
                // CORE OUTPUT 1 – 50%
                [
                    'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                    'success_indicators' => [
                        'All e-bank transactions scanned and encoded daily',
                        'Indexing complete with no missing pages',
                        'Audit trail maintained within 24 hours',
                    ],
                    'target' => 'Daily; all e-bank transactions processed within the same working day',
                    'function' => 'Core (50%)',
                    'function_type' => 'core',
                ],

                // CORE OUTPUT 2 – 30%
                [
                    'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                    'success_indicators' => [
                        'Same-day verification of OTC transactions',
                        '95% encoded within the business day',
                        'OR validation completed daily',
                    ],
                    'target' => 'Daily; 95% processed within the same working day',
                    'function' => 'Core (30%)',
                    'function_type' => 'core',
                ],

                // SUPPORT OUTPUT – 20%
                [
                    'mfo' => 'Maintenance of Revenue Records Filing System',
                    'success_indicators' => [
                        'Weekly filing updated and retrievable',
                        'Digital backups synced monthly',
                        'Retrieval logs maintained for audits',
                    ],
                    'target' => 'Quarterly validation and update',
                    'function' => 'Support (20%)',
                    'function_type' => 'support',
                ],
            ],
        ];

        $pdf = Pdf::loadView('pdf.stage-one.uwp', compact('uwp'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('UWP_Revenue_Collection_Unit_Jan-Jun_2026.pdf');
    }
}
