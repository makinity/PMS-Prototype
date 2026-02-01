<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class OrsExportController extends Controller
{
    public function exportPdf()
    {
        // DEMO LOCKED DATASET (Stage II) — Supervisor Monitoring export
        // Matches ORS format screenshot + dummy data
        // Quantity = employee-declared (locked)
        // Quality + Timeliness = default 5 (per demo rule)
        $ors = [
            'ratee' => 'Ramon Reyes',
            'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
            'date_submitted' => 'January 4, 2026',
            'remarks' => 'All e-bank transactions scanned and encoded daily',

            // Locked demo values
            'quantity' => '35',
            'quality' => '5',
            'timeliness' => '5',

            'rater_signature' => '',
            'date_returned' => '',
        ];

        $pdf = Pdf::loadView('pdf.stage-two.ors', compact('ors'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('ORS_Revenue_Collection_Unit_Jan_2026.pdf');
    }

    public function preview()
    {
        // DEMO LOCKED DATASET (Stage II) — Supervisor Monitoring export
        $ors = [
            'ratee' => 'Ramon Reyes',
            'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
            'date_submitted' => 'January 4, 2026',
            'remarks' => 'All e-bank transactions scanned and encoded daily',

            'quantity' => '35',
            'quality' => '5',
            'timeliness' => '5',

            'rater_signature' => '',
            'date_returned' => '',
        ];

        $pdf = Pdf::loadView('pdf.stage-two.ors', compact('ors'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('ORS_Revenue_Collection_Unit_Jan_2026.pdf');
    }
}
