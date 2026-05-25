<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Models\PerformancePeriod;
use App\Models\QarHeader;
use App\Notifications\WorkflowEventNotification;
use App\Services\SmporGeneratorService;
use App\Services\WorkflowNotificationDispatcher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QarController extends Controller
{
    public function show(Request $request, QarHeader $qarHeader)
    {
        $qarHeader->load([
            'office:id,name',
            'performancePeriod:id,name,start_date,end_date',
            'approver:id,name',
            'pmtValidator:id,name',
            'mporLinks',
            'rows',
        ]);

        if (!in_array((string) $qarHeader->status, [QarHeader::STATUS_DEPT_HEAD_ENDORSED, QarHeader::STATUS_PMT_APPROVED], true)) {
            return redirect()->route('pmt.qar', $this->buildRedirectParams($request))->with('info', 'QAR is not viewable yet.');
        }

        $quarterInputValue = (int) $request->query('q', 0);
        $officeSearchSafe = trim((string) $request->query('office', ''));

        return view('pmt.qar-show', [
            'header' => $qarHeader,
            'quarterInputValue' => $quarterInputValue,
            'officeSearchSafe' => $officeSearchSafe,
        ]);
    }

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

            // Finalize Office OPCR
            \App\Models\Opcr::query()
                ->where('office_id', $qarHeader->office_id)
                ->where('performance_period_id', $qarHeader->performance_period_id)
                ->whereIn('status', [
                    \App\Models\Opcr::STATUS_PENDING_PMT_CALIBRATION,
                    \App\Models\Opcr::STATUS_ADJUSTED_BY_PMT
                ])
                ->get()
                ->each(function ($opcr) {
                    $finalStatus = ($opcr->status === \App\Models\Opcr::STATUS_ADJUSTED_BY_PMT)
                        ? \App\Models\Opcr::STATUS_ADJUSTED_BY_PMT
                        : \App\Models\Opcr::STATUS_APPROVED_BY_PMT;

                    $opcr->update([
                        'status' => $finalStatus,
                        'locked_at' => now(),
                    ]);
                });

            // AUTOMATIC IPCR CALCULATION FOR ALL EMPLOYEES
            $ipcrs = \App\Models\Ipcr::query()
                ->where('office_id', $qarHeader->office_id)
                ->where('performance_period_id', $qarHeader->performance_period_id)
                ->where('status', \App\Models\Ipcr::STATUS_PENDING_PMT_CALIBRATION)
                ->get();

            $ratingService = app(\App\Services\PerformanceRatingService::class);
            foreach ($ipcrs as $ipcr) {
                $ratingService->calculateAndSaveFinalScore($ipcr);
            }

            // Calculate OPCR final_score from employee IPCR averages
            $opcrs = \App\Models\Opcr::query()
                ->where('office_id', $qarHeader->office_id)
                ->where('performance_period_id', $qarHeader->performance_period_id)
                ->whereIn('status', [
                    \App\Models\Opcr::STATUS_APPROVED_BY_PMT,
                    \App\Models\Opcr::STATUS_ADJUSTED_BY_PMT,
                ])
                ->get();

            foreach ($opcrs as $opcr) {
                $ipcrScores = \App\Models\Ipcr::query()
                    ->where('office_id', $qarHeader->office_id)
                    ->where('performance_period_id', $qarHeader->performance_period_id)
                    ->whereNotNull('final_score')
                    ->where('final_score', '>', 0)
                    ->pluck('final_score');

                if ($ipcrScores->isNotEmpty()) {
                    $avgScore = round($ipcrScores->avg(), 2);
                    $opcr->update([
                        'final_score' => $avgScore,
                        'adjectival_rating' => $ratingService->resolveAdjectivalRating($avgScore),
                    ]);
                }
            }

            app(SmporGeneratorService::class)->generateFromApprovedQar($qarHeader, $request->user()?->id);
        });

        // Notify Dept Head that QAR was approved
        $qarHeader->loadMissing('office.head');
        $deptHead = $qarHeader->office?->head;
        if ($deptHead) {
            $user = $request->user();
            app(WorkflowNotificationDispatcher::class)->notifyUser(
                $deptHead,
                new WorkflowEventNotification(
                    title: 'QAR Approved by PMT',
                    body: ($user->name ?? 'PMT') . " approved your QAR.",
                    url: route('dept-head.qar'),
                    type: 'success',
                    meta: [
                        'event' => 'qar.pmt_approved',
                        'qar_header_id' => $qarHeader->id,
                        'office_id' => $qarHeader->office_id,
                        'performance_period_id' => $qarHeader->performance_period_id,
                        'source_role' => 'pmt',
                    ],
                )
            );
        }

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
            $qarHeader->status = QarHeader::STATUS_RETURNED;
            $qarHeader->pmt_status = QarHeader::PMT_RETURNED;
            $qarHeader->pmt_validated_at = now();
            $qarHeader->pmt_validated_by = $request->user()?->id;
            $qarHeader->save();

            // Restore IPCRs back to committed
            \App\Models\Ipcr::query()
                ->where('office_id', $qarHeader->office_id)
                ->where('performance_period_id', $qarHeader->performance_period_id)
                ->where('status', \App\Models\Ipcr::STATUS_PENDING_PMT_CALIBRATION)
                ->update(['status' => \App\Models\Ipcr::STATUS_COMMITTED]);

            // Restore OPCRs back to approved
            \App\Models\Opcr::query()
                ->where('office_id', $qarHeader->office_id)
                ->where('performance_period_id', $qarHeader->performance_period_id)
                ->where('status', \App\Models\Opcr::STATUS_PENDING_PMT_CALIBRATION)
                ->update(['status' => \App\Models\Opcr::STATUS_APPROVED]);

            $mporIds = $qarHeader->mporLinks()
                ->pluck('mpor_id')
                ->filter()
                ->unique()
                ->values();

            if ($mporIds->isNotEmpty()) {
                DB::table('mpors')
                    ->whereIn('id', $mporIds->all())
                    ->update([
                        'status' => 'returned',
                        'updated_at' => now(),
                    ]);
            }
        });

        // Notify Dept Head that QAR was returned
        $qarHeader->loadMissing('office.head');
        $deptHead = $qarHeader->office?->head;
        if ($deptHead) {
            $user = $request->user();
            app(WorkflowNotificationDispatcher::class)->notifyUser(
                $deptHead,
                new WorkflowEventNotification(
                    title: 'QAR Returned by PMT',
                    body: ($user->name ?? 'PMT') . " returned your QAR for revision.",
                    url: route('dept-head.qar'),
                    type: 'alert',
                    meta: [
                        'event' => 'qar.pmt_returned',
                        'qar_header_id' => $qarHeader->id,
                        'office_id' => $qarHeader->office_id,
                        'performance_period_id' => $qarHeader->performance_period_id,
                        'source_role' => 'pmt',
                    ],
                )
            );
        }

        return redirect()
            ->route('pmt.qar', $redirectParams)
            ->with('success', 'QAR returned to Dept Head. Linked MPORs were also returned to employees.');
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

    public function previewPdf(Request $request, QarHeader $qarHeader)
    {
        $qarHeader->load([
            'office:id,name',
            'performancePeriod:id,name,start_date,end_date',
            'rows:id,qar_header_id,ppa_code,mfo_title,indicator_text,target_quantity,target_timeline,actual_performance,variance,remarks,sort_order',
        ]);

        $officeName = $qarHeader->office?->name ?? 'Office';
        $period = $qarHeader->performancePeriod;

        $periodName = $period?->name ?? 'Performance Period';

        $periodRange = '-';
        if ($period?->start_date && $period?->end_date) {
            $periodRange =
                Carbon::parse($period->start_date)->format('M d, Y')
                . ' - ' .
                Carbon::parse($period->end_date)->format('M d, Y');
        }

        $quarterEndingLabel = now()->format('F d, Y');
        if (preg_match('/^(\d{4})-Q(\d)$/', (string) $qarHeader->quarter_key, $matches)) {
            $year = (int) $matches[1];
            $quarter = (int) $matches[2];

            if ($quarter === 1) {
                $quarterEndingLabel = Carbon::create($year, 3, 31)->format('F d, Y');
            } elseif ($quarter === 2) {
                $quarterEndingLabel = Carbon::create($year, 6, 30)->format('F d, Y');
            }
        }

        $pdf = Pdf::loadView('pdf.stage-two.qar', [
            'qarHeader' => $qarHeader,
            'officeName' => $officeName,
            'periodName' => $periodName,
            'periodRange' => $periodRange,
            'quarterEndingLabel' => $quarterEndingLabel,
        ])->setPaper('legal', 'landscape');

        return $pdf->stream('QAR-' . ($qarHeader->quarter_key ?? 'report') . '.pdf');
    }
}
