<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class OrsExportController extends Controller
{
    public function exportPdf()
    {
        // DEMO LOCKED DATASET (Stage II – Supervisor Monitoring Export)
        // Aligned with Supervisor ORS + Employee My Tasks
        $ors = [
            'ratee' => 'Ramon Reyes',
            'supervisor' => 'Carlo D. Beray',

            'output' => 'All e-bank transactions scanned and encoded daily',
            'date_submitted' => 'January 4, 2026',

            // Employee-declared (locked)
            'quantity' => '1 daily batch',

            // Supervisor monitoring rating (locked demo)
            'quality' => '5',
            'timeliness' => '5',
            'remarks' => 'All Goods',

            // Signature placeholders (future)
            'rater_signature' => '',
            'date_returned' => '',
        ];

        $pdf = Pdf::loadView('pdf.stage-two.ors', compact('ors'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('ORS_Revenue_Collection_Unit_Jan_2026.pdf');
    }

    public function preview()
    {
        // Same locked dataset for preview consistency
        $ors = [
            'ratee' => 'Ramon Reyes',
            'supervisor' => 'Carlo D. Beray',

            'output' => 'All e-bank transactions scanned and encoded daily',
            'date_submitted' => 'January 4, 2026',

            'quantity' => '1 daily batch',

            'quality' => '5',
            'timeliness' => '5',
            'remarks' => 'All Goods',

            'rater_signature' => '',
            'date_returned' => '',
        ];

        $pdf = Pdf::loadView('pdf.stage-two.ors', compact('ors'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('ORS_Revenue_Collection_Unit_Jan_2026.pdf');
    }
}
