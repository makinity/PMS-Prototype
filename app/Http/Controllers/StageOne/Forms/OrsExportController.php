<?php

namespace App\Http\Controllers\StageOne\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class OrsExportController extends Controller
{
    public function exportPdf()
    {
        // Simulated selected MPOR entry (Stage II)
        $mporEntry = [
            'output' => 'E-Bank Scanning',
            'request_id' => 'REQ-2026-002',
            'date_submitted' => 'January 4, 2026',
        ];

        $ors = [
            // Header
            'ratee' => 'Ramon Reyes',
            'office' => 'Revenue Collection Unit',
            'period' => 'January 2026',

            // ORS-specific fields (single-entry form)
            'output' => $mporEntry['output'],
            'request_id' => $mporEntry['request_id'],
            'date_submitted' => $mporEntry['date_submitted'],

            // Stage II: ratings intentionally blank
            'quantity' => '',
            'quality' => '',
            'timeliness' => '',
            'remarks' => '',
            'rater_signature' => '',
            'rater_date' => '',
            'date_returned' => '',
        ];

        $pdf = Pdf::loadView('pdf.stage-two.ors', compact('ors'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('ORS_Revenue_Collection_Unit_Jan_2026.pdf');
    }

}
