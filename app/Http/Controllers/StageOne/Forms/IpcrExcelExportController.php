<?php

namespace App\Http\Controllers\StageOne\Forms;

use App\Exports\StageOne\IpcrExcelExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class IpcrExcelExportController extends Controller
{
    // Employee exports IPCR (locked demo)
    public function exportExcel()
    {
        $ipcr = $this->buildIpcr();
        $standards = $this->getStandardsSeedMap();

        return Excel::download(
            new IpcrExcelExport($ipcr, $standards),
            'IPCR_Ramon_Reyes_Revenue_Collection_Unit_Jan-Jun_2026.xlsx'
        );
    }

    public function previewExcel()
    {
        $ipcr = $this->buildIpcr();
        $standards = $this->getStandardsSeedMap();

        return Excel::download(
            new IpcrExcelExport($ipcr, $standards),
            'IPCR_Ramon_Reyes_Revenue_Collection_Unit_Jan-Jun_2026_Preview.xlsx'
        );
    }

    protected function buildIpcr(): array
    {
        // locked base from your IPCR Blade (demoMfos)
        return [
            'core' => [
                [
                    'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                    'indicators' => [
                        'All e-bank transactions scanned and encoded daily',
                        'Indexing complete with no missing pages',
                        'Audit trail maintained within 24 hours',
                    ],
                ],
                [
                    'output' => 'Processing of Over-the-Counter Revenue Transactions',
                    'indicators' => [
                        'Same-day verification of OTC transactions',
                        '95% encoded within the business day',
                        'OR validation completed daily',
                    ],
                ],
            ],
            'support' => [
                [
                    'output' => 'Maintenance of Revenue Records Filing System',
                    'indicators' => [
                        'Weekly filing updated and retrievable',
                        'Digital backups synced monthly',
                        'Retrieval logs maintained for audits',
                    ],
                ],
            ],
        ];
    }

    protected function getStandardsSeedMap(): array
    {
        // locked base from your IPCR Blade (standardsSeedMap)
        return [
            'All e-bank transactions scanned and encoded daily' => [
                5 => ['q' => ['No errors; accurate encoding'], 'e' => ['100% processed'], 't' => ['Same working day']],
                4 => ['q' => ['Minor errors'], 'e' => ['100% processed'], 't' => ['Same working day']],
                3 => ['q' => ['Few minor errors'], 'e' => ['95–99% processed'], 't' => ['End of working day']],
                2 => ['q' => ['Multiple errors'], 'e' => ['<95% processed'], 't' => ['Beyond working day']],
                1 => ['q' => ['Major errors/missing'], 'e' => ['Majority unprocessed'], 't' => ['Not within acceptable time']],
            ],
            'Indexing complete with no missing pages' => [
                5 => ['q' => ['Indexing fully verified, zero gaps'], 'e' => ['100% pages indexed'], 't' => ['Same day']],
                4 => ['q' => ['Indexing minor rechecks'], 'e' => ['100% pages indexed'], 't' => ['Same day']],
                3 => ['q' => ['Occasional missing indexes fixed'], 'e' => ['95–99% indexed'], 't' => ['Within 24 hours']],
                2 => ['q' => ['Frequent missing pages'], 'e' => ['<95% indexed'], 't' => ['Beyond 24 hours']],
                1 => ['q' => ['Indexing largely incomplete'], 'e' => ['Major gaps'], 't' => ['Unacceptable']],
            ],
            'Audit trail maintained within 24 hours' => [
                5 => ['q' => ['Complete trail, no errors'], 'e' => ['100% entries captured'], 't' => ['Within 24 hours']],
                4 => ['q' => ['Minor corrections only'], 'e' => ['100% entries captured'], 't' => ['Within 24 hours']],
                3 => ['q' => ['Some gaps corrected'], 'e' => ['95–99% entries captured'], 't' => ['Within 48 hours']],
                2 => ['q' => ['Multiple missing logs'], 'e' => ['<95% captured'], 't' => ['Beyond 48 hours']],
                1 => ['q' => ['Trail missing'], 'e' => ['Majority uncaptured'], 't' => ['Unacceptable']],
            ],
            'Same-day verification of OTC transactions' => [
                5 => ['q' => ['Verified without discrepancies'], 'e' => ['100% OTC verified'], 't' => ['Same working day']],
                4 => ['q' => ['Minor verifications pending'], 'e' => ['100% OTC verified'], 't' => ['Same working day']],
                3 => ['q' => ['Few pending verifications'], 'e' => ['95–99% verified'], 't' => ['End of working day']],
                2 => ['q' => ['Several unverified'], 'e' => ['<95% verified'], 't' => ['Beyond working day']],
                1 => ['q' => ['Verification not done'], 'e' => ['Majority unverified'], 't' => ['Unacceptable']],
            ],
            '95% encoded within the business day' => [
                5 => ['q' => ['Encodings error-free'], 'e' => ['100% encoded'], 't' => ['Same business day']],
                4 => ['q' => ['Minor corrections'], 'e' => ['100% encoded'], 't' => ['Same business day']],
                3 => ['q' => ['Few delays'], 'e' => ['95–99% encoded'], 't' => ['By end of day']],
                2 => ['q' => ['Multiple delays'], 'e' => ['<95% encoded'], 't' => ['Next day']],
                1 => ['q' => ['Encoding largely incomplete'], 'e' => ['Major backlog'], 't' => ['Unacceptable']],
            ],
            'OR validation completed daily' => [
                5 => ['q' => ['All ORs validated error-free'], 'e' => ['100% validated'], 't' => ['Daily']],
                4 => ['q' => ['Minor issues corrected same day'], 'e' => ['100% validated'], 't' => ['Daily']],
                3 => ['q' => ['Some validations late'], 'e' => ['95–99% validated'], 't' => ['Within 48 hours']],
                2 => ['q' => ['Frequent late validations'], 'e' => ['<95% validated'], 't' => ['Beyond 48 hours']],
                1 => ['q' => ['Validations mostly missing'], 'e' => ['Majority unvalidated'], 't' => ['Unacceptable']],
            ],
            'Weekly filing updated and retrievable' => [
                5 => ['q' => ['Zero retrieval issues'], 'e' => ['100% weekly updates'], 't' => ['Within week']],
                4 => ['q' => ['Minor retrieval fixes'], 'e' => ['100% weekly updates'], 't' => ['Within week']],
                3 => ['q' => ['Some items late'], 'e' => ['95–99% updates'], 't' => ['Within next week']],
                2 => ['q' => ['Many late updates'], 'e' => ['<95% updates'], 't' => ['Beyond next week']],
                1 => ['q' => ['Updates not done'], 'e' => ['Major gaps'], 't' => ['Unacceptable']],
            ],
            'Digital backups synced monthly' => [
                5 => ['q' => ['Backups verified'], 'e' => ['100% synced'], 't' => ['Within month']],
                4 => ['q' => ['Minor sync corrections'], 'e' => ['100% synced'], 't' => ['Within month']],
                3 => ['q' => ['Some delays'], 'e' => ['95–99% synced'], 't' => ['Within following week']],
                2 => ['q' => ['Frequent delays'], 'e' => ['<95% synced'], 't' => ['Beyond following week']],
                1 => ['q' => ['Backups largely missing'], 'e' => ['Major gaps'], 't' => ['Unacceptable']],
            ],
            'Retrieval logs maintained for audits' => [
                5 => ['q' => ['Logs complete and audit-ready'], 'e' => ['100% requests logged'], 't' => ['Same day']],
                4 => ['q' => ['Minor log gaps corrected'], 'e' => ['100% requests logged'], 't' => ['Same day']],
                3 => ['q' => ['Some gaps'], 'e' => ['95–99% logged'], 't' => ['Within 48 hours']],
                2 => ['q' => ['Many gaps'], 'e' => ['<95% logged'], 't' => ['Beyond 48 hours']],
                1 => ['q' => ['Logs largely missing'], 'e' => ['Majority unlogged'], 't' => ['Unacceptable']],
            ],
        ];
    }
}
