<?php

namespace App\Http\Controllers\StageTwo\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PmtQarApprovalController extends Controller
{
    private const SESSION_KEY = 'stage2_pmt_qar_records';

    public function index(Request $request)
    {
        $records = $request->session()->get(self::SESSION_KEY);

        if (! is_array($records) || $records === []) {
            $records = [$this->defaultQar()];
            $request->session()->put(self::SESSION_KEY, $records);
        }

        $qars = collect($records)
            ->map(fn (array $record) => $this->normalizeQar($record))
            ->values();

        return view('pmt.qar', [
            'qars' => $qars,
            'pendingQars' => $qars->where('status', 'dept_head_approved')->values(),
        ]);
    }

    public function validateQar(Request $request, int $qar)
    {
        $records = $request->session()->get(self::SESSION_KEY, []);

        if (! is_array($records) || $records === []) {
            return redirect()->route('pmt.qar.index')
                ->with('error', 'No QAR record found for validation.');
        }

        $found = false;

        foreach ($records as &$record) {
            if ((int) ($record['id'] ?? 0) !== $qar) {
                continue;
            }

            $found = true;
            $status = (string) ($record['status'] ?? 'draft');

            if ($status === 'pmt_validated') {
                return redirect()->route('pmt.qar.index')
                    ->with('info', 'QAR is already PMT validated.');
            }

            if ($status !== 'dept_head_approved') {
                return redirect()->route('pmt.qar.index')
                    ->with('error', 'QAR is not ready for PMT final validation.');
            }

            $record['status'] = 'pmt_validated';
            $record['validated_by'] = $request->user()?->name ?? 'PMT';
            $record['validated_at'] = now()->toDateTimeString();
            break;
        }

        unset($record);

        if (! $found) {
            return redirect()->route('pmt.qar.index')
                ->with('error', 'No QAR record found for validation.');
        }

        $request->session()->put(self::SESSION_KEY, array_values($records));

        return redirect()->route('pmt.qar.index')
            ->with('success', 'QAR validated.');
    }

    private function defaultQar(): array
    {
        $rows = $this->applyTargetTimeline([
            [
                'ppa_code' => 'QAR-001',
                'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'indicator' => 'All e-bank transactions scanned and encoded daily',
                'actual_performance' => 1,
                'remarks' => 'From submitted MPOR (Jan 2026)',
            ],
            [
                'ppa_code' => 'QAR-002',
                'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                'indicator' => 'Same-day verification of OTC transactions',
                'actual_performance' => 12,
                'remarks' => 'From submitted MPOR (Jan 2026)',
            ],
        ]);

        return [
            'id' => 1,
            'office' => 'Revenue Collection Unit',
            'quarter' => 'Q1 2026',
            'status' => 'dept_head_approved',
            'prepared_by' => 'Dept Head',
            'prepared_date' => now()->subDay()->toDateTimeString(),
            'validated_by' => null,
            'validated_at' => null,
            'rows' => $rows,
        ];
    }

    private function normalizeQar(array $record): array
    {
        $status = (string) ($record['status'] ?? 'draft');

        if (! in_array($status, ['draft', 'dept_head_approved', 'pmt_validated'], true)) {
            $status = 'draft';
        }

        $rows = is_array($record['rows'] ?? null) ? $record['rows'] : [];
        $rows = array_values(array_map(static function ($row) {
            $row = is_array($row) ? $row : [];

            return array_merge([
                'ppa_code' => '-',
                'mfo' => '-',
                'indicator' => '-',
                'actual_performance' => '-',
                'remarks' => '-',
                'target_timeline' => '-',
            ], $row);
        }, $rows));
        $rows = $this->applyTargetTimeline($rows);

        return [
            'id' => (int) ($record['id'] ?? 0),
            'office' => (string) ($record['office'] ?? 'Revenue Collection Unit'),
            'quarter' => (string) ($record['quarter'] ?? 'Q1 2026'),
            'status' => $status,
            'prepared_by' => (string) ($record['prepared_by'] ?? 'Dept Head'),
            'prepared_date' => $record['prepared_date'] ?? null,
            'validated_by' => $record['validated_by'] ?? null,
            'validated_at' => $record['validated_at'] ?? null,
            'rows' => $rows,
        ];
    }

    private function getUwpTargetTimelineMap(): array
    {
        return [
            'QAR-001' => 'Daily; all e-bank transactions processed within the same working day',
            'QAR-002' => 'Daily; 95% processed within the same working day',
            'QAR-003' => 'Quarterly; records validated and properly filed',
        ];
    }

    private function applyTargetTimeline(array $rows): array
    {
        $map = $this->getUwpTargetTimelineMap();

        return array_values(array_map(static function ($row) use ($map) {
            $row = is_array($row) ? $row : [];
            $code = (string) ($row['ppa_code'] ?? '');
            $row['target_timeline'] = $map[$code] ?? '-';

            return $row;
        }, $rows));
    }
}
