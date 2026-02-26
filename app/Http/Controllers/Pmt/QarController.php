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

        $quarterContext = $this->resolveQuarterContext($request);
        $quarterNumber = $quarterContext['quarterNumber'];
        $quarterKey = $quarterContext['quarterKey'];
        $quarterLabel = $quarterContext['quarterLabel'];
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
                    'mporLinks',
                    'rows',
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
        $redirectParams = $this->buildRedirectParams($request);

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
        $redirectParams = $this->buildRedirectParams($request);

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

        DB::transaction(function () use ($request, $qarHeader): void {
            $qarHeader->pmt_status = QarHeader::PMT_RETURNED;
            $qarHeader->pmt_validated_at = now();
            $qarHeader->pmt_validated_by = $request->user()?->id;
            $qarHeader->save();
        });

        return redirect()
            ->route('pmt.qar', $redirectParams)
            ->with('success', 'QAR returned to Dept Head.');
    }

    private function resolveQuarterContext(Request $request): array
    {
        $selectedQuarterNumber = (int) $request->query('q', 1);
        if (!in_array($selectedQuarterNumber, [1, 2], true)) {
            $selectedQuarterNumber = 1;
        }

        $year = (int) Carbon::now()->year;
        $quarterKey = sprintf('%d-Q%d', $year, $selectedQuarterNumber);
        $quarterLabel = sprintf('Q%d %d', $selectedQuarterNumber, $year);
        $allowedQuarterOptions = [
            ['value' => 1, 'label' => 'Q1'],
            ['value' => 2, 'label' => 'Q2'],
        ];

        return [
            'quarterNumber' => $selectedQuarterNumber,
            'quarterKey' => $quarterKey,
            'quarterLabel' => $quarterLabel,
            'allowedQuarterOptions' => $allowedQuarterOptions,
            'selectedQuarterNumber' => $selectedQuarterNumber,
        ];
    }

    private function buildRedirectParams(Request $request): array
    {
        $params = [];

        if ($request->filled('q')) {
            $params['q'] = (int) $request->input('q');
        }

        $office = trim((string) $request->input('office', ''));
        if ($office !== '') {
            $params['office'] = $office;
        }

        return $params;
    }
}
