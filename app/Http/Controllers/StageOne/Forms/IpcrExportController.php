<?php

namespace App\Http\Controllers\StageOne\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class IpcrExportController extends Controller
{
    public function exportPdf()
    {
        $ipcr = [
            'employee_name' => 'Ramon Reyes',
            'position' => 'Records Management Officer',
            'office' => 'Revenue Collection Unit',
            'supervisor' => 'Carlo D. Beray',
            'dept_head' => 'Dept-head',
            'period' => 'January – June 2026',

            'core_functions' => [
                [
                    'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                    'target' => '95% same-day processing',
                    'timeline' => 'January – June 2026',
                    'weight' => '50%',
                ],
                [
                    'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                    'target' => '95% same-day processing',
                    'timeline' => 'January – June 2026',
                    'weight' => '30%',
                ],
            ],

            'support_functions' => [
                [
                    'mfo' => 'Maintenance of Revenue Records Filing System',
                    'target' => 'Quarterly validation',
                    'timeline' => 'January – June 2026',
                    'weight' => '20%',
                ],
            ],
        ];

        $pdf = Pdf::loadView('pdf.stage-one.ipcr', compact('ipcr'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('IPCR_Targets_Ramon_Reyes_Jan-Jun_2026.pdf');
    }
}
