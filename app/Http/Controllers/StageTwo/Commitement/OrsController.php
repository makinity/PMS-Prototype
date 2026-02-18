<?php

namespace App\Http\Controllers\StageTwo\Commitement;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrsController extends Controller
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

        $committedStatus = defined(Ipcr::class . '::STATUS_COMMITTED')
            ? Ipcr::STATUS_COMMITTED
            : 'committed';

        $ipcr = null;
        if ($activePeriod) {
            $ipcr = Ipcr::query()
                ->with(['ipcrItems', 'office', 'performancePeriod', 'employee'])
                ->where('employee_id', $user->id)
                ->where('status', $committedStatus)
                ->where('performance_period_id', $activePeriod->id)
                ->orderByDesc('id')
                ->first();
        }

        $orsGate = [
            'blocked' => false,
            'reason' => null,
            'ipcr_status' => $ipcr?->status,
            'ipcr_id' => $ipcr?->id,
        ];

        if (!$activePeriod) {
            $orsGate['blocked'] = true;
            $orsGate['reason'] = 'No active performance period found.';
        } elseif (!$ipcr) {
            $orsGate['blocked'] = true;
            $orsGate['reason'] = 'IPCR not committed for active period.';
        }

        $orsOptions = [];
        if ($ipcr) {
            $grouped = [];

            foreach ($ipcr->ipcrItems as $item) {
                $outputTitle = trim((string) ($item->output_title ?? ''));
                $outputTitle = $outputTitle !== '' ? $outputTitle : 'Untitled Output';
                $groupKey = mb_strtolower($outputTitle);

                if (!isset($grouped[$groupKey])) {
                    $grouped[$groupKey] = [
                        'output_key' => 'mfo_' . substr(md5($groupKey), 0, 12),
                        'output_title' => $outputTitle,
                        'indicators' => [],
                    ];
                }

                $grouped[$groupKey]['indicators'][] = [
                    'ipcr_item_id' => $item->id,
                    'indicator_text' => (string) ($item->indicator_text ?? ''),
                    'target_summary' => (string) ($item->target_summary ?? ''),
                    'standards_payload' => is_array($item->standards_payload) ? $item->standards_payload : null,
                ];
            }

            $orsOptions = array_values($grouped);

            usort($orsOptions, function (array $a, array $b): int {
                return strcasecmp((string) ($a['output_title'] ?? ''), (string) ($b['output_title'] ?? ''));
            });

            foreach ($orsOptions as &$option) {
                usort($option['indicators'], function (array $a, array $b): int {
                    return strcasecmp((string) ($a['indicator_text'] ?? ''), (string) ($b['indicator_text'] ?? ''));
                });
            }
            unset($option);
        }

        $orsEntries = [];
        if (!$orsGate['blocked'] && Schema::hasTable('ors_entries')) {
            $orsTable = 'ors_entries';
            $hasEmployeeId = Schema::hasColumn($orsTable, 'employee_id');
            $hasUserId = Schema::hasColumn($orsTable, 'user_id');
            $hasPerformancePeriodId = Schema::hasColumn($orsTable, 'performance_period_id');
            $hasIpcrId = Schema::hasColumn($orsTable, 'ipcr_id');
            $hasIpcrItemId = Schema::hasColumn($orsTable, 'ipcr_item_id');
            $hasWorkDate = Schema::hasColumn($orsTable, 'work_date');
            $hasStatus = Schema::hasColumn($orsTable, 'status');
            $hasOutputType = Schema::hasColumn($orsTable, 'output_type');
            $hasNotes = Schema::hasColumn($orsTable, 'notes');
            $hasQuantity = Schema::hasColumn($orsTable, 'quantity');
            $hasSubmittedAt = Schema::hasColumn($orsTable, 'submitted_at');
            $hasTotalSeconds = Schema::hasColumn($orsTable, 'total_seconds');
            $hasClientRequestId = Schema::hasColumn($orsTable, 'client_request_id');
            $hasRequestId = Schema::hasColumn($orsTable, 'request_id');

            $entries = collect();

            if (class_exists(OrsEntry::class)) {
                $query = OrsEntry::query();

                if ($hasIpcrItemId && Schema::hasTable('ipcr_items')) {
                    $query->with(['ipcrItem:id,output_title,indicator_text']);
                }

                if ($hasEmployeeId) {
                    $query->where('employee_id', $user->id);
                } elseif ($hasUserId) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->whereRaw('1 = 0');
                }

                if ($activePeriod && $hasPerformancePeriodId) {
                    $query->where('performance_period_id', $activePeriod->id);
                } elseif ($ipcr && $hasIpcrId) {
                    $query->where('ipcr_id', $ipcr->id);
                }

                if ($hasWorkDate) {
                    $query->orderByDesc('work_date');
                }
                $query->orderByDesc('id');

                $entries = $query->get();
            } else {
                $query = DB::table('ors_entries as oe');

                if ($hasIpcrItemId && Schema::hasTable('ipcr_items')) {
                    $query->leftJoin('ipcr_items as ii', 'ii.id', '=', 'oe.ipcr_item_id');
                }

                if ($hasEmployeeId) {
                    $query->where('oe.employee_id', $user->id);
                } elseif ($hasUserId) {
                    $query->where('oe.user_id', $user->id);
                } else {
                    $query->whereRaw('1 = 0');
                }

                if ($activePeriod && $hasPerformancePeriodId) {
                    $query->where('oe.performance_period_id', $activePeriod->id);
                } elseif ($ipcr && $hasIpcrId) {
                    $query->where('oe.ipcr_id', $ipcr->id);
                }

                $query->select('oe.*');
                if ($hasIpcrItemId && Schema::hasTable('ipcr_items')) {
                    $query->addSelect([
                        'ii.output_title as joined_output_title',
                        'ii.indicator_text as joined_indicator_text',
                    ]);
                }

                if ($hasWorkDate) {
                    $query->orderByDesc('oe.work_date');
                }
                $query->orderByDesc('oe.id');

                $entries = $query->get();
            }

            $evidenceCounts = collect();
            if (Schema::hasTable('ors_entry_evidences') && Schema::hasColumn('ors_entry_evidences', 'ors_entry_id')) {
                $entryIds = $entries->pluck('id')->filter()->values();
                if ($entryIds->isNotEmpty()) {
                    $evidenceCounts = DB::table('ors_entry_evidences')
                        ->whereIn('ors_entry_id', $entryIds)
                        ->select('ors_entry_id', DB::raw('COUNT(*) as aggregate'))
                        ->groupBy('ors_entry_id')
                        ->pluck('aggregate', 'ors_entry_id');
                }
            }

            $orsEntries = $entries->map(function ($entry) use (
                $hasWorkDate,
                $hasStatus,
                $hasRequestId,
                $hasClientRequestId,
                $hasOutputType,
                $hasNotes,
                $hasQuantity,
                $hasSubmittedAt,
                $hasTotalSeconds,
                $evidenceCounts
            ) {
                $workDateRaw = $hasWorkDate ? data_get($entry, 'work_date') : null;
                $workDate = null;
                if ($workDateRaw instanceof \DateTimeInterface) {
                    $workDate = $workDateRaw->format('Y-m-d');
                } elseif (!is_null($workDateRaw) && (string) $workDateRaw !== '') {
                    $workDate = substr((string) $workDateRaw, 0, 10);
                }

                $indicatorText = (string) (
                    data_get($entry, 'ipcrItem.indicator_text')
                    ?? data_get($entry, 'joined_indicator_text')
                    ?? ''
                );

                $outputTitle = (string) (
                    data_get($entry, 'ipcrItem.output_title')
                    ?? data_get($entry, 'joined_output_title')
                    ?? ''
                );

                $entryId = (int) (data_get($entry, 'id') ?? 0);
                $requestId = $hasRequestId
                    ? data_get($entry, 'request_id')
                    : ($hasClientRequestId ? data_get($entry, 'client_request_id') : null);

                return [
                    'id' => $entryId,
                    'title' => $indicatorText !== '' ? $indicatorText : 'Untitled Activity',
                    'date' => $workDate,
                    'state' => $hasStatus ? (string) (data_get($entry, 'status') ?? 'draft') : 'draft',
                    'uwpOutputLabel' => $outputTitle !== '' ? $outputTitle : 'Untitled Output',
                    'requestId' => $requestId,
                    'output' => $hasOutputType ? data_get($entry, 'output_type') : null,
                    'notes' => $hasNotes ? data_get($entry, 'notes') : null,
                    'quantity' => $hasQuantity ? data_get($entry, 'quantity') : null,
                    'submittedAt' => $hasSubmittedAt ? data_get($entry, 'submitted_at') : null,
                    'durationSeconds' => $hasTotalSeconds ? (int) (data_get($entry, 'total_seconds') ?? 0) : 0,
                    'evidenceAttached' => (int) ($evidenceCounts[$entryId] ?? 0) > 0,
                ];
            })->values()->all();
        }

        $officeName = $ipcr?->office?->name ?? '';
        $periodName = $ipcr?->performancePeriod?->name ?? ($activePeriod?->name ?? '');

        return view('employee.ors', [
            'activePeriod' => $activePeriod,
            'ipcr' => $ipcr,
            'orsGate' => $orsGate,
            'orsOptions' => $orsOptions,
            'orsEntries' => $orsEntries,
            'employeeName' => $user->name ?? '',
            'officeName' => $officeName,
            'periodName' => $periodName,
        ]);
    }
}
