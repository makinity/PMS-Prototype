<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class SmporExportController extends Controller
{
    /**
     * Demo-only Stage II SMPOR export (monitoring copy).
     */
    public function exportPdf()
    {
        $employeeName = 'Juan Dela Cruz';
        $officeDivision = 'Revenue Collection Unit';
        $semestralPeriod = 'January–June 2026';
        $provinceCity = 'Province of Davao del Sur – Matti, Digos City';

        $coreFunctions = [
            [
                'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'efficiency' => [
                    'Jan' => '✓', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 1, 'Average' => '',
                ],
                'quality' => [
                    'Jan' => '✓', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 1, 'Average' => '',
                ],
            ],
            [
                'output' => 'Processing of Over-the-Counter Revenue Transactions',
                'efficiency' => [
                    'Jan' => '✓', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 1, 'Average' => '',
                ],
                'quality' => [
                    'Jan' => '✓', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 1, 'Average' => '',
                ],
            ],
        ];

        $strategicObjectives = [
            [
                'output' => '',
                'efficiency' => [
                    'Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => '', 'Average' => '',
                ],
                'quality' => [
                    'Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => '', 'Average' => '',
                ],
            ],
        ];

        $supportFunctions = [
            [
                'output' => 'Maintenance of Revenue Records Filing System',
                'efficiency' => [
                    'Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 0, 'Average' => '',
                ],
                'quality' => [
                    'Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 0, 'Average' => '',
                ],
            ],
        ];

        $timeliness = [
            'core' => ['Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0'],
            'strategic' => ['Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0'],
            'support' => ['Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0'],
        ];

        $manDaysLost = '0';
        $manHoursLost = '0';

        $pdf = Pdf::loadView('pdf.stage-two.smpor', [
            'employeeName'        => $employeeName,
            'officeDivision'      => $officeDivision,
            'semestralPeriod'     => $semestralPeriod,
            'provinceCity'        => $provinceCity,
            'coreFunctions'       => $coreFunctions,
            'strategicObjectives' => $strategicObjectives,
            'supportFunctions'    => $supportFunctions,
            'timeliness'          => $timeliness,
            'manDaysLost'         => $manDaysLost,
            'manHoursLost'        => $manHoursLost,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Employee_SMPOR_Jan-Jun_2026.pdf');
    }

    public function preview(){
        $employeeName = 'Juan Dela Cruz';
        $officeDivision = 'Revenue Collection Unit';
        $semestralPeriod = 'January–June 2026';
        $provinceCity = 'Province of Davao del Sur – Matti, Digos City';

        $coreFunctions = [
            [
                'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'efficiency' => [
                    'Jan' => '✓', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 1, 'Average' => '',
                ],
                'quality' => [
                    'Jan' => '✓', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 1, 'Average' => '',
                ],
            ],
            [
                'output' => 'Processing of Over-the-Counter Revenue Transactions',
                'efficiency' => [
                    'Jan' => '✓', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 1, 'Average' => '',
                ],
                'quality' => [
                    'Jan' => '✓', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 1, 'Average' => '',
                ],
            ],
        ];

        $strategicObjectives = [
            [
                'output' => '',
                'efficiency' => [
                    'Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => '', 'Average' => '',
                ],
                'quality' => [
                    'Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => '', 'Average' => '',
                ],
            ],
        ];

        $supportFunctions = [
            [
                'output' => 'Maintenance of Revenue Records Filing System',
                'efficiency' => [
                    'Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 0, 'Average' => '',
                ],
                'quality' => [
                    'Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0',
                    'Total' => 0, 'Average' => '',
                ],
            ],
        ];

        $timeliness = [
            'core' => ['Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0'],
            'strategic' => ['Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0'],
            'support' => ['Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0'],
        ];

        $manDaysLost = '0';
        $manHoursLost = '0';

       $pdf = Pdf::loadView(
            'pdf.stage-two.smpor',
            compact(
                'employeeName',
                'officeDivision',
                'semestralPeriod',
                'provinceCity',
                'coreFunctions',
                'strategicObjectives',
                'supportFunctions',
                'timeliness',
                'manDaysLost',
                'manHoursLost'
            )
        )->setPaper('a4', 'portrait');

        return $pdf->stream('SMPOR.pdf');
    }
}
