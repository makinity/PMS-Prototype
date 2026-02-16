<?php

namespace App\Http\Controllers\StageTwo\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SupervisorMporController extends Controller
{
    private const SESSION_KEY = 'stage2_supervisor_mpor_records';

    public function index(Request $request)
    {
        $allowedStatuses = ['submitted', 'approved', 'endorsed'];
        $selectedStatus = strtolower((string) $request->query('status', 'submitted'));
        if (!in_array($selectedStatus, $allowedStatuses, true)) {
            $selectedStatus = 'submitted';
        }

        $records = $this->loadRecords($request);
        if (!$request->has('status') || $selectedStatus === 'submitted') {
            $records = $this->resetDefaultStatusToSubmitted($request, $records);
            $selectedStatus = 'submitted';
        }

        $filtered = $records
            ->where('status', $selectedStatus)
            ->values();

        $counts = [
            'submitted' => $records->where('status', 'submitted')->count(),
            'approved' => $records->where('status', 'approved')->count(),
            'endorsed' => $records->where('status', 'endorsed')->count(),
        ];

        return view('supervisor.mpor', [
            'mpors' => $filtered,
            'selectedStatus' => $selectedStatus,
            'counts' => $counts,
        ]);
    }

    private function loadRecords(Request $request): Collection
    {
        $sessionRecords = $request->session()->get(self::SESSION_KEY);
        if (!is_array($sessionRecords) || empty($sessionRecords)) {
            $sessionRecords = [$this->defaultRecord()];
            $request->session()->put(self::SESSION_KEY, $sessionRecords);
        }

        $records = collect($sessionRecords)
            ->filter(static fn ($record): bool => is_array($record))
            ->map(fn (array $record): array => $this->normalizeRecord($record))
            ->take(1)
            ->values();

        if ($records->isEmpty()) {
            $records = collect([$this->defaultRecord()]);
        }

        $request->session()->put(self::SESSION_KEY, $records->all());

        return $records;
    }

    private function resetDefaultStatusToSubmitted(Request $request, Collection $records): Collection
    {
        $first = $records->first();
        if (!is_array($first)) {
            $first = $this->defaultRecord();
        }

        $first['status'] = 'submitted';
        $first['approved_by'] = null;
        $first['approved_at'] = null;
        $first['endorsed_by'] = null;
        $first['endorsed_at'] = null;

        $reset = collect([$this->normalizeRecord($first)]);
        $request->session()->put(self::SESSION_KEY, $reset->all());

        return $reset;
    }

    private function defaultRecord(): array
    {
        return [
            'id' => 1,
            'employee' => 'Ramon Reyes',
            'office' => 'Revenue Collection Unit',
            'month' => 'January 2026',
            'status' => 'submitted',
            'submitted_at' => 'Jan 31, 2026',
            'approved_at' => null,
            'endorsed_at' => null,
            'preview' => [
                'outputs' => [
                    [
                        'title' => 'Processing of Over-the-Counter Revenue Transactions',
                        'qty' => ['w1' => 12, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 12],
                        'quality' => ['w1' => 60, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 60],
                        'timeliness' => ['w1' => 60, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 60],
                    ],
                    [
                        'title' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                        'qty' => ['w1' => 1, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 1],
                        'quality' => ['w1' => 5, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 5],
                        'timeliness' => ['w1' => 5, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 5],
                    ],
                    [
                        'title' => 'Maintenance of Revenue Records Filing System',
                        'qty' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0],
                        'quality' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0],
                        'timeliness' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0],
                    ],
                ],
            ],
        ];
    }

    private function normalizeRecord(array $record): array
    {
        $normalized = array_merge([
            'id' => 0,
            'employee' => 'Ramon Reyes',
            'office' => 'Revenue Collection Unit',
            'month' => 'January 2026',
            'status' => 'submitted',
            'submitted_at' => null,
            'approved_at' => null,
            'endorsed_at' => null,
            'preview' => ['outputs' => []],
        ], $record);

        $normalized['status'] = strtolower((string) $normalized['status']);
        if (!in_array($normalized['status'], ['submitted', 'approved', 'endorsed'], true)) {
            $normalized['status'] = 'submitted';
        }

        $outputs = is_array($normalized['preview']['outputs'] ?? null)
            ? $normalized['preview']['outputs']
            : [];

        $normalized['preview']['outputs'] = array_values(array_map(
            static function (array $output): array {
                $mergeWeekly = static function ($value): array {
                    $base = ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0];
                    if (!is_array($value)) {
                        return $base;
                    }

                    return array_merge($base, $value);
                };

                return [
                    'title' => (string) ($output['title'] ?? '-'),
                    'qty' => $mergeWeekly($output['qty'] ?? []),
                    'quality' => $mergeWeekly($output['quality'] ?? []),
                    'timeliness' => $mergeWeekly($output['timeliness'] ?? []),
                ];
            },
            $outputs
        ));

        return $normalized;
    }
}
