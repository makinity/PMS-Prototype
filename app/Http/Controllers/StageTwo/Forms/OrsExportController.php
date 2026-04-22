<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Http\Controllers\Controller;
use App\Models\OrsEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrsExportController extends Controller
{
    public function exportPdf(Request $request)
    {
        [$ors, $filename] = $this->buildOrs($request);

        $pdf = Pdf::loadView('pdf.stage-two.ors', compact('ors'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    public function preview(Request $request)
    {
        [$ors, $filename] = $this->buildOrs($request);

        $pdf = Pdf::loadView('pdf.stage-two.ors', compact('ors'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }

    private function buildOrs(Request $request): array
    {
        $user = $request->user();
        $userId = (int) ($user?->id ?? 0);
        $orsEntryId = (int) $request->query('ors_entry_id', 0);

        abort_if($userId <= 0, 403, 'Unauthorized.');
        abort_if($orsEntryId <= 0, 404, 'ors_entry_id is required.');

        $entry = OrsEntry::query()
            ->with([
                'employee.office',
                'ipcrItem',
                'monitoring.supervisor',
            ])
            ->whereKey($orsEntryId)
            ->where('employee_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'rated')
                    ->orWhereHas('monitoring', function ($monitoringQuery) {
                        $monitoringQuery->whereNotNull('rated_at');
                    });
            })
            ->firstOrFail();

        $filename = $this->makeFilename($entry);

        return [$this->mapEntry($entry), $filename];
    }

    private function mapEntry(OrsEntry $entry): array
    {
        $ratee = $entry->employee?->name ?? '--';
        $supervisor = $entry->monitoring?->supervisor?->name ?? '--';

        $outputTitle = $entry->ipcrItem?->output_title ?? '--';
        $indicator = $entry->ipcrItem?->indicator_text ?? '';
        $output = trim($outputTitle . ($indicator ? ' - ' . $indicator : ''));

        $dateSubmitted = $entry->submitted_at
            ? Carbon::parse($entry->submitted_at)->format('F j, Y')
            : ($entry->work_date ? Carbon::parse($entry->work_date)->format('F j, Y') : '--');

        $quantity = is_null($entry->quantity) ? '--' : (string) $entry->quantity;
        $quality = $entry->monitoring?->quality_rating ?? '--';
        $timeliness = $entry->monitoring?->timeliness_rating ?? '--';
        $remarks = $entry->monitoring?->remarks ?? '';

        return [
            'ratee' => $ratee,
            'supervisor' => $supervisor,
            'output' => $output,
            'date_submitted' => $dateSubmitted,
            'quantity' => $quantity,
            'quality' => $quality,
            'timeliness' => $timeliness,
            'remarks' => $remarks,
            'rater_signature' => '',
            'date_returned' => '',
        ];
    }

    private function makeFilename(OrsEntry $entry): string
    {
        $office = $entry->employee?->office?->name ?? 'Office';
        $officeSlug = preg_replace('/\s+/', '_', trim($office));

        $monthLabel = $entry->work_date
            ? Carbon::parse($entry->work_date)->format('M_Y')
            : now()->format('M_Y');

        return "ORS_{$officeSlug}_{$monthLabel}.pdf";
    }
}
