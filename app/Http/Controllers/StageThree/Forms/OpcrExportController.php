<?php

namespace App\Http\Controllers\StageThree\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OpcrExportController extends Controller
{
    public function exportPdf(){


        $pdf = Pdf::loadView('pdf.stage-three.opcr', [

        ])->setPaper('legal', 'landscape');

        return $pdf->download('Opcr_Jan-Jun_2026.pdf');
    }

    public function preview(){
        $pdf = Pdf::loadView('pdf.stage-three.opcr', [

        ])->setPaper('legal', 'landscape');

        return $pdf->stream('Opcr_Jan-Jun_2026.pdf');
    }
}
