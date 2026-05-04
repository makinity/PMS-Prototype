<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Models\PerformancePeriod;
use App\Services\StageFourPerformerService;
use Illuminate\Http\Request;

class TopPerformersController extends Controller
{
    public function index(Request $request, StageFourPerformerService $performerService)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pmt', 403);

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $data = $performerService->getTopAndLowPerformers($activePeriod);

        $hasReleasedRecords = collect([
            $data['top_employees']->count(),
            $data['top_offices']->count(),
            $data['low_employees']->count(),
            $data['low_offices']->count(),
        ])->sum() > 0;

        $infoMessage = null;
        if (!$activePeriod) {
            $infoMessage = 'No active performance period is configured.';
        } elseif (!$hasReleasedRecords) {
            $infoMessage = 'No officially released Stage III IPCR or OPCR results found for the active period.';
        }

        return view('pmt.top-performers.index', [
            'activePeriod' => $activePeriod,
            'topEmployees' => $data['top_employees'],
            'topOffices' => $data['top_offices'],
            'lowEmployees' => $data['low_employees'],
            'lowOffices' => $data['low_offices'],
            'summaryCounts' => $data['summary_counts'],
            'infoMessage' => $infoMessage,
        ]);
    }
}
