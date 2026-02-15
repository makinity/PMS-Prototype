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
        $allowedStatuses = ['submitted', 'endorsed'];
        $selectedStatus = strtolower((string) $request->query('status', 'submitted'));
        if (!in_array($selectedStatus, $allowedStatuses, true)) {
            $selectedStatus = 'submitted';
        }

        $records = $this->loadRecords($request);
        $filtered = $records
            ->where('status', $selectedStatus)
            ->values();

        $counts = [
            'submitted' => $records->where('status', 'submitted')->count(),
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

        return collect($sessionRecords)->values();
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
            'endorsed_at' => null,
            'preview' => [
                'outputs' => [
                    [
                        'title' => 'Processing of Over-the-Counter Revenue Transactions',
                        'qty' => ['w1' => 12, 'total' => 12],
                        'quality' => ['w1' => 60, 'total' => 60],
                        'timeliness' => ['w1' => 60, 'total' => 60],
                    ],
                    [
                        'title' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                        'qty' => ['w1' => 1, 'total' => 1],
                        'quality' => ['w1' => 5, 'total' => 5],
                        'timeliness' => ['w1' => 5, 'total' => 5],
                    ],
                    [
                        'title' => 'Maintenance of Revenue Records Filing System',
                        'qty' => ['w1' => 0, 'total' => 0],
                        'quality' => ['w1' => 0, 'total' => 0],
                        'timeliness' => ['w1' => 0, 'total' => 0],
                    ],
                ],
            ],
        ];
    }
}

