<?php

namespace App\Http\Controllers\StageTwo\Commitement;

use App\Http\Controllers\Controller;
use App\Models\OrsEntry;
use App\Models\OrsEntryEvidence;
use App\Models\PerformancePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class MyTasksController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->authorizedEmployee();

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $orsEntriesQuery = OrsEntry::query()
            ->with([
                'ipcrItem:id,output_title,indicator_text',
                'evidences:id,ors_entry_id,file_name,file_path,mime_type,file_size,uploaded_at',
            ])
            ->where('employee_id', $user->id)
            ->orderByDesc('work_date')
            ->orderByDesc('id');

        if ($activePeriod) {
            $orsEntriesQuery->where('performance_period_id', $activePeriod->id);
        }

        $orsEntries = $orsEntriesQuery->get();

        return view('employee.my-task', [
            'orsEntries' => $orsEntries,
            'activePeriod' => $activePeriod,
            'employeeName' => $user->name,
        ]);
    }

    public function evidences(OrsEntry $orsEntry)
    {
        $this->authorizeEntryOwnership($orsEntry);

        $evidences = OrsEntryEvidence::query()
            ->where('ors_entry_id', $orsEntry->id)
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->get();

        $payload = $evidences->map(function (OrsEntryEvidence $evidence) use ($orsEntry) {
            return [
                'id' => $evidence->id,
                'file_name' => $evidence->file_name,
                'mime_type' => $evidence->mime_type,
                'file_size' => (int) ($evidence->file_size ?? 0),
                'uploaded_at' => $evidence->uploaded_at?->toISOString(),
                'view_url' => route('stage2.my_tasks.evidences.view', [
                    'orsEntry' => $orsEntry->id,
                    'evidence' => $evidence->id,
                ]),
                'download_url' => route('stage2.my_tasks.evidences.download', [
                    'orsEntry' => $orsEntry->id,
                    'evidence' => $evidence->id,
                ]),
            ];
        })->values();

        return response()->json($payload);
    }

    public function viewEvidence(OrsEntry $orsEntry, OrsEntryEvidence $evidence)
    {
        $this->authorizeEntryOwnership($orsEntry);
        $this->assertEvidenceBelongsToEntry($orsEntry, $evidence);

        $disk = Storage::disk('public');
        $filePath = (string) $evidence->file_path;

        if ($filePath === '' || !$disk->exists($filePath)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $response = response()->file($disk->path($filePath), [
            'Content-Type' => $evidence->mime_type ?: 'application/octet-stream',
        ]);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");

        return $response;
    }

    public function downloadEvidence(OrsEntry $orsEntry, OrsEntryEvidence $evidence)
    {
        $this->authorizeEntryOwnership($orsEntry);
        $this->assertEvidenceBelongsToEntry($orsEntry, $evidence);

        $disk = Storage::disk('public');
        $filePath = (string) $evidence->file_path;

        if ($filePath === '' || !$disk->exists($filePath)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $downloadName = trim((string) $evidence->file_name) !== ''
            ? (string) $evidence->file_name
            : Str::of($filePath)->afterLast('/')->toString();

        return $disk->download(
            $filePath,
            $downloadName,
            ['Content-Type' => $evidence->mime_type ?: 'application/octet-stream']
        );
    }

    private function authorizedEmployee()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'employee') {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized.');
        }

        return $user;
    }

    private function authorizeEntryOwnership(OrsEntry $orsEntry): void
    {
        $user = $this->authorizedEmployee();

        if ((int) $orsEntry->employee_id !== (int) $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized.');
        }
    }

    private function assertEvidenceBelongsToEntry(OrsEntry $orsEntry, OrsEntryEvidence $evidence): void
    {
        if ((int) $evidence->ors_entry_id !== (int) $orsEntry->id) {
            abort(Response::HTTP_NOT_FOUND);
        }
    }
}
