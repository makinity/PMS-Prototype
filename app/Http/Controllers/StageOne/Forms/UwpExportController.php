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
                    'expected_output' =>
                        'All e-bank transaction documents are scanned, encoded, and uploaded to the system with complete details and proper indexing.',
                    'target' => 'Verified and accurately recorded over-the-counter revenue transactions.',
                    'timeframe' => 'January – June 2026',
                    'function' => 'Core (50%)',
                    'function_type' => 'core',
                ],

                // CORE OUTPUT 2 – 30%
                [
                    'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                    'expected_output' =>
                        'All over-the-counter revenue transactions are verified, recorded, and encoded accurately within the same working day.',
                    'target' => 'Daily; 95% processed within the same working day',
                    'timeframe' => 'January – June 2026',
                    'function' => 'Core (30%)',
                    'function_type' => 'core',
                ],

                // SUPPORT OUTPUT – 20%
                [
                    'mfo' => 'Maintenance of Revenue Records Filing System',
                    'expected_output' =>
                        'Organized and updated physical and digital filing of revenue transaction records.',
                    'target' => 'Quarterly validation and update',
                    'timeframe' => 'Quarterly',
                    'function' => 'Support (20%)',
                    'function_type' => 'support',
                ],
            ],
        ];

        $pdf = Pdf::loadView('pdf.stage-one.uwp', compact('uwp'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('UWP_Revenue_Collection_Unit_Jan-Jun_2026.pdf');
    }
}
