<?php

namespace App\Http\Controllers\StageTwo\Monitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeeAccomplishmentController extends Controller
{
    private const MPOR_SNAPSHOT_KEY = 'stage2_employee_mpor_snapshot';
    private const ACCOMPLISHMENT_STATE_KEY = 'stage2_employee_accomplishment_state';

    public function index(Request $request)
    {
        $snapshot = $this->getOrSeedMporSnapshot($request);
        $submissionState = $this->getSubmissionState($request);

        [$smporRows, $smporTotals] = $this->deriveSmporRows($snapshot['entries'] ?? []);
        $ipcrRows = $this->deriveIpcrRows($smporRows);

        $submittedAt = $submissionState['submitted_at'] ?? null;

        return view('employee.accomplishment-submission', [
            'employeeName' => $snapshot['employee'] ?? 'Ramon Reyes',
            'officeName' => $snapshot['office'] ?? 'Revenue Collection Unit',
            'periodLabel' => $snapshot['period'] ?? 'January-June 2026',
            'smporRows' => $smporRows,
            'smporTotals' => $smporTotals,
            'ipcrRows' => $ipcrRows,
            'submissionStatus' => $submissionState['status'] ?? 'draft',
            'submittedAt' => $submittedAt,
            'submittedAtLabel' => $submittedAt ? Carbon::parse($submittedAt)->format('M d, Y g:i A') : null,
            'remarksValue' => (string) ($submissionState['remarks'] ?? ''),
            'attachmentNames' => array_values($submissionState['attachments'] ?? []),
        ]);
    }

    public function submit(Request $request)
    {
        $state = $this->getSubmissionState($request);

        if (($state['status'] ?? 'draft') === 'submitted') {
            return redirect()
                ->route('employee.accomplishment-submission')
                ->with('info', 'Accomplishment submission is already locked.');
        }

        $validated = $request->validate([
            'remarks' => 'nullable|string|max:5000',
            'supporting_files.*' => 'file|max:10240',
        ]);

        $files = $request->file('supporting_files', []);
        $attachmentNames = [];
        foreach ($files as $file) {
            if ($file) {
                $attachmentNames[] = $file->getClientOriginalName();
            }
        }

        $state['status'] = 'submitted';
        $state['submitted_at'] = now()->toDateTimeString();
        $state['remarks'] = (string) ($validated['remarks'] ?? '');
        $state['attachments'] = $attachmentNames;

        $request->session()->put(self::ACCOMPLISHMENT_STATE_KEY, $state);
        $request->session()->save();

        return redirect()
            ->route('employee.accomplishment-submission')
            ->with('success', 'Accomplishments submitted to Supervisor & Dept Head.');
    }

    private function getOrSeedMporSnapshot(Request $request): array
    {
        $snapshot = $request->session()->get(self::MPOR_SNAPSHOT_KEY, []);

        if (! is_array($snapshot) || empty($snapshot)) {
            $snapshot = [
                'employee' => 'Ramon Reyes',
                'office' => 'Revenue Collection Unit',
                'period' => 'January-June 2026',
                'months' => [
                    'January 2026',
                    'February 2026',
                    'March 2026',
                    'April 2026',
                    'May 2026',
                    'June 2026',
                ],
                'status' => 'submitted',
                'entries' => [
                    [
                        'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                        'month' => 'January 2026',
                        'quantity' => 1,
                        'quality_rating' => 5,
                        'timeliness_rating' => 5,
                    ],
                    [
                        'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                        'month' => 'January 2026',
                        'quantity' => 12,
                        'quality_rating' => 5,
                        'timeliness_rating' => 5,
                    ],
                ],
            ];

            $request->session()->put(self::MPOR_SNAPSHOT_KEY, $snapshot);
            $request->session()->save();
        }

        return $snapshot;
    }

    private function getSubmissionState(Request $request): array
    {
        $state = $request->session()->get(self::ACCOMPLISHMENT_STATE_KEY, []);
        if (! is_array($state)) {
            $state = [];
        }

        return array_merge([
            'status' => 'draft',
            'submitted_at' => null,
            'remarks' => '',
            'attachments' => [],
        ], $state);
    }

    private function deriveSmporRows(array $entries): array
    {
        $grouped = [];

        foreach ($entries as $entry) {
            $mfo = (string) ($entry['mfo'] ?? '');
            if ($mfo === '') {
                continue;
            }

            $quantity = (float) ($entry['quantity'] ?? 0);
            $qualityRating = (float) ($entry['quality_rating'] ?? 0);
            $timelinessRating = (float) ($entry['timeliness_rating'] ?? 0);

            if (! isset($grouped[$mfo])) {
                $grouped[$mfo] = [
                    'mfo' => $mfo,
                    'total_quantity' => 0.0,
                    'total_quality_points' => 0.0,
                    'total_timeliness_points' => 0.0,
                ];
            }

            $grouped[$mfo]['total_quantity'] += $quantity;
            $grouped[$mfo]['total_quality_points'] += $quantity * $qualityRating;
            $grouped[$mfo]['total_timeliness_points'] += $quantity * $timelinessRating;
        }

        $rows = array_values(array_map(static function (array $row): array {
            return [
                'mfo' => $row['mfo'],
                'total_quantity' => (int) round($row['total_quantity']),
                'total_quality_points' => (int) round($row['total_quality_points']),
                'total_timeliness_points' => (int) round($row['total_timeliness_points']),
            ];
        }, $grouped));

        $totals = [
            'quantity' => array_sum(array_column($rows, 'total_quantity')),
            'quality_points' => array_sum(array_column($rows, 'total_quality_points')),
            'timeliness_points' => array_sum(array_column($rows, 'total_timeliness_points')),
        ];

        return [$rows, $totals];
    }

    private function deriveIpcrRows(array $smporRows): array
    {
        return array_map(static function (array $row): array {
            $quantity = (int) ($row['total_quantity'] ?? 0);

            return [
                'mfo' => $row['mfo'] ?? '',
                'accomplishment_summary' => 'Completed ' . $quantity . ' output(s) for the period based on submitted MPOR totals.',
                'evidence_label' => 'Attached (reference)',
            ];
        }, $smporRows);
    }
}

