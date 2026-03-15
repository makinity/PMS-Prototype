<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use App\Models\User;
use App\Models\UwpFunction;
use App\Models\UwpIndicatorAssignment;
use App\Models\UwpMfo;
use App\Models\UwpQetStandard;
use App\Models\UwpSuccessIndicator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();
            $password = Hash::make('password');

            $officeRCU = Office::query()->updateOrCreate(
                ['code' => 'RCU'],
                [
                    'name' => 'Revenue Collection Unit',
                    'head_id' => null,
                ]
            );

            $officeRMU = Office::query()->updateOrCreate(
                ['code' => 'RMU'],
                [
                    'name' => 'Revenue Management Unit',
                    'head_id' => null,
                ]
            );

            User::query()->updateOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'employee_id' => 'ADM-0001',
                    'name' => 'admin',
                    'password' => $password,
                    'role' => 'admin',
                    'office_id' => null,
                    'position' => 'Administrator',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            User::query()->updateOrCreate(
                ['email' => 'pmt@example.com'],
                [
                    'employee_id' => 'PMT-0001',
                    'name' => 'pmt',
                    'password' => $password,
                    'role' => 'pmt',
                    'office_id' => null,
                    'position' => 'PMT',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $deptHeadRCU = User::query()->updateOrCreate(
                ['email' => 'dept-head.rcu@example.com'],
                [
                    'employee_id' => 'DH-RCU-0001',
                    'name' => 'dept-head',
                    'password' => $password,
                    'role' => 'dept-head',
                    'office_id' => $officeRCU->id,
                    'position' => 'Department Head',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $supervisorRCU = User::query()->updateOrCreate(
                ['email' => 'carlo.beray@example.com'],
                [
                    'employee_id' => 'SUP-RCU-0001',
                    'name' => 'Carlo D. Beray',
                    'password' => $password,
                    'role' => 'supervisor',
                    'office_id' => $officeRCU->id,
                    'position' => 'Supervisor',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $empRamon = User::query()->updateOrCreate(
                ['email' => 'ramon.reyes@example.com'],
                [
                    'employee_id' => 'EMP-RCU-0001',
                    'name' => 'Ramon Reyes',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRCU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $empMark = User::query()->updateOrCreate(
                ['email' => 'marklionesios@gmail.com.com'],
                [
                    'employee_id' => 'EMP-RCU-0002',
                    'name' => 'Mark Juntilla',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRCU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $empDenji = User::query()->updateOrCreate(
                ['email' => 'denjikun1030.com'],
                [
                    'employee_id' => 'EMP-RCU-0003',
                    'name' => 'Denji Kun',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRCU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $deptHeadRMU = User::query()->updateOrCreate(
                ['email' => 'dept-head.rmu@example.com'],
                [
                    'employee_id' => 'DH-RMU-0001',
                    'name' => 'dept-head-rmu',
                    'password' => $password,
                    'role' => 'dept-head',
                    'office_id' => $officeRMU->id,
                    'position' => 'Department Head',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $supervisorRMU = User::query()->updateOrCreate(
                ['email' => 'maria.navarro@example.com'],
                [
                    'employee_id' => 'SUP-RMU-0001',
                    'name' => 'Maria P. Navarro',
                    'password' => $password,
                    'role' => 'supervisor',
                    'office_id' => $officeRMU->id,
                    'position' => 'Supervisor',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $empMilo = User::query()->updateOrCreate(
                ['email' => 'milo.ramos@example.com'],
                [
                    'employee_id' => 'EMP-RMU-0001',
                    'name' => 'Milo Ramos',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRMU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $empXuiie = User::query()->updateOrCreate(
                ['email' => 'xuiie.fernandez@example.com'],
                [
                    'employee_id' => 'EMP-RMU-0002',
                    'name' => 'Xuiie Fernandez',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRMU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
                ]
            );

            $officeRCU->update(['head_id' => $deptHeadRCU->id]);
            $officeRMU->update(['head_id' => $deptHeadRMU->id]);

            PerformancePeriod::query()->update(['is_active' => false]);

            $period = PerformancePeriod::query()->updateOrCreate(
                ['name' => 'Jan–Jun 2026'],
                [
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-06-30',
                    'is_active' => true,
                ]
            );

            $uwpRCU = UnitWorkPlan::query()->updateOrCreate(
                [
                    'office_id' => $officeRCU->id,
                    'performance_period_id' => $period->id,
                ],
                [
                    'created_by' => $supervisorRCU->id,
                    'status' => UnitWorkPlan::STATUS_DRAFT,
                    'submitted_at' => null,
                    'locked_at' => null,
                ]
            );

            $uwpRMU = UnitWorkPlan::query()->updateOrCreate(
                [
                    'office_id' => $officeRMU->id,
                    'performance_period_id' => $period->id,
                ],
                [
                    'created_by' => $supervisorRMU->id,
                    'status' => UnitWorkPlan::STATUS_DRAFT,
                    'submitted_at' => null,
                    'locked_at' => null,
                ]
            );

            $seedUwpTemplate = function (
                UnitWorkPlan $uwp,
                array $functionSeed,
                array $standardsMap
            ): void {
                $functionIds = UwpFunction::query()
                    ->where('unit_work_plan_id', $uwp->id)
                    ->pluck('id');

                if ($functionIds->isNotEmpty()) {
                    $mfoIds = UwpMfo::query()
                        ->whereIn('uwp_function_id', $functionIds)
                        ->pluck('id');

                    $indicatorIds = UwpSuccessIndicator::query()
                        ->whereIn('uwp_mfo_id', $mfoIds)
                        ->pluck('id');

                    if ($indicatorIds->isNotEmpty()) {
                        UwpIndicatorAssignment::query()
                            ->whereIn('uwp_success_indicator_id', $indicatorIds)
                            ->delete();

                        UwpQetStandard::query()
                            ->whereIn('uwp_success_indicator_id', $indicatorIds)
                            ->delete();

                        UwpSuccessIndicator::query()
                            ->whereIn('id', $indicatorIds)
                            ->delete();
                    }

                    if ($mfoIds->isNotEmpty()) {
                        UwpMfo::query()
                            ->whereIn('id', $mfoIds)
                            ->delete();
                    }

                    UwpFunction::query()
                        ->whereIn('id', $functionIds)
                        ->delete();
                }

                foreach ($functionSeed as $functionData) {
                    $function = UwpFunction::query()->create([
                        'unit_work_plan_id' => $uwp->id,
                        'name' => $functionData['name'],
                        'function_type' => $functionData['function_type'],
                        'weight_percent' => $functionData['weight_percent'],
                        'sort_order' => $functionData['sort_order'],
                    ]);

                    foreach ($functionData['mfos'] as $mfoData) {
                        $mfo = UwpMfo::query()->create([
                            'uwp_function_id' => $function->id,
                            'title' => $mfoData['title'],
                            'target_quantity' => $mfoData['target_quantity'] ?? null,
                            'target_timeline' => $mfoData['target_timeline'],
                            'sort_order' => $mfoData['sort_order'],
                        ]);

                        foreach ($mfoData['indicators'] as $indicatorSort => $indicatorText) {
                            $indicator = UwpSuccessIndicator::query()->create([
                                'uwp_mfo_id' => $mfo->id,
                                'indicator_text' => $indicatorText,
                                'sort_order' => $indicatorSort + 1,
                            ]);

                            $indicatorStandards = $standardsMap[$indicatorText] ?? [];
                            foreach ([5, 4, 3, 2, 1] as $rating) {
                                $standardsByDimension = $indicatorStandards[$rating] ?? [];
                                foreach (['q', 'e', 't'] as $dimension) {
                                    $standardText = trim((string) ($standardsByDimension[$dimension] ?? ''));
                                    if ($standardText === '') {
                                        continue;
                                    }

                                    UwpQetStandard::query()->create([
                                        'uwp_success_indicator_id' => $indicator->id,
                                        'dimension' => $dimension,
                                        'rating' => $rating,
                                        'standard_text' => $standardText,
                                    ]);
                                }
                            }
                        }
                    }
                }
            };

            $functionSeedRCU = [
                [
                    'name' => 'Core Functions',
                    'function_type' => 'core',
                    'weight_percent' => 80,
                    'sort_order' => 1,
                    'mfos' => [
                        [
                            'title' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                            'target_quantity' => 1200,
                            'target_timeline' => 'e-bank transactions processed within the semester',
                            'sort_order' => 1,
                            'indicators' => [
                                'All e-bank transactions scanned and encoded daily',
                                'Indexing complete with no missing pages',
                                'Audit trail maintained within 24 hours',
                            ],
                        ],
                        [
                            'title' => 'Processing of Over-the-Counter Revenue Transactions',
                            'target_quantity' => 3000,
                            'target_timeline' => 'OCR processed within the semester',
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
                            'target_quantity' => 2400,
                            'target_timeline' => 'records validated and properly filed within the semester',
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

            $standardsMapRCU = [
                'All e-bank transactions scanned and encoded daily' => [
                    5 => ['q' => 'No errors; accurate encoding', 'e' => '100% processed', 't' => 'Same working day'],
                    4 => ['q' => 'Minor errors', 'e' => '100% processed', 't' => 'Same working day'],
                    3 => ['q' => 'Few minor errors', 'e' => '95–99% processed', 't' => 'End of working day'],
                    2 => ['q' => 'Multiple errors', 'e' => '<95% processed', 't' => 'Beyond working day'],
                    1 => ['q' => 'Major errors/missing', 'e' => 'Majority unprocessed', 't' => 'Not within acceptable time'],
                ],
                'Indexing complete with no missing pages' => [
                    5 => ['q' => 'Indexing fully verified, zero gaps', 'e' => '100% pages indexed', 't' => 'Same day'],
                    4 => ['q' => 'Indexing minor rechecks', 'e' => '100% pages indexed', 't' => 'Same day'],
                    3 => ['q' => 'Occasional missing indexes fixed', 'e' => '95–99% indexed', 't' => 'Within 24 hours'],
                    2 => ['q' => 'Frequent missing pages', 'e' => '<95% indexed', 't' => 'Beyond 24 hours'],
                    1 => ['q' => 'Indexing largely incomplete', 'e' => 'Major gaps', 't' => 'Unacceptable'],
                ],
                'Audit trail maintained within 24 hours' => [
                    5 => ['q' => 'Complete trail, no errors', 'e' => '100% entries captured', 't' => 'Within 24 hours'],
                    4 => ['q' => 'Minor corrections only', 'e' => '100% entries captured', 't' => 'Within 24 hours'],
                    3 => ['q' => 'Some gaps corrected', 'e' => '95–99% entries captured', 't' => 'Within 48 hours'],
                    2 => ['q' => 'Multiple missing logs', 'e' => '<95% captured', 't' => 'Beyond 48 hours'],
                    1 => ['q' => 'Trail missing', 'e' => 'Majority uncaptured', 't' => 'Unacceptable'],
                ],
                'Same-day verification of OTC transactions' => [
                    5 => ['q' => 'Verified without discrepancies', 'e' => '100% OTC verified', 't' => 'Same working day'],
                    4 => ['q' => 'Minor verifications pending', 'e' => '100% OTC verified', 't' => 'Same working day'],
                    3 => ['q' => 'Few pending verifications', 'e' => '95–99% verified', 't' => 'End of working day'],
                    2 => ['q' => 'Several unverified', 'e' => '<95% verified', 't' => 'Beyond working day'],
                    1 => ['q' => 'Verification not done', 'e' => 'Majority unverified', 't' => 'Unacceptable'],
                ],
                '95% encoded within the business day' => [
                    5 => ['q' => 'Encodings error-free', 'e' => '100% encoded', 't' => 'Same business day'],
                    4 => ['q' => 'Minor corrections', 'e' => '100% encoded', 't' => 'Same business day'],
                    3 => ['q' => 'Few delays', 'e' => '95–99% encoded', 't' => 'By end of day'],
                    2 => ['q' => 'Multiple delays', 'e' => '<95% encoded', 't' => 'Next day'],
                    1 => ['q' => 'Encoding largely incomplete', 'e' => 'Major backlog', 't' => 'Unacceptable'],
                ],
                'OR validation completed daily' => [
                    5 => ['q' => 'All ORs validated error-free', 'e' => '100% validated', 't' => 'Daily'],
                    4 => ['q' => 'Minor issues corrected same day', 'e' => '100% validated', 't' => 'Daily'],
                    3 => ['q' => 'Some validations late', 'e' => '95–99% validated', 't' => 'Within 48 hours'],
                    2 => ['q' => 'Frequent late validations', 'e' => '<95% validated', 't' => 'Beyond 48 hours'],
                    1 => ['q' => 'Validations mostly missing', 'e' => 'Majority unvalidated', 't' => 'Unacceptable'],
                ],
                'Weekly filing updated and retrievable' => [
                    5 => ['q' => 'Zero retrieval issues', 'e' => '100% weekly updates', 't' => 'Within week'],
                    4 => ['q' => 'Minor retrieval fixes', 'e' => '100% weekly updates', 't' => 'Within week'],
                    3 => ['q' => 'Some items late', 'e' => '95–99% updates', 't' => 'Within next week'],
                    2 => ['q' => 'Many late updates', 'e' => '<95% updates', 't' => 'Beyond next week'],
                    1 => ['q' => 'Updates not done', 'e' => 'Major gaps', 't' => 'Unacceptable'],
                ],
                'Digital backups synced monthly' => [
                    5 => ['q' => 'Backups verified', 'e' => '100% synced', 't' => 'Within month'],
                    4 => ['q' => 'Minor sync corrections', 'e' => '100% synced', 't' => 'Within month'],
                    3 => ['q' => 'Some delays', 'e' => '95–99% synced', 't' => 'Within following week'],
                    2 => ['q' => 'Frequent delays', 'e' => '<95% synced', 't' => 'Beyond following week'],
                    1 => ['q' => 'Backups largely missing', 'e' => 'Major gaps', 't' => 'Unacceptable'],
                ],
                'Retrieval logs maintained for audits' => [
                    5 => ['q' => 'Logs complete and audit-ready', 'e' => '100% requests logged', 't' => 'Same day'],
                    4 => ['q' => 'Minor log gaps corrected', 'e' => '100% requests logged', 't' => 'Same day'],
                    3 => ['q' => 'Some gaps', 'e' => '95–99% logged', 't' => 'Within 48 hours'],
                    2 => ['q' => 'Many gaps', 'e' => '<95% logged', 't' => 'Beyond 48 hours'],
                    1 => ['q' => 'Logs largely missing', 'e' => 'Majority unlogged', 't' => 'Unacceptable'],
                ],
            ];

            $functionSeedRMU = [
                [
                    'name' => 'Core Functions',
                    'function_type' => 'core',
                    'weight_percent' => 80,
                    'sort_order' => 1,
                    'mfos' => [
                        [
                            'title' => 'Daily Revenue Posting and Ledger Updating',
                            'target_quantity' => 4100,
                            'target_timeline' => 'collections posted within the semester',
                            'sort_order' => 1,
                            'indicators' => [
                                'All daily collections posted to the ledger within the day',
                                'Daily totals reconciled against validated ORs',
                                'Posting errors corrected within 24 hours',
                            ],
                        ],
                        [
                            'title' => 'Preparation of Monthly Revenue Reports',
                            'target_quantity' => 3200,
                            'target_timeline' => 'report submitted within 3 working days after semester-end',
                            'sort_order' => 2,
                            'indicators' => [
                                'Monthly revenue report prepared with complete schedules',
                                'Report figures match the ledger and subsidiary records',
                                'Report submitted on or before deadline',
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
                            'title' => 'Responding to revenue verification and audit requests',
                            'target_quantity' => 2700,
                            'target_timeline' => 'responses issued within before semester ends',
                            'sort_order' => 1,
                            'indicators' => [
                                'Audit request documents compiled complete and accurate',
                                'Verification responses issued within 2 working days',
                                'Follow-up clarifications resolved within 3 working days',
                            ],
                        ],
                    ],
                ],
            ];

            $standardsMapRMU = [
                'All daily collections posted to the ledger within the day' => [
                    5 => ['q' => 'Zero posting errors; entries accurate', 'e' => '100% posted', 't' => 'Same working day'],
                    4 => ['q' => 'Minor corrections only', 'e' => '100% posted', 't' => 'Same working day'],
                    3 => ['q' => 'Few correctable errors', 'e' => '95–99% posted', 't' => 'By end of day'],
                    2 => ['q' => 'Multiple errors requiring rework', 'e' => '<95% posted', 't' => 'Next day'],
                    1 => ['q' => 'Major inaccuracies', 'e' => 'Major backlog', 't' => 'Unacceptable delay'],
                ],
                'Daily totals reconciled against validated ORs' => [
                    5 => ['q' => 'Reconciled with zero variance', 'e' => 'All ORs included', 't' => 'Same day'],
                    4 => ['q' => 'Minor variance resolved', 'e' => 'All ORs included', 't' => 'Same day'],
                    3 => ['q' => 'Some variances corrected', 'e' => '95–99% ORs included', 't' => 'Within 24 hours'],
                    2 => ['q' => 'Frequent variances', 'e' => '<95% ORs included', 't' => 'Beyond 24 hours'],
                    1 => ['q' => 'Not reconciled', 'e' => 'Majority missing', 't' => 'Unacceptable'],
                ],
                'Posting errors corrected within 24 hours' => [
                    5 => ['q' => 'All corrections documented', 'e' => '100% corrected', 't' => 'Within 24 hours'],
                    4 => ['q' => 'Minor corrections documented', 'e' => '100% corrected', 't' => 'Within 24 hours'],
                    3 => ['q' => 'Some corrections delayed', 'e' => '95–99% corrected', 't' => 'Within 48 hours'],
                    2 => ['q' => 'Many corrections delayed', 'e' => '<95% corrected', 't' => 'Beyond 48 hours'],
                    1 => ['q' => 'Corrections not done', 'e' => 'Majority pending', 't' => 'Unacceptable'],
                ],
                'Monthly revenue report prepared with complete schedules' => [
                    5 => ['q' => 'Complete schedules, no gaps', 'e' => 'All sections included', 't' => 'Within 3 working days'],
                    4 => ['q' => 'Minor schedule tweaks', 'e' => 'All sections included', 't' => 'Within 3 working days'],
                    3 => ['q' => 'Some missing items fixed', 'e' => '95–99% complete', 't' => 'Within 5 working days'],
                    2 => ['q' => 'Many missing schedules', 'e' => '<95% complete', 't' => 'Beyond 5 working days'],
                    1 => ['q' => 'Report incomplete', 'e' => 'Majority missing', 't' => 'Unacceptable'],
                ],
                'Report figures match the ledger and subsidiary records' => [
                    5 => ['q' => 'Exact match, no variance', 'e' => 'All reconciled', 't' => 'Before submission'],
                    4 => ['q' => 'Minor variance resolved', 'e' => 'All reconciled', 't' => 'Before submission'],
                    3 => ['q' => 'Few variances corrected', 'e' => '95–99% reconciled', 't' => 'At submission'],
                    2 => ['q' => 'Frequent variances', 'e' => '<95% reconciled', 't' => 'After submission'],
                    1 => ['q' => 'Not reconciled', 'e' => 'Majority not reconciled', 't' => 'Unacceptable'],
                ],
                'Report submitted on or before deadline' => [
                    5 => ['q' => 'Submission complete', 'e' => 'All attachments included', 't' => 'On/before deadline'],
                    4 => ['q' => 'Minor attachment fixes', 'e' => 'All included', 't' => 'On/before deadline'],
                    3 => ['q' => 'Late minor attachment', 'e' => '95–99% included', 't' => '1 day late'],
                    2 => ['q' => 'Several missing attachments', 'e' => '<95% included', 't' => '2–3 days late'],
                    1 => ['q' => 'Not submitted/very late', 'e' => 'Majority missing', 't' => 'Unacceptable'],
                ],
                'Audit request documents compiled complete and accurate' => [
                    5 => ['q' => 'Complete packet, error-free', 'e' => 'All requested docs included', 't' => 'Within 2 working days'],
                    4 => ['q' => 'Minor formatting fixes', 'e' => 'All included', 't' => 'Within 2 working days'],
                    3 => ['q' => 'Some missing docs recovered', 'e' => '95–99% included', 't' => 'Within 3 working days'],
                    2 => ['q' => 'Many missing docs', 'e' => '<95% included', 't' => 'Beyond 3 working days'],
                    1 => ['q' => 'Packet incomplete', 'e' => 'Major gaps', 't' => 'Unacceptable'],
                ],
                'Verification responses issued within 2 working days' => [
                    5 => ['q' => 'Clear, accurate response', 'e' => 'All queries answered', 't' => 'Within 2 working days'],
                    4 => ['q' => 'Minor clarifications', 'e' => 'All answered', 't' => 'Within 2 working days'],
                    3 => ['q' => 'Some clarifications needed', 'e' => '95–99% answered', 't' => 'Within 3 working days'],
                    2 => ['q' => 'Many clarifications needed', 'e' => '<95% answered', 't' => 'Beyond 3 working days'],
                    1 => ['q' => 'Responses inadequate', 'e' => 'Majority unanswered', 't' => 'Unacceptable'],
                ],
                'Follow-up clarifications resolved within 3 working days' => [
                    5 => ['q' => 'Resolved fully with evidence', 'e' => 'All follow-ups closed', 't' => 'Within 3 working days'],
                    4 => ['q' => 'Minor evidence follow-up', 'e' => 'All closed', 't' => 'Within 3 working days'],
                    3 => ['q' => 'Some follow-ups delayed', 'e' => '95–99% closed', 't' => 'Within 5 working days'],
                    2 => ['q' => 'Many follow-ups delayed', 'e' => '<95% closed', 't' => 'Beyond 5 working days'],
                    1 => ['q' => 'Follow-ups not closed', 'e' => 'Majority open', 't' => 'Unacceptable'],
                ],
            ];

            $seedUwpTemplate(
                $uwpRCU,
                $functionSeedRCU,
                $standardsMapRCU
            );

            $seedUwpTemplate(
                $uwpRMU,
                $functionSeedRMU,
                $standardsMapRMU
            );
        });
    }
}
