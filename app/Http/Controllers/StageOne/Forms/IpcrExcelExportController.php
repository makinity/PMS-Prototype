<?php

namespace App\Http\Controllers\StageOne\Forms;

use App\Exports\StageOne\IpcrExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Services\IpcrGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class IpcrExcelExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $ipcr = $this->resolveIpcrForExport($request);

        return Excel::download(
            new IpcrExcelExport($ipcr),
            $this->buildFilename($ipcr, false)
        );
    }

    public function previewExcel(Request $request)
    {
        $ipcr = $this->resolveIpcrForExport($request);

        return Excel::download(
            new IpcrExcelExport($ipcr),
            $this->buildFilename($ipcr, true)
        );
    }

    protected function resolveIpcrForExport(Request $request): Ipcr
    {
        $employee = $request->user();
        abort_unless($employee, 403, 'Unauthorized.');

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        abort_unless($activePeriod, 404, 'No active performance period found.');

        $ipcr = $this->buildEmployeeIpcrQuery((int) $employee->id, (int) $activePeriod->id)
            ->first();

        if (!$ipcr) {
            $opcr = Opcr::query()
                ->where('office_id', $employee->office_id)
                ->where('performance_period_id', $activePeriod->id)
                ->where('status', Opcr::STATUS_APPROVED)
                ->latest('id')
                ->first();

            if ($opcr) {
                app(IpcrGeneratorService::class)->generateFromOpcr($opcr);

                $ipcr = $this->buildEmployeeIpcrQuery((int) $employee->id, (int) $activePeriod->id)
                    ->first();
            }
        }

        abort_unless($ipcr, 404, 'No IPCR data available for the active performance period.');

        return $ipcr;
    }

    protected function buildEmployeeIpcrQuery(int $employeeId, int $periodId)
    {
        return Ipcr::query()
            ->where('employee_id', $employeeId)
            ->where('performance_period_id', $periodId)
            ->with([
                'employee:id,name,office_id',
                'office:id,name,head_id',
                'office.head:id,name',
                'performancePeriod:id,name,start_date,end_date',
                'opcr:id,unit_work_plan_id',
                'opcr.unitWorkPlan:id,created_by',
                'opcr.unitWorkPlan.creator:id,name',
                'items' => function ($query) {
                    $query->orderByRaw(
                        "CASE
                            WHEN LOWER(COALESCE(function_type, '')) = 'core' THEN 0
                            WHEN LOWER(COALESCE(function_type, '')) = 'support' THEN 1
                            ELSE 2
                        END"
                    )
                    ->orderBy('output_title')
                    ->orderBy('id');
                },
            ])
            ->orderByDesc('committed_at')
            ->orderByDesc('generated_at')
            ->orderByDesc('id');
    }

    protected function buildFilename(Ipcr $ipcr, bool $preview): string
    {
        $employee = Str::slug((string) ($ipcr->employee?->name ?? 'Employee'), '_');
        $office = Str::slug((string) ($ipcr->office?->name ?? 'Office'), '_');
        $period = Str::slug((string) ($ipcr->performancePeriod?->name ?? 'Period'), '_');
        $suffix = $preview ? '_Preview' : '';

        return "IPCR_{$employee}_{$office}_{$period}{$suffix}.xlsx";
    }
}
