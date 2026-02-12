<?php

namespace Database\Seeders;

use App\Models\UnitWorkPlan;
use App\Models\User;
use App\Models\UwpFunction;
use App\Models\UwpIndicatorAssignment;
use App\Models\UwpMfo;
use App\Models\UwpQetStandard;
use App\Models\UwpSuccessIndicator;
use Illuminate\Database\Seeder;

class DemoUwpSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $supervisor = User::query()
            ->where('role', 'supervisor')
            ->orderBy('id')
            ->first();

        $employee = User::query()
            ->where('role', 'employee')
            ->orderBy('id')
            ->first();

        if (!$supervisor || !$employee) {
            return;
        }

        $uwp = UnitWorkPlan::firstOrCreate(
            [
                'office_id' => 1,
                'performance_period_id' => 1,
                'created_by' => $supervisor->id,
            ],
            [
                'status' => UnitWorkPlan::STATUS_DRAFT,
            ]
        );

        $functions = [
            [
                'name' => 'Core Functions',
                'function_type' => 'core',
                'weight_percent' => 80,
                'sort_order' => 1,
                'mfos' => [
                    [
                        'title' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                        'target_timeline' => 'Daily; all e-bank transactions processed within the same working day',
                        'sort_order' => 1,
                        'indicators' => [
                            'All e-bank transactions scanned and encoded daily',
                            'Indexing complete with no missing pages',
                            'Audit trail maintained within 24 hours',
                        ],
                    ],
                    [
                        'title' => 'Processing of Over-the-Counter Revenue Transactions',
                        'target_timeline' => 'Daily; 95% processed within the same working day',
                        'sort_order' => 2,
                        'indicators' => [
                            'Same-day verification of OTC transactions',
                            '95% encoded within the business day',
                            'OR validation completed daily',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Support Functions',
                'function_type' => 'support',
                'weight_percent' => 20,
                'sort_order' => 2,
                'mfos' => [
                    [
                        'title' => 'Maintenance of revenue records and filing system',
                        'target_timeline' => 'Quarterly; records validated and properly filed',
                        'sort_order' => 1,
                        'indicators' => [
                            'Weekly filing updated and retrievable',
                            'Digital backups synced monthly',
                            'Retrieval logs maintained for audits',
                        ],
                    ],
                ],
            ],
        ];

        $standardsSeedMap = [
            'All e-bank transactions scanned and encoded daily' => [
                5 => ['q' => 'No errors; accurate encoding', 'e' => '100% processed', 't' => 'Same working day'],
                4 => ['q' => 'Minor errors', 'e' => '100% processed', 't' => 'Same working day'],
                3 => ['q' => 'Few minor errors', 'e' => '95-99% processed', 't' => 'End of working day'],
                2 => ['q' => 'Multiple errors', 'e' => '<95% processed', 't' => 'Beyond working day'],
                1 => ['q' => 'Major errors/missing', 'e' => 'Majority unprocessed', 't' => 'Not within acceptable time'],
            ],
            'Indexing complete with no missing pages' => [
                5 => ['q' => 'Indexing fully verified, zero gaps', 'e' => '100% pages indexed', 't' => 'Same day'],
                4 => ['q' => 'Indexing minor rechecks', 'e' => '100% pages indexed', 't' => 'Same day'],
                3 => ['q' => 'Occasional missing indexes fixed', 'e' => '95-99% indexed', 't' => 'Within 24 hours'],
                2 => ['q' => 'Frequent missing pages', 'e' => '<95% indexed', 't' => 'Beyond 24 hours'],
                1 => ['q' => 'Indexing largely incomplete', 'e' => 'Major gaps', 't' => 'Unacceptable'],
            ],
            'Audit trail maintained within 24 hours' => [
                5 => ['q' => 'Complete trail, no errors', 'e' => '100% entries captured', 't' => 'Within 24 hours'],
                4 => ['q' => 'Minor corrections only', 'e' => '100% entries captured', 't' => 'Within 24 hours'],
                3 => ['q' => 'Some gaps corrected', 'e' => '95-99% entries captured', 't' => 'Within 48 hours'],
                2 => ['q' => 'Multiple missing logs', 'e' => '<95% captured', 't' => 'Beyond 48 hours'],
                1 => ['q' => 'Trail missing', 'e' => 'Majority uncaptured', 't' => 'Unacceptable'],
            ],
            'Same-day verification of OTC transactions' => [
                5 => ['q' => 'Verified without discrepancies', 'e' => '100% OTC verified', 't' => 'Same working day'],
                4 => ['q' => 'Minor verifications pending', 'e' => '100% OTC verified', 't' => 'Same working day'],
                3 => ['q' => 'Few pending verifications', 'e' => '95-99% verified', 't' => 'End of working day'],
                2 => ['q' => 'Several unverified', 'e' => '<95% verified', 't' => 'Beyond working day'],
                1 => ['q' => 'Verification not done', 'e' => 'Majority unverified', 't' => 'Unacceptable'],
            ],
            '95% encoded within the business day' => [
                5 => ['q' => 'Encodings error-free', 'e' => '100% encoded', 't' => 'Same business day'],
                4 => ['q' => 'Minor corrections', 'e' => '100% encoded', 't' => 'Same business day'],
                3 => ['q' => 'Few delays', 'e' => '95-99% encoded', 't' => 'By end of day'],
                2 => ['q' => 'Multiple delays', 'e' => '<95% encoded', 't' => 'Next day'],
                1 => ['q' => 'Encoding largely incomplete', 'e' => 'Major backlog', 't' => 'Unacceptable'],
            ],
            'OR validation completed daily' => [
                5 => ['q' => 'All ORs validated error-free', 'e' => '100% validated', 't' => 'Daily'],
                4 => ['q' => 'Minor issues corrected same day', 'e' => '100% validated', 't' => 'Daily'],
                3 => ['q' => 'Some validations late', 'e' => '95-99% validated', 't' => 'Within 48 hours'],
                2 => ['q' => 'Frequent late validations', 'e' => '<95% validated', 't' => 'Beyond 48 hours'],
                1 => ['q' => 'Validations mostly missing', 'e' => 'Majority unvalidated', 't' => 'Unacceptable'],
            ],
            'Weekly filing updated and retrievable' => [
                5 => ['q' => 'Zero retrieval issues', 'e' => '100% weekly updates', 't' => 'Within week'],
                4 => ['q' => 'Minor retrieval fixes', 'e' => '100% weekly updates', 't' => 'Within week'],
                3 => ['q' => 'Some items late', 'e' => '95-99% updates', 't' => 'Within next week'],
                2 => ['q' => 'Many late updates', 'e' => '<95% updates', 't' => 'Beyond next week'],
                1 => ['q' => 'Updates not done', 'e' => 'Major gaps', 't' => 'Unacceptable'],
            ],
            'Digital backups synced monthly' => [
                5 => ['q' => 'Backups verified', 'e' => '100% synced', 't' => 'Within month'],
                4 => ['q' => 'Minor sync corrections', 'e' => '100% synced', 't' => 'Within month'],
                3 => ['q' => 'Some delays', 'e' => '95-99% synced', 't' => 'Within following week'],
                2 => ['q' => 'Frequent delays', 'e' => '<95% synced', 't' => 'Beyond following week'],
                1 => ['q' => 'Backups largely missing', 'e' => 'Major gaps', 't' => 'Unacceptable'],
            ],
            'Retrieval logs maintained for audits' => [
                5 => ['q' => 'Logs complete and audit-ready', 'e' => '100% requests logged', 't' => 'Same day'],
                4 => ['q' => 'Minor log gaps corrected', 'e' => '100% requests logged', 't' => 'Same day'],
                3 => ['q' => 'Some gaps', 'e' => '95-99% logged', 't' => 'Within 48 hours'],
                2 => ['q' => 'Many gaps', 'e' => '<95% logged', 't' => 'Beyond 48 hours'],
                1 => ['q' => 'Logs largely missing', 'e' => 'Majority unlogged', 't' => 'Unacceptable'],
            ],
        ];

        foreach ($functions as $functionData) {
            $function = UwpFunction::updateOrCreate(
                [
                    'unit_work_plan_id' => $uwp->id,
                    'name' => $functionData['name'],
                ],
                [
                    'function_type' => $functionData['function_type'],
                    'weight_percent' => $functionData['weight_percent'],
                    'sort_order' => $functionData['sort_order'],
                ]
            );

            foreach ($functionData['mfos'] as $mfoData) {
                $mfo = UwpMfo::updateOrCreate(
                    [
                        'uwp_function_id' => $function->id,
                        'title' => $mfoData['title'],
                    ],
                    [
                        'target_timeline' => $mfoData['target_timeline'],
                        'weight_percent' => null,
                        'sort_order' => $mfoData['sort_order'],
                    ]
                );

                foreach ($mfoData['indicators'] as $index => $indicatorText) {
                    $indicator = UwpSuccessIndicator::updateOrCreate(
                        [
                            'uwp_mfo_id' => $mfo->id,
                            'indicator_text' => $indicatorText,
                        ],
                        [
                            'sort_order' => $index + 1,
                        ]
                    );

                    $seed = $standardsSeedMap[$indicatorText] ?? [];
                    foreach ([5, 4, 3, 2, 1] as $rating) {
                        $row = $seed[$rating] ?? ['q' => null, 'e' => null, 't' => null];
                        foreach (['q', 'e', 't'] as $dim) {
                            UwpQetStandard::updateOrCreate(
                                [
                                    'uwp_success_indicator_id' => $indicator->id,
                                    'dimension' => $dim,
                                    'rating' => $rating,
                                ],
                                [
                                    'standard_text' => $row[$dim] ?? null,
                                ]
                            );
                        }
                    }
                }
            }
        }

        $firstIndicator = UwpSuccessIndicator::query()
            ->whereHas('uwpMfo.uwpFunction', function ($query) use ($uwp) {
                $query->where('unit_work_plan_id', $uwp->id);
            })
            ->orderBy('id')
            ->first();

        if ($firstIndicator) {
            UwpIndicatorAssignment::updateOrCreate(
                [
                    'uwp_success_indicator_id' => $firstIndicator->id,
                    'employee_id' => $employee->id,
                ],
                [
                    'assigned_by' => $supervisor->id,
                    'assigned_at' => $now,
                ]
            );
        }
    }
}
