<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class OrsExportController extends Controller
{
    public function exportPdf()
    {
        // Stage II monitoring copy; read-only export derived from submitted ORS
        $ors = [
            'ratee' => 'Ramon Reyes',
            'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
            'date_submitted' => 'January 19, 2026',
            'remarks' => 'All e-bank transactions scanned and encoded daily',
            'quantity' => '',
            'quality' => '',
            'timeliness' => '',
            'rater_signature' => '',
            'date_returned' => '',
        ];

        $pdf = Pdf::loadView('pdf.stage-two.ors', compact('ors'))
        ->setPaper('a4', 'portrait');


        return $pdf->download('ORS_Revenue_Collection_Unit_Jan_2026.pdf');
    }

    public function preview(){
        // Stage II monitoring copy; read-only export derived from submitted ORS
        $ors = [
            'ratee' => 'Ramon Reyes',
            'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
            'date_submitted' => 'January 19, 2026',
            'remarks' => 'All e-bank transactions scanned and encoded daily',
            'quantity' => '',
            'quality' => '',
            'timeliness' => '',
            'rater_signature' => '',
            'date_returned' => '',
        ];

        $pdf = Pdf::loadView('pdf.stage-two.ors', compact('ors'))
        ->setPaper('a4', 'portrait');


        return $pdf->stream('ORS_Revenue_Collection_Unit_Jan_2026.pdf');
    }

}
