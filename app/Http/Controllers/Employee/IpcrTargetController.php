<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ipcr;
use App\Models\PerformancePeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IpcrTargetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            abort(403, 'Unauthorized.');
        }

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $ipcrQuery = Ipcr::query()
            ->with([
                'ipcrItems.uwpFunction',
                'unitWorkPlan.uwpFunctions.mfos',
                'office',
                'performancePeriod',
                'employee',
            ])
            ->where('employee_id', $user->id)
            ->orderByDesc('id');

        if ($activePeriod) {
            $ipcrQuery->where('performance_period_id', $activePeriod->id);
        }

        $ipcr = $ipcrQuery->first();

        $payload = [
            'status' => $ipcr?->status,
            'core' => [],
            'support' => [],
        ];

        $functionHeaderMeta = [
            'core_percent' => 80,
            'support_percent' => 20,
        ];

        $functionTypeLabels = [
            'core' => 'Core Functions',
            'support' => 'Support Functions',
        ];

        if ($ipcr) {
            $mfoTargetSummaryMap = [];
            foreach ($ipcr->unitWorkPlan?->uwpFunctions ?? [] as $function) {
                foreach ($function->mfos ?? [] as $mfo) {
                    $mapKey = (int) $function->id . '||' . trim((string) ($mfo->title ?? ''));
                    $mfoTargetSummaryMap[$mapKey] = $this->buildTargetSummary(
                        $mfo->target_quantity,
                        $mfo->target_timeline
                    );
                }
            }

            $functions = $ipcr->ipcrItems
                ->pluck('uwpFunction')
                ->filter()
                ->unique('id')
                ->values();

            if ($functions->isNotEmpty()) {
                $functionHeaderMeta = [
                    'core_percent' => (float) $functions->sum(function ($function) {
                        return strtolower(trim((string) ($function->function_type ?? ''))) === 'support'
                            ? 0
                            : (float) ($function->weight_percent ?? 0);
                    }),
                    'support_percent' => (float) $functions->sum(function ($function) {
                        return strtolower(trim((string) ($function->function_type ?? ''))) === 'support'
                            ? (float) ($function->weight_percent ?? 0)
                            : 0;
                    }),
                ];
            }

            $groups = [];
            $items = $ipcr->ipcrItems
                ->sortBy(function ($item) {
                    $sortOrder = $item->uwpFunction?->sort_order;
                    $resolvedSort = is_numeric($sortOrder) ? (int) $sortOrder : 9999;

                    return sprintf('%05d-%010d', $resolvedSort, (int) $item->id);
                })
                ->values();

            foreach ($items as $item) {
                $groupFunctionType = strtolower(trim((string) ($item->uwpFunction?->function_type ?? $item->function_type)));
                $groupType = $groupFunctionType === 'support' ? 'support' : 'core';
                $outputTitle = (string) ($item->output_title ?? '');
                $groupKey = $groupType . '||' . $outputTitle;

                if (!isset($groups[$groupKey])) {
                    $groups[$groupKey] = [
                        'type' => $groupType,
                        'title' => $outputTitle,
                        'items' => [],
                    ];
                }

                $groups[$groupKey]['items'][] = $item;
            }

            $coreIndex = 0;
            $supportIndex = 0;

            foreach ($groups as $group) {
                $functionType = $group['type'];
                $outputTitle = $group['title'];
                $groupItems = collect($group['items']);
                $firstGroupItem = $groupItems->first();
                $targetMapKey = (int) ($firstGroupItem?->uwp_function_id ?? 0) . '||' . trim($outputTitle);

                $storedTargetSummary = (string) (
                    $groupItems->first(function ($row) {
                        return trim((string) ($row->target_summary ?? '')) !== '';
                    })?->target_summary ?? ''
                );

                $targetSummary = $mfoTargetSummaryMap[$targetMapKey] ?? '';
                if ($targetSummary === '') {
                    $targetSummary = trim($storedTargetSummary);
                }
                if ($targetSummary === '') {
                    $targetSummary = '—';
                }

                $indicators = $groupItems->map(function ($row) {
                    $rawStandards = $row->standards_payload;
                    $standards = is_array($rawStandards) ? $rawStandards : [];

                    if (is_string($rawStandards)) {
                        $decoded = json_decode($rawStandards, true);
                        if (is_array($decoded)) {
                            $standards = $decoded;
                        }
                    }

                    $normalized = [];
                    foreach ([5, 4, 3, 2, 1] as $rating) {
                        $key = (string) $rating;
                        $bucket = $standards[$key] ?? $standards[$rating] ?? [];

                        if (is_string($bucket)) {
                            $bucket = ['Q' => [$bucket], 'E' => [], 'T' => []];
                        }
                        if (!is_array($bucket)) {
                            $bucket = [];
                        }

                        $q = $bucket['Q'] ?? $bucket['q'] ?? [];
                        $e = $bucket['E'] ?? $bucket['e'] ?? [];
                        $t = $bucket['T'] ?? $bucket['t'] ?? [];

                        $normalized[$key] = [
                            'Q' => is_array($q) ? array_values($q) : (strlen((string) $q) ? [(string) $q] : []),
                            'E' => is_array($e) ? array_values($e) : (strlen((string) $e) ? [(string) $e] : []),
                            'T' => is_array($t) ? array_values($t) : (strlen((string) $t) ? [(string) $t] : []),
                        ];
                    }

                    return [
                        'indicator_text' => (string) $row->indicator_text,
                        'standards_by_rating' => $normalized,
                    ];
                })->values()->all();

                $entry = [
                    'key' => $functionType === 'support' ? ('support_' . $supportIndex++) : ('core_' . $coreIndex++),
                    'output_title' => $outputTitle,
                    'target_summary' => $targetSummary,
                    'timeline' => (string) ($ipcr->performancePeriod?->name ?? ''),
                    'weight_percent' => (float) ($groupItems->first()?->uwpFunction?->weight_percent ?? 0),
                    'indicators' => $indicators,
                ];

                if ($functionType === 'support') {
                    $payload['support'][] = $entry;
                } else {
                    $payload['core'][] = $entry;
                }
            }
        }

        return view('employee.ipcr-target', [
            'activePeriod' => $activePeriod,
            'ipcr' => $ipcr,
            'ipcrPayload' => $payload,
            'functionHeaderMeta' => $functionHeaderMeta,
            'functionTypeLabels' => $functionTypeLabels,
            'employeeName' => $user->name,
            'officeName' => (string) ($ipcr?->office?->name ?? ''),
            'periodName' => (string) ($ipcr?->performancePeriod?->name ?? ($activePeriod?->name ?? '')),
            'supervisorName' => '',
            'employeePosition' => (string) ($ipcr?->employee?->position ?? ''),
        ]);
    }

    private function buildTargetSummary($targetQuantity, ?string $targetTimeline): string
    {
        $quantityText = $targetQuantity === null ? '' : trim((string) $targetQuantity);
        $timelineText = trim((string) ($targetTimeline ?? ''));

        return trim($quantityText . ' ' . $timelineText);
    }

    public function commit(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            abort(403, 'Unauthorized.');
        }

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $ipcrQuery = Ipcr::query()
            ->withCount('ipcrItems')
            ->where('employee_id', $user->id)
            ->orderByDesc('id');

        if ($activePeriod) {
            $ipcrQuery->where('performance_period_id', $activePeriod->id);
        }

        $ipcr = $ipcrQuery->first();
        if (!$ipcr) {
            return back()->with('error', 'No generated IPCR found for this period.');
        }

        // Already committed => treat as success (idempotent)
        if (strtolower((string) $ipcr->status) === Ipcr::STATUS_COMMITTED) {
            return back()->with('success', 'IPCR already committed.');
        }

        if (strtolower((string) $ipcr->status) !== Ipcr::STATUS_FOR_COMMITMENT) {
            return back()->with('error', 'IPCR is not in a commit-ready state.');
        }

        if ((int) $ipcr->ipcr_items_count <= 0) {
            return back()->with('error', 'Cannot commit: IPCR has no items.');
        }

        DB::transaction(function () use ($ipcr) {
            $now = now();
            $ipcr->status = Ipcr::STATUS_COMMITTED;
            $ipcr->committed_at = $now;
            $ipcr->locked_at = $now; // freeze targets for ORS reference
            $ipcr->save();
        });

        return back()->with('success', 'IPCR committed. ORS is now available.');
    }
}
