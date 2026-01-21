<?php
namespace App\Http\Controllers\StageThree\Forms;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class IpcrExportController extends Controller
{
    public function exportPdf()
    {
        $employeeName = 'Ramon Reyes';
        $officeDivision = 'Revenue Collection Unit';
        $position = 'Records Management Officer';
        $performancePeriod = 'January - June 2026';

        $coreFunctions = [
            [
                'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'success_indicators' => [
                    'All e-bank transactions scanned and encoded daily',
                    'Indexing complete with no missing pages',
                    'Audit trail maintained within 24 hours',
                ],
                'standard' => '5 - Stretch target',
                'accomplishment' => 'Completed daily scanning and encoding of e-bank transactions based on submitted ORS entries.',
                'q' => '5',
                'e' => '5',
                't' => '5',
                'a' => '5.00',
                'remarks' => 'Supervisor-encoded rating based on locked SMPOR and ORS accomplishments.',
            ],
            [
                'output' => 'Processing of Over-the-Counter Revenue Transactions',
                'success_indicators' => [
                    'Same-day verification of OTC transactions',
                    '95% encoded within the business day',
                    'OR validation completed daily',
                ],
                'standard' => '5 - Stretch target',
                'accomplishment' => 'Same-day verification of over-the-counter revenue transactions completed based on submitted ORS entry.',
                'q' => '5',
                'e' => '5',
                't' => '5',
                'a' => '5.00',
                'remarks' => 'Supervisor-encoded rating based on locked SMPOR and ORS accomplishments.',
            ],
        ];

        $supportFunctions = [
            [
                'output' => 'Maintenance of Revenue Records Filing System',
                'success_indicators' => [
                    'Weekly filing updated and retrievable',
                    'Digital backups synced monthly',
                    'Retrieval logs maintained for audits',
                ],
                'standard' => '5 - Stretch target',
                'accomplishment' => '0',
                'q' => '',
                'e' => '',
                't' => '',
                'a' => '',
                'remarks' => 'No output logged for the period',
            ],
        ];

        $weightedCore = '5.00';
        $weightedSupport = '';
        $overallRating = '5.00';
        $adjectival = 'Outstanding';

        $pdf = Pdf::loadView('pdf.stage-three.ipcr', [
            'employeeName'      => $employeeName,
            'officeDivision'    => $officeDivision,
            'position'          => $position,
            'performancePeriod' => $performancePeriod,
            'coreFunctions'     => $coreFunctions,
            'supportFunctions'  => $supportFunctions,
            'weightedCore'      => $weightedCore,
            'weightedSupport'   => $weightedSupport,
            'overallRating'     => $overallRating,
            'adjectival'        => $adjectival,
        ])->setPaper('legal', 'landscape');

        return $pdf->download('Ramon_Reyes_IPCR_Jan-Jun_2026.pdf');
    }

    public function preview(){
        $employeeName = 'Ramon Reyes';
        $officeDivision = 'Revenue Collection Unit';
        $position = 'Records Management Officer';
        $performancePeriod = 'January - June 2026';

        $coreFunctions = [
            [
                'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'success_indicators' => [
                    'All e-bank transactions scanned and encoded daily',
                    'Indexing complete with no missing pages',
                    'Audit trail maintained within 24 hours',
                ],
                'standard' => '5 - Stretch target',
                'accomplishment' => 'Completed daily scanning and encoding of e-bank transactions based on submitted ORS entries.',
                'q' => '5',
                'e' => '5',
                't' => '5',
                'a' => '5.00',
                'remarks' => 'Supervisor-encoded rating based on locked SMPOR and ORS accomplishments.',
            ],
            [
                'output' => 'Processing of Over-the-Counter Revenue Transactions',
                'success_indicators' => [
                    'Same-day verification of OTC transactions',
                    '95% encoded within the business day',
                    'OR validation completed daily',
                ],
                'standard' => '5 - Stretch target',
                'accomplishment' => 'Same-day verification of over-the-counter revenue transactions completed based on submitted ORS entry.',
                'q' => '5',
                'e' => '5',
                't' => '5',
                'a' => '5.00',
                'remarks' => 'Supervisor-encoded rating based on locked SMPOR and ORS accomplishments.',
            ],
        ];

        $supportFunctions = [
            [
                'output' => 'Maintenance of Revenue Records Filing System',
                'success_indicators' => [
                    'Weekly filing updated and retrievable',
                    'Digital backups synced monthly',
                    'Retrieval logs maintained for audits',
                ],
                'standard' => '5 - Stretch target',
                'accomplishment' => '0',
                'q' => '',
                'e' => '',
                't' => '',
                'a' => '',
                'remarks' => 'No output logged for the period',
            ],
        ];

        $weightedCore = '5.00';
        $weightedSupport = '';
        $overallRating = '5.00';
        $adjectival = 'Outstanding';

        $pdf = Pdf::loadView('pdf.stage-three.ipcr', [
            'employeeName'      => $employeeName,
            'officeDivision'    => $officeDivision,
            'position'          => $position,
            'performancePeriod' => $performancePeriod,
            'coreFunctions'     => $coreFunctions,
            'supportFunctions'  => $supportFunctions,
            'weightedCore'      => $weightedCore,
            'weightedSupport'   => $weightedSupport,
            'overallRating'     => $overallRating,
            'adjectival'        => $adjectival,
        ])->setPaper('legal', 'landscape');

        return $pdf->stream('Ramon_Reyes_IPCR_Jan-Jun_2026.pdf');
    }
}
