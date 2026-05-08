<?php

namespace App\Http\Controllers\StageOne\Forms;

use App\Exports\StageOne\OpcrExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Opcr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class OpcrExcelExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $opcr = $this->resolveOpcrForExport($request);

        // Check if there's a signed artifact for this OPCR
        $latestSignature = \App\Models\UwpConsolidationSignature::query()
            ->where('opcr_id', $opcr->id)
            ->orderByDesc('signed_at')
            ->first();

        if ($latestSignature && $latestSignature->signed_excel_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($latestSignature->signed_excel_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->download(
                $latestSignature->signed_excel_path,
                $this->buildFilename($opcr, false, true)
            );
        }

        return Excel::download(
            new OpcrExcelExport($opcr),
            $this->buildFilename($opcr, false)
        );
    }

    public function previewExcel(Request $request)
    {
        $opcr = $this->resolveOpcrForExport($request);

        return Excel::download(
            new OpcrExcelExport($opcr),
            $this->buildFilename($opcr, true)
        );
    }

    protected function resolveOpcrForExport(Request $request): Opcr
    {
        $opcrId = (int) $request->query('opcr');
        $query = Opcr::query()
            ->with([
                'office.head',
                'performancePeriod',
                'unitWorkPlan.office.head',
                'unitWorkPlan.performancePeriod',
                'unitWorkPlan.creator',
                'unitWorkPlan.uwpFunctions' => function ($query) {
                    $query->orderBy('sort_order')->with([
                        'mfos' => function ($mfoQuery) {
                            $mfoQuery->orderBy('sort_order')->with([
                                'successIndicators' => function ($indicatorQuery) {
                                    $indicatorQuery->orderBy('sort_order')->with([
                                        'qetStandards',
                                        'assignments.employee',
                                    ]);
                                },
                            ]);
                        },
                    ]);
                },
                'unitWorkPlans.office.head',
                'unitWorkPlans.performancePeriod',
                'unitWorkPlans.creator',
                'unitWorkPlans.uwpFunctions' => function ($query) {
                    $query->orderBy('sort_order')->with([
                        'mfos' => function ($mfoQuery) {
                            $mfoQuery->orderBy('sort_order')->with([
                                'successIndicators' => function ($indicatorQuery) {
                                    $indicatorQuery->orderBy('sort_order')->with([
                                        'qetStandards',
                                        'assignments.employee',
                                    ]);
                                },
                            ]);
                        },
                    ]);
                },
            ]);

        if ($opcrId > 0) {
            return $query->whereKey($opcrId)->firstOrFail();
        }

        return $query->latest('id')->firstOrFail();
    }

    protected function buildFilename(Opcr $opcr, bool $preview, bool $signed = false): string
    {
        $source = $opcr->sourceUnitWorkPlans()->first();
        $office = Str::slug((string) ($opcr->office?->name ?? $source?->office?->name ?? 'Office'), '_');
        $period = Str::slug((string) ($opcr->performancePeriod?->name ?? $source?->performancePeriod?->name ?? 'Period'), '_');
        $suffix = $preview ? '_Preview' : '';
        if ($signed) {
            $suffix .= '_SIGNED';
        }

        return "OPCR_{$office}_{$period}{$suffix}.xlsx";
    }
}
