<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\PerformancePeriod;
use App\Models\Ipcr;
use App\Models\IpcrItem;
use App\Models\Opcr;
use App\Models\OrsEntry;
use App\Models\OrsEntryEvidence;
use App\Models\OrsEntryMonitoring;
use App\Models\AccomplishmentSubmission;
use App\Models\MyTask;
use App\Models\Mpor;
use App\Models\QarHeader;
use App\Models\QarRow;
use App\Models\Smpor;
use App\Models\SmporItem;
use App\Models\TopPerformer;
use App\Models\DevelopmentPlan;
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
            $seedUser = function (array $attributes): User {
                $employeeId = (string) $attributes['employee_id'];
                $email = (string) $attributes['email'];

                $user = User::query()->where('employee_id', $employeeId)->first()
                    ?: User::query()->where('email', $email)->first()
                    ?: new User();

                $userKey = $user->exists ? $user->getKey() : 0;
                $emailIsAvailable = ! User::query()
                    ->where('email', $email)
                    ->whereKeyNot($userKey)
                    ->exists();
                $employeeIdIsAvailable = ! User::query()
                    ->where('employee_id', $employeeId)
                    ->whereKeyNot($userKey)
                    ->exists();

                if ($emailIsAvailable) {
                    $user->email = $email;
                }

                if ($employeeIdIsAvailable) {
                    $user->employee_id = $employeeId;
                }

                unset($attributes['email'], $attributes['employee_id']);
                $user->fill($attributes);
                $user->save();

                return $user;
            };

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

            $admin = $seedUser([
                    'employee_id' => 'ADM-0001',
                    'email' => 'admin@example.com',
                    'name' => 'admin',
                    'password' => $password,
                    'role' => 'admin',
                    'office_id' => null,
                    'position' => 'Administrator',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $pmt = $seedUser([
                    'employee_id' => 'PMT-0001',
                    'email' => 'pmt@example.com',
                    'name' => 'pmt',
                    'password' => $password,
                    'role' => 'pmt',
                    'office_id' => null,
                    'position' => 'PMT',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $deptHeadRCU = $seedUser([
                    'employee_id' => 'DH-RCU-0001',
                    'email' => 'dept-head.rcu@example.com',
                    'name' => 'dept-head',
                    'password' => $password,
                    'role' => 'dept-head',
                    'office_id' => $officeRCU->id,
                    'position' => 'Department Head',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $supervisorRCU = $seedUser([
                    'employee_id' => 'SUP-RCU-0001',
                    'email' => 'carlo.beray@example.com',
                    'name' => 'Carlo D. Beray',
                    'password' => $password,
                    'role' => 'supervisor',
                    'office_id' => $officeRCU->id,
                    'position' => 'Supervisor',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $supervisorRCU2 = $seedUser([
                    'employee_id' => 'SUP-RCU-0002',
                    'email' => 'juan.delacruz@example.com',
                    'name' => 'Juan Dela Cruz',
                    'password' => $password,
                    'role' => 'supervisor',
                    'office_id' => $officeRCU->id,
                    'position' => 'Supervisor',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $empRamon = $seedUser([
                    'employee_id' => 'EMP-RCU-0001',
                    'email' => 'ramon.reyes@example.com',
                    'name' => 'Ramon Reyes',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRCU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $empJustine = $seedUser([
                    'employee_id' => 'EMP-RCU-0004',
                    'email' => 'justineaguirre@example.com',
                    'name' => 'Justine Aguirre',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRCU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $empMark = $seedUser([
                    'employee_id' => 'EMP-RCU-0002',
                    'email' => 'marklionesios@gmail.com',
                    'name' => 'Mark Juntilla',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRCU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $empDenji = $seedUser([
                    'employee_id' => 'EMP-RCU-0003',
                    'email' => 'denjikun1030@gmail.com',
                    'name' => 'Denji Kun',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRCU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $deptHeadRMU = $seedUser([
                    'employee_id' => 'DH-RMU-0001',
                    'email' => 'dept-head.rmu@example.com',
                    'name' => 'dept-head-rmu',
                    'password' => $password,
                    'role' => 'dept-head',
                    'office_id' => $officeRMU->id,
                    'position' => 'Department Head',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $supervisorRMU = $seedUser([
                    'employee_id' => 'SUP-RMU-0001',
                    'email' => 'maria.navarro@example.com',
                    'name' => 'Maria P. Navarro',
                    'password' => $password,
                    'role' => 'supervisor',
                    'office_id' => $officeRMU->id,
                    'position' => 'Supervisor',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $empMilo = $seedUser([
                    'employee_id' => 'EMP-RMU-0001',
                    'email' => 'milo.ramos@example.com',
                    'name' => 'Milo Ramos',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRMU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

            $empXuiie = $seedUser([
                    'employee_id' => 'EMP-RMU-0002',
                    'email' => 'xuiie.fernandez@example.com',
                    'name' => 'Xuiie Fernandez',
                    'password' => $password,
                    'role' => 'employee',
                    'office_id' => $officeRMU->id,
                    'position' => 'Employee',
                    'is_active' => true,
                    'activated_at' => $now,
            ]);

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
                    'created_by' => $supervisorRCU->id,
                ],
                [
                    'status' => UnitWorkPlan::STATUS_DRAFT,
                    'submitted_at' => null,
                    'locked_at' => null,
                ]
            );

            $uwpRCU2 = UnitWorkPlan::query()->updateOrCreate(
                [
                    'office_id' => $officeRCU->id,
                    'performance_period_id' => $period->id,
                    'created_by' => $supervisorRCU2->id,
                ],
                [
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
                    'status' => UnitWorkPlan::STATUS_PMT_APPROVED,
                    'submitted_at' => now()->subDays(5),
                    'locked_at' => now()->subDays(4),
                ]
            );

            $seedUwpTemplate = function (
                UnitWorkPlan $uwp,
                array $functionSeed,
                array $standardsMap,
                array $employeeIds = []
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

                        foreach ($mfoData['indicators'] as $indicatorSort => $indicatorSeed) {
                            $indicatorText = is_array($indicatorSeed)
                                ? trim((string) ($indicatorSeed['indicator_text'] ?? $indicatorSeed['text'] ?? ''))
                                : trim((string) $indicatorSeed);

                            if ($indicatorText === '') {
                                continue;
                            }

                            $indicator = UwpSuccessIndicator::query()->create([
                                'uwp_mfo_id' => $mfo->id,
                                'indicator_text' => $indicatorText,
                                'target_quantity' => is_array($indicatorSeed)
                                    ? ($indicatorSeed['target_quantity'] ?? $mfoData['target_quantity'] ?? null)
                                    : ($mfoData['target_quantity'] ?? null),
                                'target_timeline' => is_array($indicatorSeed)
                                    ? ($indicatorSeed['target_timeline'] ?? $mfoData['target_timeline'] ?? null)
                                    : ($mfoData['target_timeline'] ?? null),
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

                            // Automatically assign employees to this indicator
                            foreach ($employeeIds as $eid) {
                                UwpIndicatorAssignment::query()->create([
                                    'uwp_success_indicator_id' => $indicator->id,
                                    'employee_id' => $eid,
                                    'assigned_by' => $uwp->created_by,
                                    'assigned_at' => now(),
                                ]);
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

            $rcuEmployeeIds = [$empRamon->id, $empMark->id, $empDenji->id];
            $rmuEmployeeIds = [$empMilo->id, $empXuiie->id];

            $seedUwpTemplate(
                $uwpRCU,
                $functionSeedRCU,
                $standardsMapRCU,
                $rcuEmployeeIds
            );

            $seedUwpTemplate(
                $uwpRCU2,
                $functionSeedRMU,
                $standardsMapRMU,
                $rcuEmployeeIds
            );

            $seedUwpTemplate(
                $uwpRMU,
                $functionSeedRMU,
                $standardsMapRMU,
                $rmuEmployeeIds
            );

            // --- GENERATE OPCRs ---
            $opcrRMU = Opcr::query()->firstOrCreate(
                [
                    'unit_work_plan_id' => $uwpRMU->id,
                    'office_id' => $officeRMU->id,
                    'performance_period_id' => $period->id,
                ],
                [
                    'generated_by' => $supervisorRMU->id,
                    'status' => Opcr::STATUS_APPROVED,
                    'submitted_at' => now()->subDays(10),
                    'approved_at' => now()->subDays(9),
                    'pmt_reviewed_at' => null,
                    'pmt_reviewed_by' => null,
                    'final_score' => null,
                    'adjectival_rating' => null,
                    'released_at' => null,
                ]
            );

            // --- Helper: seed IPCR + OrsEntries + MyTasks + Mpor for employees ---
            $seedEmployeeData = function (
                array $employeeIds,
                Opcr $opcr,
                UnitWorkPlan $uwp,
                int $supervisorId,
                int $officeId,
                PerformancePeriod $period,
                bool $perWeek = false
            ): void {
                $maxDay = (int) now()->format('d');

                foreach ($employeeIds as $employeeId) {
                    $ipcr = Ipcr::query()->firstOrCreate(
                        [
                            'opcr_id' => $opcr->id,
                            'unit_work_plan_id' => $uwp->id,
                            'employee_id' => $employeeId,
                            'performance_period_id' => $period->id,
                            'office_id' => $officeId,
                        ],
                        [
                            'status' => Ipcr::STATUS_COMMITTED,
                            'generated_at' => now()->subDays(14),
                            'committed_at' => now()->subDays(12),
                        ]
                    );

                    $assignments = UwpIndicatorAssignment::query()
                        ->where('employee_id', $employeeId)
                        ->with(['successIndicator.uwpMfo.uwpFunction'])
                        ->get();

                    foreach ($assignments as $assignment) {
                        $indicator = $assignment->successIndicator;
                        $mfo = $indicator->uwpMfo;
                        $function = $mfo->uwpFunction;

                        if ($function->unit_work_plan_id !== $uwp->id) {
                            continue;
                        }

                        $qetStandards = UwpQetStandard::query()
                            ->where('uwp_success_indicator_id', $indicator->id)
                            ->get();

                        $standardsPayload = [];
                        foreach ($qetStandards as $std) {
                            $standardsPayload[$std->rating][$std->dimension] = $std->standard_text;
                        }

                        $ipcrItem = IpcrItem::query()->firstOrCreate(
                            [
                                'ipcr_id' => $ipcr->id,
                                'uwp_function_id' => $function->id,
                                'uwp_success_indicator_id' => $indicator->id,
                            ],
                            [
                                'output_title' => $mfo->title,
                                'function_type' => $function->function_type,
                                'indicator_text' => $indicator->indicator_text,
                                'target_quantity' => $indicator->target_quantity,
                                'target_timeline' => $indicator->target_timeline,
                                'target_summary' => '',
                                'standards_payload' => $standardsPayload,
                            ]
                        );

                        // One entry per picked day (one per week if perWeek)
                        $monthDays = [];
                        if ($perWeek) {
                            for ($d = 1; $d <= $maxDay; $d += 7) {
                                $monthDays[] = rand($d, min($d + 6, $maxDay));
                            }
                        } else {
                            $monthDays = [rand(1, $maxDay)];
                        }

                        foreach ($monthDays as $dayIndex) {
                            $workDate = now()->startOfMonth()->addDays($dayIndex - 1)->startOfDay();

                            $orsEntry = OrsEntry::query()->firstOrCreate(
                                [
                                    'employee_id' => $employeeId,
                                    'ipcr_item_id' => $ipcrItem->id,
                                    'work_date' => $workDate,
                                ],
                                [
                                    'supervisor_id' => $supervisorId,
                                    'office_id' => $officeId,
                                    'performance_period_id' => $period->id,
                                    'ipcr_id' => $ipcr->id,
                                    'notes' => "Worked on {$indicator->indicator_text}",
                                    'quantity' => rand(20, 100),
                                    'status' => 'draft',
                                    'submitted_at' => null,
                                    'started_at' => null,
                                    'stopped_at' => null,
                                    'total_seconds' => rand(3600, 14400),
                                ]
                            );

                            MyTask::query()->firstOrCreate(
                                ['ors_entry_id' => $orsEntry->id],
                                [
                                    'employee_id' => $employeeId,
                                    'office_id' => $officeId,
                                    'performance_period_id' => $period->id,
                                    'ipcr_id' => $ipcr->id,
                                    'ipcr_item_id' => $ipcrItem->id,
                                    'work_date' => $workDate,
                                    'notes' => $orsEntry->notes,
                                    'quantity' => $orsEntry->quantity,
                                    'started_at' => $orsEntry->started_at,
                                    'stopped_at' => $orsEntry->stopped_at,
                                    'total_seconds' => $orsEntry->total_seconds,
                                    'status' => 'draft',
                                    'submitted_at' => $orsEntry->submitted_at,
                                    'locked_at' => null,
                                    'has_evidence' => false,
                                ]
                            );
                        }
                    }

                    Mpor::query()->firstOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'office_id' => $officeId,
                            'month' => now()->subMonth()->format('Y-m'),
                        ],
                        [
                            'status' => 'approved',
                            'generated_at' => now()->subDays(10),
                            'submitted_at' => now()->subDays(9),
                            'approved_by' => $supervisorId,
                            'approved_at' => now()->subDays(8),
                        ]
                    );
                }
            };

            // Seed RMU employees — one entry per week across the month
            $seedEmployeeData($rmuEmployeeIds, $opcrRMU, $uwpRMU, $supervisorRMU->id, $officeRMU->id, $period, true);

            // --- RATE ALL RMU ORS ENTRIES + ADD EVIDENCE ---
            $rmuOrsEntries = OrsEntry::query()
                ->where('office_id', $officeRMU->id)
                ->where('performance_period_id', $period->id)
                ->get();

            foreach ($rmuOrsEntries as $entry) {
                $entry->update(['status' => 'rated', 'submitted_at' => now()->subDays(3)]);

                OrsEntryMonitoring::query()->firstOrCreate(
                    ['ors_entry_id' => $entry->id, 'supervisor_id' => $supervisorRMU->id],
                    [
                        'quality_rating' => rand(4, 5),
                        'timeliness_rating' => rand(4, 5),
                        'remarks' => 'Good work.',
                        'rated_at' => now()->subDays(2),
                    ]
                );

                OrsEntryEvidence::query()->firstOrCreate(
                    ['ors_entry_id' => $entry->id, 'file_name' => 'Sample Evidence.pdf'],
                    [
                        'file_path' => 'sample-evidence/Sample Evidence.pdf',
                        'mime_type' => 'application/pdf',
                        'file_size' => 13901,
                        'uploaded_at' => now()->subDays(3),
                    ]
                );

                // Update MyTask to submitted
                MyTask::query()->where('ors_entry_id', $entry->id)->update(['status' => 'submitted', 'submitted_at' => now()->subDays(3), 'has_evidence' => true]);
            }

            // --- SUBMIT & APPROVE MPORs FOR RMU ---
            $currentMonth = now()->format('Y-m');
            foreach ($rmuEmployeeIds as $employeeId) {
                Mpor::query()->updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'month' => $currentMonth,
                    ],
                    [
                        'office_id' => $officeRMU->id,
                        'status' => 'endorsed',
                        'submitted_at' => now()->subDays(2),
                        'approved_at' => now()->subDays(1),
                        'approved_by' => $supervisorRMU->id,
                        'endorsed_at' => now()->subHours(12),
                        'endorsed_by' => $supervisorRMU->id,
                    ]
                );
            }

            // --- GENERATE QAR (Q2 for current month) ---
            $quarterKey = now()->year . '-Q' . (int) ceil(now()->month / 3);
            $qarHeader = QarHeader::query()->firstOrCreate(
                [
                    'office_id' => $officeRMU->id,
                    'performance_period_id' => $period->id,
                    'quarter_key' => $quarterKey,
                ],
                [
                    'status' => QarHeader::STATUS_PMT_APPROVED,
                    'generated_at' => now()->subDays(5),
                    'generated_by' => $deptHeadRMU->id,
                    'approved_at' => now()->subDays(4),
                    'approved_by' => $deptHeadRMU->id,
                    'pmt_status' => QarHeader::PMT_VALIDATED,
                    'pmt_validated_at' => now()->subDays(3),
                ]
            );

            // QAR Rows
            $mfoCount = 1;
            foreach ($functionSeedRMU as $func) {
                foreach ($func['mfos'] as $mfo) {
                    $tqty = number_format((float) ($mfo['target_quantity'] ?? 100));
                    $tline = $mfo['target_timeline'] ?? 'Semester';
                    QarRow::query()->firstOrCreate(
                        ['qar_header_id' => $qarHeader->id, 'ppa_code' => (string) $mfoCount, 'mfo_title' => $mfo['title'], 'indicator_text' => $mfo['indicators'][0] ?? 'Indicator'],
                        [
                            'target_quantity' => $mfo['target_quantity'] ?? 100,
                            'target_timeline' => "{$tqty} {$tline}",
                            'actual_performance' => rand(150, 300),
                            'variance' => ($mfo['target_quantity'] ?? 100) - rand(150, 300),
                            'remarks' => 'Consolidated from multiple employee MPORs',
                            'sort_order' => $mfoCount,
                        ]
                    );
                    $mfoCount++;
                }
            }

            // --- CALCULATE IPCR SCORES ---
            $ratingService = app(\App\Services\PerformanceRatingService::class);
            foreach ($rmuEmployeeIds as $employeeId) {
                $ipcr = Ipcr::query()->where('employee_id', $employeeId)->where('performance_period_id', $period->id)->first();
                if ($ipcr) {
                    $ratingService->calculateAndSaveFinalScore($ipcr);
                    $ipcr->update(['status' => Ipcr::STATUS_RELEASED_BY_PMT, 'released_by' => $pmt->id, 'released_at' => now()->subDays(1), 'finalized_at' => now()->subDays(1), 'locked_at' => now()->subDays(1)]);
                }
            }

            // --- UPDATE OPCR SCORE ---
            $ipcrScores = Ipcr::where('office_id', $officeRMU->id)->where('performance_period_id', $period->id)->whereNotNull('final_score')->where('final_score', '>', 0)->pluck('final_score');
            if ($ipcrScores->isNotEmpty()) {
                $avgScore = round($ipcrScores->avg(), 2);
                $opcrRMU->update([
                    'final_score' => $avgScore,
                    'adjectival_rating' => $ratingService->resolveAdjectivalRating($avgScore),
                    'status' => Opcr::STATUS_RELEASED_BY_PMT,
                    'released_by' => $pmt->id,
                    'released_at' => now()->subDays(1),
                    'locked_at' => now()->subDays(1),
                ]);
            }

            // --- ACCOMPLISHMENT SUBMISSIONS ---
            foreach ($rmuEmployeeIds as $employeeId) {
                $ipcr = Ipcr::query()->where('employee_id', $employeeId)->where('performance_period_id', $period->id)->first();
                if ($ipcr) {
                    AccomplishmentSubmission::query()->firstOrCreate(
                        ['employee_id' => $employeeId, 'performance_period_id' => $period->id],
                        [
                            'office_id' => $officeRMU->id,
                            'ipcr_id' => $ipcr->id,
                            'dataset_source' => 'qar_official',
                            'qar_header_id' => $qarHeader->id,
                            'status' => AccomplishmentSubmission::STATUS_RELEASED_BY_PMT,
                            'submitted_at' => now()->subDays(3),
                            'supervisor_id' => $supervisorRMU->id,
                            'supervisor_action_at' => now()->subDays(2),
                            'dept_head_id' => $deptHeadRMU->id,
                            'dept_head_action_at' => now()->subDays(2),
                            'pmt_id' => $pmt->id,
                            'pmt_action_at' => now()->subDays(1),
                        ]
                    );
                }
            }

            // --- GENERATE SMPOR ---
            $smpor = Smpor::query()->firstOrCreate(
                ['qar_header_id' => $qarHeader->id, 'office_id' => $officeRMU->id, 'performance_period_id' => $period->id, 'quarter_key' => $quarterKey],
                ['generated_at' => now()->subDays(3), 'generated_by' => $deptHeadRMU->id, 'avg_quality' => 4.60, 'avg_timeliness' => 4.80, 'overall_score' => 4.70, 'adjectival_rating' => 'Outstanding']
            );

            foreach ($rmuEmployeeIds as $index => $employeeId) {
                SmporItem::query()->firstOrCreate(
                    ['smpor_id' => $smpor->id, 'employee_id' => $employeeId],
                    ['quality_avg' => 4.5 + ($index * 0.1), 'timeliness_avg' => 4.6 + ($index * 0.1), 'overall_score' => 4.55 + ($index * 0.1), 'adjectival_rating' => 'Outstanding']
                );
            }

            // --- GENERATE TOP PERFORMERS ---
            TopPerformer::query()->firstOrCreate(
                [
                    'performer_type' => TopPerformer::TYPE_OFFICE,
                    'office_id' => $officeRMU->id,
                    'performance_period_id' => $period->id,
                    'rank' => 1,
                ],
                [
                    'source_record_id' => $opcrRMU->id,
                    'opcr_id' => $opcrRMU->id,
                    'performer_name' => 'Revenue Management Unit',
                    'office_name' => 'Revenue Management Unit',
                    'department_head_name' => $deptHeadRMU->name,
                    'official_score' => 4.70,
                    'official_rating' => 'Outstanding',
                    'released_at' => now(),
                ]
            );

            foreach ($rmuEmployeeIds as $index => $employeeId) {
                $employee = User::find($employeeId);
                $ipcr = Ipcr::where('employee_id', $employeeId)->where('performance_period_id', $period->id)->first();
                TopPerformer::query()->firstOrCreate(
                    [
                        'performer_type' => TopPerformer::TYPE_EMPLOYEE,
                        'employee_id' => $employeeId,
                        'performance_period_id' => $period->id,
                        'rank' => count($rmuEmployeeIds) - $index,
                    ],
                    [
                        'source_record_id' => $ipcr?->id,
                        'ipcr_id' => $ipcr?->id,
                        'office_id' => $officeRMU->id,
                        'performer_name' => $employee->name,
                        'office_name' => 'Revenue Management Unit',
                        'official_score' => $ipcr?->final_score ?? (4.55 + ($index * 0.1)),
                        'official_rating' => $ipcr?->adjectival_rating ?? 'Outstanding',
                        'released_at' => now(),
                    ]
                );

                // Development Plan
                if ($ipcr) {
                    DevelopmentPlan::query()->firstOrCreate(
                        ['ipcr_id' => $ipcr->id, 'employee_id' => $employeeId, 'office_id' => $officeRMU->id, 'performance_period_id' => $period->id],
                        [
                            'source_score' => $ipcr->final_score ?? 4.50,
                            'source_rating' => $ipcr->adjectival_rating ?? 'Outstanding',
                            'status' => DevelopmentPlan::STATUS_SUBMITTED_TO_LD,
                            'pmt_remarks' => 'Recommended for leadership training.',
                            'created_by' => $supervisorRMU->id,
                            'submitted_to_ld_at' => now()->subDays(1),
                        ]
                    );
                }
            }
        });
    }
}
