<?php

namespace App\Http\Controllers\StageOne\Forms;

use App\Exports\StageOne\UwpExcelExport;
use App\Http\Controllers\Controller;
use App\Models\UnitWorkPlan;
use App\Services\UwpExcelPayloadService;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UwpExcelExportController extends Controller
{
    public function excelExport(int $uwp, UwpExcelPayloadService $payloadService)
    {
        $uwpModel = UnitWorkPlan::query()
            ->with([
                'office.head',
                'performancePeriod',
                'creator',
                'uwpFunctions.mfos.successIndicators.qetStandards',
            ])
            ->where('id', $uwp)
            ->firstOrFail();

        if ($uwpModel->uwpFunctions->isEmpty()) {
            return back()->with('error', 'Cannot export an empty UWP. Please add some functions and outputs first.');
        }

        $payload = $payloadService->build($uwpModel);

        // Check if there's a signed artifact for this UWP
        $latestSignature = \App\Models\UwpConsolidationSignature::query()
            ->where('unit_work_plan_id', $uwp)
            ->orderByDesc('signed_at')
            ->first();

        if ($latestSignature && $latestSignature->signed_excel_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($latestSignature->signed_excel_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->download(
                $latestSignature->signed_excel_path,
                'UWP_' . Str::slug($uwpModel->office?->name ?? 'Office') . '_' .
                Str::slug($uwpModel->performancePeriod?->name ?? 'Period') . '_SIGNED.xlsx'
            );
        }

        return Excel::download(
            new UwpExcelExport($payload['uwp'], $payload['standards']),
            'UWP_' . Str::slug($uwpModel->office?->name ?? 'Office') . '_' .
            Str::slug($uwpModel->performancePeriod?->name ?? 'Period') . '.xlsx'
        );
    }
}
