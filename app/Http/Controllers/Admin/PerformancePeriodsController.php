<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Opcr;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformancePeriodsController extends Controller
{
    public function index()
    {
        $periods = PerformancePeriod::query()
            ->withCount('unitWorkPlans as uwp_count')
            ->orderByDesc('start_date')
            ->get();

        $opcrCounts = Opcr::query()
            ->selectRaw('performance_period_id, COUNT(*) as total')
            ->groupBy('performance_period_id')
            ->pluck('total', 'performance_period_id');

        $ipcrCounts = Ipcr::query()
            ->selectRaw('performance_period_id, COUNT(*) as total')
            ->groupBy('performance_period_id')
            ->pluck('total', 'performance_period_id');

        $orsCounts = OrsEntry::query()
            ->selectRaw('performance_period_id, COUNT(*) as total')
            ->groupBy('performance_period_id')
            ->pluck('total', 'performance_period_id');

        $periods->transform(function (PerformancePeriod $period) use ($opcrCounts, $ipcrCounts, $orsCounts) {
            $period->opcr_count = (int) ($opcrCounts[$period->id] ?? 0);
            $period->ipcr_count = (int) ($ipcrCounts[$period->id] ?? 0);
            $period->ors_count = (int) ($orsCounts[$period->id] ?? 0);
            $period->has_data = ((int) $period->uwp_count + (int) $period->opcr_count + (int) $period->ipcr_count + (int) $period->ors_count) > 0;

            return $period;
        });

        return view('admin.performance-period', compact('periods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $setActive = (bool) ($validated['is_active'] ?? false);

        DB::transaction(function () use ($validated, $setActive) {
            if ($setActive) {
                PerformancePeriod::query()->update(['is_active' => false]);
            }

            PerformancePeriod::query()->create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $setActive,
            ]);
        });

        return back()->with('success', 'Performance period created successfully.');
    }

    public function update(Request $request, PerformancePeriod $period): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $counts = $this->periodUsageCounts($period);
        $hasRelatedData = $this->hasRelatedData($counts);

        if ($hasRelatedData) {
            $startChanged = (string) $period->start_date?->toDateString() !== (string) date('Y-m-d', strtotime($validated['start_date']));
            $endChanged = (string) $period->end_date?->toDateString() !== (string) date('Y-m-d', strtotime($validated['end_date']));

            $period->update(['name' => $validated['name']]);

            if ($startChanged || $endChanged) {
                return back()->with('error', 'This period already has data. Dates are locked. Name was updated.');
            }

            return back()->with('success', 'Performance period name updated successfully.');
        }

        $period->update([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        return back()->with('success', 'Performance period updated successfully.');
    }

    public function activate(PerformancePeriod $period): RedirectResponse
    {
        DB::transaction(function () use ($period) {
            PerformancePeriod::query()->update(['is_active' => false]);

            $period->update(['is_active' => true]);
        });

        return back()->with('success', 'Performance period activated successfully.');
    }

    public function deactivate(PerformancePeriod $period): RedirectResponse
    {
        $period->update(['is_active' => false]);

        return back()->with('success', 'Performance period deactivated successfully.');
    }

    public function destroy(PerformancePeriod $period): RedirectResponse
    {
        $counts = $this->periodUsageCounts($period);
        if ($this->hasRelatedData($counts)) {
            return back()->with('error', 'Cannot delete. Period already in use.');
        }

        $period->delete();

        return back()->with('success', 'Performance period deleted successfully.');
    }

    private function periodUsageCounts(PerformancePeriod $period): array
    {
        return [
            'uwp' => UnitWorkPlan::query()->where('performance_period_id', $period->id)->count(),
            'opcr' => Opcr::query()->where('performance_period_id', $period->id)->count(),
            'ipcr' => Ipcr::query()->where('performance_period_id', $period->id)->count(),
            'ors' => OrsEntry::query()->where('performance_period_id', $period->id)->count(),
        ];
    }

    private function hasRelatedData(array $counts): bool
    {
        return ((int) ($counts['uwp'] ?? 0)
                + (int) ($counts['opcr'] ?? 0)
                + (int) ($counts['ipcr'] ?? 0)
                + (int) ($counts['ors'] ?? 0)) > 0;
    }
}
