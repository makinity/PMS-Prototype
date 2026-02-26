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
        $period = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();

        $now = Carbon::now();
        $year = (int) $now->year;
        $quarterNumber = (int) ceil($now->month / 3);
        $quarterKey = sprintf('%d-Q%d', $year, $quarterNumber);
        $quarterLabel = sprintf('Q%d %d', $quarterNumber, $year);

        $headers = collect();

        if ($period) {
            $headers = QarHeader::query()
                ->with([
                    'office:id,name',
                    'performancePeriod:id,name,start_date,end_date',
                    'approver:id,name',
                    'pmtValidator:id,name',
                ])
                ->where('performance_period_id', $period->id)
                ->where('quarter_key', $quarterKey)
                ->whereIn('status', [QarHeader::STATUS_DEPT_HEAD_ENDORSED, QarHeader::STATUS_PMT_APPROVED])
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
            'quarterKey',
            'quarterLabel',
            'headers',
            'endorsedCount',
            'approvedCount'
        ));
    }

    public function approve(Request $request, QarHeader $qarHeader)
    {
        if ($qarHeader->status === QarHeader::STATUS_PMT_APPROVED) {
            return redirect()
                ->route('pmt.qar')
                ->with('info', 'QAR already approved.');
        }

        if ($qarHeader->status !== QarHeader::STATUS_DEPT_HEAD_ENDORSED) {
            return redirect()
                ->route('pmt.qar')
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
            ->route('pmt.qar')
            ->with('success', 'QAR approved. Employees may now proceed to IPCR/SMPOR accomplishments.');

    }
}
