<?php

namespace App\Http\Controllers\StageTwo\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

        $status = (string) ($state['status'] ?? 'draft');
        if (! in_array($status, ['draft', 'dept_head_approved'], true)) {
            $status = 'draft';
        }

        $incomingMpors = array_values($state['incoming_mpors'] ?? []);
        $consolidatedMpors = array_values($state['consolidated_mpors'] ?? []);
        $rows = array_values($state['qar_rows'] ?? []);

        $includedEmployeeCount = count(array_unique(array_filter(array_map(
            static fn (array $mpor): string => (string) ($mpor['employee'] ?? ''),
            $consolidatedMpors
        ))));

        $includedMonthsCount = count(array_unique(array_filter(array_map(
            static fn (array $mpor): string => (string) ($mpor['month'] ?? ''),
            $consolidatedMpors
        ))));

        return view('dept-head.qar', [
            'office' => 'Revenue Collection Unit',
            'quarter' => 'Q1 2026 (Jan-Mar)',
            'status' => $status,
            'generatedAt' => $state['generated_at'] ?? null,
            'approvedAt' => $state['approved_at'] ?? null,
            'incomingMpors' => $incomingMpors,
            'consolidatedMpors' => $consolidatedMpors,
            'rows' => $rows,
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
        $state['qar_rows'] = [
            [
                'ppa_code' => 'QAR-001',
                'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'indicator' => 'All e-bank transactions scanned and encoded daily',
                'target_output' => '-',
                'actual_performance' => 1,
                'variance' => '-',
                'remarks' => 'From consolidated MPOR (Jan 2026)',
            ],
            [
                'ppa_code' => 'QAR-002',
                'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                'indicator' => 'Same-day verification of OTC transactions',
                'target_output' => '-',
                'actual_performance' => 12,
                'variance' => '-',
                'remarks' => 'From consolidated MPOR (Jan 2026)',
            ],
        ];
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
}
