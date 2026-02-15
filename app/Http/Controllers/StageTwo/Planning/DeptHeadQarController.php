<?php

namespace App\Http\Controllers\StageTwo\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeptHeadQarController extends Controller
{
    private const SESSION_KEY = 'stage2_dept_head_qar_state';

    public function index(Request $request)
    {
        $state = $request->session()->get(self::SESSION_KEY, [
            'status' => 'draft',
            'generated_at' => null,
            'approved_at' => null,
        ]);

        $status = (string) ($state['status'] ?? 'draft');
        if (!in_array($status, ['draft', 'dept_head_approved'], true)) {
            $status = 'draft';
        }

        $office = 'Revenue Collection Unit';
        $quarter = 'Q1 2026 (Jan–Mar)';

        $includedMpors = [
            [
                'employee' => 'Ramon Reyes',
                'month' => 'January 2026',
                'status' => 'Submitted (Locked)',
            ],
        ];

        $rows = [
            [
                'ppa_code' => 'QAR-001',
                'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'indicator' => 'All e-bank transactions scanned and encoded daily',
                'target_output' => '—',
                'actual_performance' => 1,
                'variance' => '—',
                'remarks' => 'From submitted MPOR (Jan 2026)',
            ],
            [
                'ppa_code' => 'QAR-002',
                'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                'indicator' => 'Same-day verification of OTC transactions',
                'target_output' => '—',
                'actual_performance' => 12,
                'variance' => '—',
                'remarks' => 'From submitted MPOR (Jan 2026)',
            ],
        ];

        return view('dept-head.qar', [
            'office' => $office,
            'quarter' => $quarter,
            'status' => $status,
            'generatedAt' => $state['generated_at'] ?? null,
            'approvedAt' => $state['approved_at'] ?? null,
            'includedMpors' => $includedMpors,
            'rows' => $rows,
            'includedMporCount' => count($includedMpors),
            'includedEmployeeCount' => 1,
            'includedMonthsCount' => 1,
            'includedMonthsTotal' => 3,
            'deptHeadName' => $request->user()?->name ?? 'Department Head',
            'pmtStatusLabel' => 'Pending PMT validation',
        ]);
    }

    public function generate(Request $request)
    {
        $state = $request->session()->get(self::SESSION_KEY, [
            'status' => 'draft',
            'generated_at' => null,
            'approved_at' => null,
        ]);

        if (($state['status'] ?? 'draft') === 'dept_head_approved') {
            return redirect()->route('dept-head.qar')
                ->with('info', 'QAR is already approved and locked at Dept Head level.');
        }

        $state['status'] = 'draft';
        $state['generated_at'] = now()->toDateTimeString();
        $state['approved_at'] = null;

        $request->session()->put(self::SESSION_KEY, $state);

        return redirect()->route('dept-head.qar')
            ->with('success', 'QAR generated/refreshed from available submitted MPOR data.');
    }

    public function approve(Request $request)
    {
        $state = $request->session()->get(self::SESSION_KEY, [
            'status' => 'draft',
            'generated_at' => null,
            'approved_at' => null,
        ]);

        if (($state['status'] ?? '') === 'dept_head_approved') {
            return redirect()->route('dept-head.qar')
                ->with('info', 'QAR is already approved.');
        }

        $state['status'] = 'dept_head_approved';
        $state['approved_at'] = now()->toDateTimeString();
        $state['generated_at'] = $state['generated_at'] ?? now()->toDateTimeString();

        $request->session()->put(self::SESSION_KEY, $state);

        return redirect()->route('dept-head.qar')
            ->with('success', 'QAR approved and forwarded for PMT validation.');
    }
}

