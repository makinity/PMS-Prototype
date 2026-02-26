<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Models\PerformancePeriod;
use App\Models\QarHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QarController extends Controller
{
    public function index(Request $request)
    {
        $officeSearch = trim((string) $request->query('office', ''));

        $period = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();

        $quarterContext = $this->resolveQuarterContext($request, $period);
        $period = $quarterContext['period'];
        $quarterNumber = $quarterContext['quarterNumber'];
        $quarterKey = $quarterContext['quarterKey'];
        $quarterLabel = $quarterContext['quarterLabel'];
        $allowedQuarterNumbers = $quarterContext['allowedQuarterNumbers'];
        $allowedQuarterOptions = $quarterContext['allowedQuarterOptions'];
        $selectedQuarterNumber = $quarterContext['selectedQuarterNumber'];

        $headers = collect();

        if ($period) {
            $headersQuery = QarHeader::query()
                ->with([
                    'office:id,name',
                    'performancePeriod:id,name,start_date,end_date',
                    'approver:id,name',
                    'pmtValidator:id,name',
                    'rows:id,qar_header_id,ppa_code,mfo_title,indicator_text,target_timeline,actual_performance,remarks,sort_order',
                    'mporLinks:id,qar_header_id,mpor_id,employee_name,month_label,status_label',
                ])
                ->where('performance_period_id', $period->id)
                ->where('quarter_key', $quarterKey)
                ->whereIn('status', [QarHeader::STATUS_DEPT_HEAD_ENDORSED, QarHeader::STATUS_PMT_APPROVED]);

            if ($officeSearch !== '') {
                $headersQuery->whereHas('office', function ($q) use ($officeSearch): void {
                    $q->where('name', 'like', '%' . $officeSearch . '%');
                });
            }

            $headers = $headersQuery
                ->orderByRaw(
                    "CASE
                        WHEN status = ? THEN 0
                        WHEN status = ? THEN 1
                        ELSE 2
                    END",
                    [QarHeader::STATUS_PMT_APPROVED, QarHeader::STATUS_DEPT_HEAD_ENDORSED]
                )
                ->orderByDesc('pmt_validated_at')
                ->orderByDesc('approved_at')
                ->orderByDesc('generated_at')
                ->orderByDesc('id')
                ->get();
        }

        $endorsedCount = $headers->where('status', QarHeader::STATUS_DEPT_HEAD_ENDORSED)->count();
        $approvedCount = $headers->where('status', QarHeader::STATUS_PMT_APPROVED)->count();

        return view('pmt.qar', compact(
            'period',
            'quarterNumber',
            'quarterKey',
            'quarterLabel',
            'allowedQuarterNumbers',
            'allowedQuarterOptions',
            'selectedQuarterNumber',
            'headers',
            'endorsedCount',
            'approvedCount',
            'officeSearch'
        ));
    }

    public function approve(Request $request, QarHeader $qarHeader)
    {
        $period = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();
        $quarterContext = $this->resolveQuarterContext($request, $period);
        $selectedQuarterNumber = $quarterContext['selectedQuarterNumber'];
        $redirectParams = $this->buildQuarterRedirectParams($request, $selectedQuarterNumber);

        if ($qarHeader->status === QarHeader::STATUS_PMT_APPROVED) {
            return redirect()
                ->route('pmt.qar', $redirectParams)
                ->with('info', 'QAR already approved.');
        }

        if ($qarHeader->status !== QarHeader::STATUS_DEPT_HEAD_ENDORSED) {
            return redirect()
                ->route('pmt.qar', $redirectParams)
                ->with('info', 'QAR must be endorsed by Dept Head first.');
        }

        DB::transaction(function () use ($request, $qarHeader): void {
            $qarHeader->status = QarHeader::STATUS_PMT_APPROVED;
            $qarHeader->pmt_status = QarHeader::PMT_VALIDATED;
            $qarHeader->pmt_validated_at = now();
            $qarHeader->pmt_validated_by = $request->user()?->id;
            $qarHeader->save();
        });

        return redirect()
            ->route('pmt.qar', $redirectParams)
            ->with('success', 'QAR approved. Employees may now proceed to IPCR/SMPOR accomplishments.');
    }

    public function return(Request $request, QarHeader $qarHeader)
    {
        $period = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();
        $quarterContext = $this->resolveQuarterContext($request, $period);
        $selectedQuarterNumber = $quarterContext['selectedQuarterNumber'];
        $redirectParams = $this->buildQuarterRedirectParams($request, $selectedQuarterNumber);

        if ($qarHeader->status === QarHeader::STATUS_PMT_APPROVED) {
            return redirect()
                ->route('pmt.qar', $redirectParams)
                ->with('info', 'Already approved; cannot return.');
        }

        if ($qarHeader->status !== QarHeader::STATUS_DEPT_HEAD_ENDORSED) {
            return redirect()
                ->route('pmt.qar', $redirectParams)
                ->with('info', 'QAR must be endorsed by Dept Head first.');
        }

        DB::transaction(function () use ($qarHeader): void {
            $qarHeader->pmt_status = QarHeader::PMT_RETURNED;
            $qarHeader->pmt_validated_at = null;
            $qarHeader->pmt_validated_by = null;
            $qarHeader->save();
        });

        return redirect()
            ->route('pmt.qar', $redirectParams)
            ->with('success', 'QAR returned to Dept Head.');
    }

    private function resolveQuarterContext(Request $request, ?PerformancePeriod $period = null): array
    {
        $period = $period ?: PerformancePeriod::query()
            ->where('is_active', true)
            ->first();

        $quarterData = $this->buildAllowedQuarterData($period);
        $allowedQuarterNumbers = $quarterData['allowedQuarterNumbers'];
        $allowedQuarterOptions = $quarterData['allowedQuarterOptions'];
        $yearForQuarterKey = $quarterData['yearForQuarterKey'];

        $requestedQ = (int) $request->input('q', 0);
        $currentQ = (int) ceil(now()->month / 3);

        if (in_array($requestedQ, $allowedQuarterNumbers, true)) {
            $selectedQuarterNumber = $requestedQ;
        } elseif (in_array($currentQ, $allowedQuarterNumbers, true)) {
            $selectedQuarterNumber = $currentQ;
        } else {
            $selectedQuarterNumber = (int) ($allowedQuarterNumbers[0] ?? $currentQ);
        }

        $quarterNumber = $selectedQuarterNumber;
        $quarterKey = sprintf('%d-Q%d', $yearForQuarterKey, $quarterNumber);
        $quarterLabel = sprintf('Q%d %d', $quarterNumber, $yearForQuarterKey);

        return [
            'period' => $period,
            'year' => $yearForQuarterKey,
            'quarterNumber' => $quarterNumber,
            'quarterKey' => $quarterKey,
            'quarterLabel' => $quarterLabel,
            'allowedQuarterNumbers' => $allowedQuarterNumbers,
            'allowedQuarterOptions' => $allowedQuarterOptions,
            'selectedQuarterNumber' => $selectedQuarterNumber,
        ];
    }

    private function buildAllowedQuarterData(?PerformancePeriod $period): array
    {
        $now = now();
        $currentQuarter = (int) ceil($now->month / 3);
        $yearForQuarterKey = (int) $now->year;

        if (!$period || empty($period->start_date) || empty($period->end_date)) {
            return [
                'allowedQuarterNumbers' => [$currentQuarter],
                'allowedQuarterOptions' => [
                    ['value' => $currentQuarter, 'label' => 'Q' . $currentQuarter],
                ],
                'yearForQuarterKey' => $yearForQuarterKey,
            ];
        }

        try {
            $start = Carbon::parse($period->start_date)->startOfMonth();
            $end = Carbon::parse($period->end_date)->startOfMonth();
        } catch (\Throwable) {
            return [
                'allowedQuarterNumbers' => [$currentQuarter],
                'allowedQuarterOptions' => [
                    ['value' => $currentQuarter, 'label' => 'Q' . $currentQuarter],
                ],
                'yearForQuarterKey' => $yearForQuarterKey,
            ];
        }

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $yearForQuarterKey = (int) $start->year;

        $quarters = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $quarters[] = (int) ceil($cursor->month / 3);
            $cursor->addMonthNoOverflow();
        }

        $allowedQuarterNumbers = collect($quarters)
            ->filter(fn ($q) => $q >= 1 && $q <= 4)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (empty($allowedQuarterNumbers)) {
            $allowedQuarterNumbers = [$currentQuarter];
        }

        $allowedQuarterOptions = collect($allowedQuarterNumbers)
            ->map(fn (int $q) => ['value' => $q, 'label' => 'Q' . $q])
            ->values()
            ->all();

        return [
            'allowedQuarterNumbers' => $allowedQuarterNumbers,
            'allowedQuarterOptions' => $allowedQuarterOptions,
            'yearForQuarterKey' => $yearForQuarterKey,
        ];
    }

    private function buildQuarterRedirectParams(Request $request, int $selectedQuarterNumber): array
    {
        $params = [];

        if ($request->filled('q')) {
            $params['q'] = $selectedQuarterNumber;
        }

        if ($request->filled('office')) {
            $params['office'] = (string) $request->input('office');
        }

        return $params;
    }
}
