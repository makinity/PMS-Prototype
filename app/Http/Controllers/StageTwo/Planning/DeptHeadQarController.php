<?php

namespace App\Http\Controllers\StageTwo\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeptHeadQarController extends Controller
{
    private const SESSION_KEY = 'stage2_dept_head_qar_state';

    public function index(Request $request)
    {
        $state = $this->getState($request);

        if (! ($state['seeded'] ?? false)) {
            $state['incoming_mpors'] = [
                [
                    'employee' => 'Ramon Reyes',
                    'month' => 'January 2026',
                    'status' => 'Submitted (Locked)',
                ],
            ];
            $state['consolidated_mpors'] = [];
            $state['qar_rows'] = [];
            $state['status'] = 'draft';
            $state['generated_at'] = null;
            $state['approved_at'] = null;
            $state['seeded'] = true;
            $request->session()->put(self::SESSION_KEY, $state);
        }

        $office = 'Revenue Collection Unit';
        $quarter = 'Q1 2026 (Jan-Mar)';

        $status = (string) ($state['status'] ?? 'draft');
        if (! in_array($status, ['draft', 'dept_head_approved'], true)) {
            $status = 'draft';
        }

        $incomingMpors = array_values($state['incoming_mpors'] ?? []);
        $consolidatedMpors = array_values($state['consolidated_mpors'] ?? []);
        $rows = array_values($state['qar_rows'] ?? []);
        $uwpTargetTimelineMap = $this->getUwpTargetTimelineMap();

        if ($rows !== []) {
            $rows = $this->applyUwpTargetsToRows($rows, $uwpTargetTimelineMap);
        }

        $mporDummyDetails = $this->buildMporDummyDetails($incomingMpors, $office);

        $includedEmployeeCount = count(array_unique(array_filter(array_map(
            static fn (array $mpor): string => (string) ($mpor['employee'] ?? ''),
            $consolidatedMpors
        ))));

        $includedMonthsCount = count(array_unique(array_filter(array_map(
            static fn (array $mpor): string => (string) ($mpor['month'] ?? ''),
            $consolidatedMpors
        ))));

        return view('dept-head.qar', [
            'office' => $office,
            'quarter' => $quarter,
            'status' => $status,
            'generatedAt' => $state['generated_at'] ?? null,
            'approvedAt' => $state['approved_at'] ?? null,
            'incomingMpors' => $incomingMpors,
            'consolidatedMpors' => $consolidatedMpors,
            'rows' => $rows,
            'uwpTargetTimelineMap' => $uwpTargetTimelineMap,
            'mporDummyDetails' => $mporDummyDetails,
            'includedMporCount' => count($consolidatedMpors),
            'includedEmployeeCount' => $includedEmployeeCount,
            'includedMonthsCount' => $includedMonthsCount,
            'includedMonthsTotal' => 3,
            'deptHeadName' => $request->user()?->name ?? 'Department Head',
            'pmtStatusLabel' => 'Pending PMT validation',
            'debugState' => [
                'seeded' => (bool) ($state['seeded'] ?? false),
                'status' => (string) ($state['status'] ?? 'draft'),
                'incoming_count' => count($incomingMpors),
                'consolidated_count' => count($consolidatedMpors),
                'rows_count' => count($rows),
                'generated_at' => $state['generated_at'] ?? null,
            ],
        ]);
    }

    public function generate(Request $request)
    {
        $state = $this->getState($request);

        if (($state['status'] ?? 'draft') === 'dept_head_approved') {
            return redirect()->route('dept-head.qar')
                ->with('info', 'QAR is already approved and locked at Dept Head level.');
        }

        $incomingMpors = array_values($state['incoming_mpors'] ?? []);
        if ($incomingMpors === []) {
            return redirect()->route('dept-head.qar')
                ->with('info', 'DEBUG: incoming empty; nothing to consolidate.');
        }

        $incomingBefore = count($incomingMpors);
        $state['consolidated_mpors'] = $incomingMpors;
        $state['incoming_mpors'] = [];

        $uwpTargetTimelineMap = $this->getUwpTargetTimelineMap();
        $rows = $this->buildDummyQarRows('Q1 2026 (Jan-Mar)');
        $state['qar_rows'] = $this->applyUwpTargetsToRows($rows, $uwpTargetTimelineMap);
        $state['generated_at'] = now()->toDateTimeString();
        $state['status'] = 'draft';
        $state['approved_at'] = null;

        $request->session()->put(self::SESSION_KEY, $state);
        $request->session()->save();

        return redirect()->route('dept-head.qar')
            ->with('success', 'DEBUG: generate hit. incoming(before)=' . $incomingBefore
                . ' consolidated(after)=' . count($state['consolidated_mpors'])
                . ' rows=' . count($state['qar_rows']));
    }

    public function approve(Request $request)
    {
        $state = $this->getState($request);

        if (($state['status'] ?? '') === 'dept_head_approved') {
            return redirect()->route('dept-head.qar')
                ->with('info', 'QAR is already approved.');
        }

        $generatedAt = $state['generated_at'] ?? null;
        $consolidatedMpors = array_values($state['consolidated_mpors'] ?? []);

        if (empty($generatedAt) || $consolidatedMpors === []) {
            return redirect()->route('dept-head.qar')
                ->with('info', 'Consolidate QAR first before approving.');
        }

        $state['status'] = 'dept_head_approved';
        $state['approved_at'] = now()->toDateTimeString();
        $state['generated_at'] = $generatedAt;

        $request->session()->put(self::SESSION_KEY, $state);
        $request->session()->save();

        return redirect()->route('dept-head.qar')
            ->with('success', 'QAR approved and forwarded for PMT validation.');
    }

    public function reset(Request $request)
    {
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->save();

        return redirect()->route('dept-head.qar')
            ->with('success', 'Prototype reset: QAR session cleared.');
    }

    private function getState(Request $request): array
    {
        $state = $request->session()->get(self::SESSION_KEY, []);

        if (! is_array($state)) {
            $state = [];
        }

        $state = array_merge([
            'status' => 'draft',
            'generated_at' => null,
            'approved_at' => null,
            'seeded' => false,
            'incoming_mpors' => [],
            'consolidated_mpors' => [],
            'qar_rows' => [],
        ], $state);

        if (! is_array($state['incoming_mpors'])) {
            $state['incoming_mpors'] = [];
        }

        if (! is_array($state['consolidated_mpors'])) {
            $state['consolidated_mpors'] = [];
        }

        if (! is_array($state['qar_rows'])) {
            $state['qar_rows'] = [];
        }

        return $state;
    }

    private function getUwpTargetTimelineMap(): array
    {
        return [
            'QAR-001' => 'Daily; all e-bank transactions processed within the same working day',
            'QAR-002' => 'Daily; 95% processed within the same working day',
            'QAR-003' => 'Quarterly; records validated and properly filed',
        ];
    }

    private function getBaseDummyMpor(string $office): array
    {
        return [
            'employee_name' => 'Ramon Reyes',
            'office_division' => $office,
            'month_label' => 'January 2026',
            'status' => 'Submitted (Locked)',
            'submitted_at' => 'Jan 31, 2026 5:12 PM',
            'groups' => [
                [
                    'label' => 'CORE FUNCTIONS',
                    'weight_label' => '80%',
                    'rows' => [
                        [
                            'task_title' => 'Processing of Over-the-Counter Revenue Transactions',
                            'eff' => ['w1' => 12, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 12],
                            'qual' => ['w1' => 60, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 60],
                            'time' => ['w1' => 60, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 60],
                        ],
                        [
                            'task_title' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                            'eff' => ['w1' => 1, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 1],
                            'qual' => ['w1' => 5, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 5],
                            'time' => ['w1' => 5, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 5],
                        ],
                    ],
                ],
                [
                    'label' => 'SUPPORT FUNCTIONS',
                    'weight_label' => '20%',
                    'rows' => [
                        [
                            'task_title' => 'Maintenance of Revenue Records Filing System',
                            'eff' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0],
                            'qual' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0],
                            'time' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0],
                        ],
                    ],
                ],
            ],
            'summary' => [
                'week1_total' => 13,
                'week2_total' => 0,
                'week3_total' => 0,
                'week4_total' => 0,
                'grand_total' => 13,
                'included_entries' => 2,
                'excluded_entries' => 3,
            ],
            'confirmed' => [
                'supervisor_name' => 'Carlo D. Beray',
                'employee_name' => 'Ramon Reyes',
            ],
            'evidence' => [
                [
                    'task_title' => 'Processing of Over-the-Counter Revenue Transactions',
                    'items' => [
                        ['label' => 'Screenshot - ORS Log W1', 'type' => 'image', 'note' => 'placeholder'],
                        ['label' => 'Monthly summary PDF', 'type' => 'pdf', 'note' => 'placeholder'],
                    ],
                ],
                [
                    'task_title' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                    'items' => [
                        ['label' => 'Scanned e-bank bundle', 'type' => 'image', 'note' => 'placeholder'],
                        ['label' => 'BSF verification sheet', 'type' => 'xlsx', 'note' => 'placeholder'],
                    ],
                ],
                [
                    'task_title' => 'Maintenance of Revenue Records Filing System',
                    'items' => [
                        ['label' => 'Records inventory checklist', 'type' => 'pdf', 'note' => 'placeholder'],
                    ],
                ],
            ],
            'employee_remarks' => 'Submitted all month-end documents and reconciled variances.',
        ];
    }

    private function buildMporKey(array $mpor): string
    {
        return Str::slug((string) ($mpor['employee'] ?? 'unknown') . '-' . (string) ($mpor['month'] ?? 'unknown'));
    }

    private function buildMporDummyDetails(array $incomingMpors, string $office): array
    {
        $baseDummy = $this->getBaseDummyMpor($office);
        $details = [];

        foreach ($incomingMpors as $mpor) {
            if (! is_array($mpor)) {
                continue;
            }

            $key = $this->buildMporKey($mpor);
            $details[$key] = array_merge($baseDummy, [
                'employee_name' => $mpor['employee'] ?? 'Unknown Employee',
                'office_division' => $office,
                'month_label' => $mpor['month'] ?? 'Unknown Month',
                'status' => $mpor['status'] ?? 'Submitted (Locked)',
            ]);
        }

        if ($details === []) {
            $details['ramon-reyes-january-2026'] = $baseDummy;
        }

        return $details;
    }

    private function buildDummyQarRows(string $quarter): array
    {
        return [
            [
                'ppa_code' => 'QAR-001',
                'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'indicator' => 'All e-bank transactions scanned and encoded daily',
                'actual_performance' => 1,
                'remarks' => 'From consolidated MPOR (Jan 2026)',
            ],
            [
                'ppa_code' => 'QAR-002',
                'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                'indicator' => 'Same-day verification of OTC transactions',
                'actual_performance' => 12,
                'remarks' => 'From consolidated MPOR (Jan 2026)',
            ],
        ];
    }

    private function applyUwpTargetsToRows(array $rows, array $uwpTargetTimelineMap): array
    {
        return array_map(static function ($row) use ($uwpTargetTimelineMap) {
            if (! is_array($row)) {
                return $row;
            }

            $code = (string) ($row['ppa_code'] ?? '');
            $row['target_timeline'] = $uwpTargetTimelineMap[$code] ?? '-';

            return $row;
        }, $rows);
    }
}

