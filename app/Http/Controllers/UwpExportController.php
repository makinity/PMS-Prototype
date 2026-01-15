<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class UwpExportController extends Controller
{
    public function exportPdf()
    {
        $uwp = [
            'office' => 'Administrative Services Unit',
            'supervisor' => 'Carlo D. Beray',
            'period' => 'January – December 2026',
            'dept_head' => 'Engr. Roberto Reyes',
            'outputs' => [
                [
                    'mfo' => 'Records Management',
                    'expected_output' => 'Process and file incoming documents',
                    'target' => '1,200',
                    'timeframe' => 'Jan – Dec',
                    'function' => 'Core',
                ],
            ],
        ];

        $pdf = Pdf::loadView('pdf.uwp', compact('uwp'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('UWP_Administrative_Services_Unit.pdf');
    }
}
