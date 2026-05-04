<?php

namespace App\Http\Controllers\StageThree\Forms;

use App\Exports\StageThree\OpcrExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Opcr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class OpcrExcelExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $opcrId = (int) $request->query('opcr');
        $opcr = Opcr::with(['office.head', 'performancePeriod', 'approver'])
            ->whereKey($opcrId)
            ->firstOrFail();

        return Excel::download(
            new OpcrExcelExport($opcr),
            $this->buildFilename($opcr)
        );
    }

    protected function buildFilename(Opcr $opcr): string
    {
        $office = Str::slug((string) ($opcr->office?->name ?? 'Office'), '_');
        $period = Str::slug((string) ($opcr->performancePeriod?->name ?? 'Period'), '_');
        
        return "OPCR_EVALUATION_{$office}_{$period}.xlsx";
    }
}
