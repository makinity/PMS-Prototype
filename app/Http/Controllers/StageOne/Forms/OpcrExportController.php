<?php

namespace App\Http\Controllers\StageOne\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class OpcrExportController extends Controller
{
    public function exportPdf()
    {
       $opcr = [
            'office'       => 'Revenue Collection Unit',
            'office_head'  => 'Carlo D. Beray',
            'dept_head'    => 'Dept-head',
            'period'       => 'January – June 2026',

            'outputs' => [

                // CORE FUNCTIONS (80%)
                [
                    'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                    'success_indicator' =>
                        'All e-bank revenue transaction documents are scanned, encoded, and uploaded with complete and accurate details.',
                    'budget' => '', // Stage 1: intentionally blank
                    'accountable' => 'Revenue Collection Unit',
                    'standard' => '',
                    'function_type' => 'core',
                    'weight' => 50,
                ],
                [
                    'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                    'success_indicator' =>
                        'All over-the-counter revenue transactions are verified, recorded, and encoded accurately within the same working day.',
                    'budget' => '', // Stage 1: intentionally blank
                    'accountable' => 'Revenue Collection Unit',
                    'standard' => '',
                    'function_type' => 'core',
                    'weight' => 30,
                ],

                // SUPPORT FUNCTIONS (20%)
                [
                    'mfo' => 'Maintenance of Revenue Records Filing System',
                    'success_indicator' =>
                        'Organized, updated, and easily retrievable physical and digital revenue records.',
                    'budget' => '', // Stage 1: intentionally blank
                    'accountable' => 'Revenue Collection Unit',
                    'standard' => '',
                    'function_type' => 'support',
                    'weight' => 20,
                ],
            ],
        ];


        $pdf = Pdf::loadView('pdf.stage-one.opcr', compact('opcr'))
            ->setPaper('legal', 'landscape');

        return $pdf->download('OPCR_Revenue_Collection_Unit_Jan-Jun_2026.pdf');
    }

    public function preview(){
        $opcr = [
            'office'       => 'Revenue Collection Unit',
            'office_head'  => 'Carlo D. Beray',
            'dept_head'    => 'Dept-head',
            'period'       => 'January – June 2026',

            'outputs' => [

                // CORE FUNCTIONS (80%)
                [
                    'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                    'success_indicator' =>
                        'All e-bank revenue transaction documents are scanned, encoded, and uploaded with complete and accurate details.',
                    'budget' => '', // Stage 1: intentionally blank
                    'accountable' => 'Revenue Collection Unit',
                    'standard' => '',
                    'function_type' => 'core',
                    'weight' => 50,
                ],
                [
                    'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                    'success_indicator' =>
                        'All over-the-counter revenue transactions are verified, recorded, and encoded accurately within the same working day.',
                    'budget' => '', // Stage 1: intentionally blank
                    'accountable' => 'Revenue Collection Unit',
                    'standard' => '',
                    'function_type' => 'core',
                    'weight' => 30,
                ],

                // SUPPORT FUNCTIONS (20%)
                [
                    'mfo' => 'Maintenance of Revenue Records Filing System',
                    'success_indicator' =>
                        'Organized, updated, and easily retrievable physical and digital revenue records.',
                    'budget' => '', // Stage 1: intentionally blank
                    'accountable' => 'Revenue Collection Unit',
                    'standard' => '',
                    'function_type' => 'support',
                    'weight' => 20,
                ],
            ],
        ];


        $pdf = Pdf::loadView('pdf.stage-one.opcr', compact('opcr'))
            ->setPaper('legal', 'landscape');

        return $pdf->stream('OPCR_Revenue_Collection_Unit_Jan-Jun_2026.pdf');
    }
}
