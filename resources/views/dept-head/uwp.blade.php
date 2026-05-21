@extends('layouts.dept-head')
{{-- Page Header --}}
@section('main-content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-100">Unit Work Plan Review</h1>
    <p class="text-sm text-slate-400 mt-1">
        Select submitted Unit Work Plans. Consolidate them into OPCR or return with remarks.
    </p>
</div>

{{-- Filters / Meta (Optional but useful) --}}
<div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-400">Performance Period</p>
        <p class="font-medium text-slate-100">{{ $activePeriod->name ?? '—' }}</p>
    </div>

    <div class="flex items-center gap-3">
        <input
            type="text"
            placeholder="Search office/unit..."
            class="w-full md:w-72
                bg-slate-900 text-slate-100 placeholder-slate-300
                border border-slate-700
                rounded-lg px-3 py-2 text-sm
                focus:bg-slate-900 focus:border-blue-500
                focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
            style="background:#0f172a;color:#e5e7eb;"
        />
        @php
            $statusFilter = strtolower((string) ($selectedStatus ?? request('status', '')));
        @endphp
        <form method="GET" action="{{ route('dept-head.uwp.index') }}">
            <select
                name="status"
                onchange="this.form.submit()"
                style="background:#0f172a;color:#e5e7eb;"
                class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-2
                text-sm text-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="" {{ $statusFilter === '' ? 'selected' : '' }}>All Status</option>
                <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="consolidated" {{ $statusFilter === 'consolidated' ? 'selected' : '' }}>Consolidated</option>
                <option value="returned" {{ $statusFilter === 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
        </form>
    </div>
</div>

{{-- Office/Unit List --}}
<div class="bg-slate-900/80 border border-slate-800 rounded-xl overflow-hidden">
    <div class="p-5 border-b border-slate-800">
        <h2 class="text-lg font-medium text-slate-100">Offices / Units</h2>
        <p class="text-sm text-slate-400 mt-1">
            Click a unit to open its UWP planned outputs.
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-800/60">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Office / Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Supervisor</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">UWP Type</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800">
                @forelse($uwps as $uwp)
                    @php
                        $payload = [
                            'id' => $uwp->id,
                            'status' => $uwp->status,
                            'return_remarks' => (string) ($uwp->return_remarks ?? ''),
                            'returned_at' => optional($uwp->returned_at)->toDateTimeString(),
                            'returned_by_role' => (string) ($uwp->returned_by_role ?? ''),
                            'office' => [
                                'id' => $uwp->office?->id,
                                'name' => $uwp->office?->name,
                            ],
                            'period' => [
                                'id' => $uwp->performancePeriod?->id,
                                'name' => $uwp->performancePeriod?->name,
                            ],
                            'supervisor' => [
                                'id' => $uwp->creator?->id,
                                'name' => $uwp->creator?->name,
                            ],
                            'department_head' => [
                                'id' => $uwp->office?->head?->id,
                                'name' => $uwp->office?->head?->name,
                            ],
                            'functions' => $uwp->uwpFunctions->map(function ($fn) {
                                return [
                                    'id' => $fn->id,
                                    'name' => $fn->name,
                                    'function_type' => $fn->function_type,
                                    'weight_percent' => (string) ($fn->weight_percent ?? ''),
                                    'weight' => (string) ($fn->weight_percent ?? ''),
                                    'mfos' => $fn->mfos->map(function ($mfo) {
                                        return [
                                            'id' => $mfo->id,
                                            'title' => $mfo->title,
                                            'target_quantity' => $mfo->target_quantity,
                                            'target_timeline' => $mfo->target_timeline,
                                            'weight_percent' => (string) ($mfo->weight_percent ?? ''),
                                            'weight' => (string) ($mfo->weight_percent ?? ''),
                                            'success_indicators' => $mfo->successIndicators->map(function ($si) {
                                                $standardsByRating = [];
                                                foreach ([5,4,3,2,1] as $r) {
                                                    $standardsByRating[(string)$r] = ['Q'=>[],'E'=>[],'T'=>[]];
                                                }

                                                foreach ($si->qetStandards as $st) {
                                                    $dim = strtolower((string) $st->dimension);
                                                    $rating = (string) $st->rating;
                                                    if (!isset($standardsByRating[$rating])) continue;

                                                    if ($dim === 'q') $standardsByRating[$rating]['Q'][] = $st->standard_text;
                                                    if ($dim === 'e') $standardsByRating[$rating]['E'][] = $st->standard_text;
                                                    if ($dim === 't') $standardsByRating[$rating]['T'][] = $st->standard_text;
                                                }

                                                $assignees = $si->assignments
                                                    ->map(fn($a) => $a->employee?->name)
                                                    ->filter()
                                                    ->values()
                                                    ->all();

                                                return [
                                                    'id' => $si->id,
                                                    'indicator_text' => $si->indicator_text,
                                                    'target_quantity' => $si->target_quantity,
                                                    'target_timeline' => $si->target_timeline,
                                                    'assignees' => $assignees,
                                                    'standards_by_rating' => $standardsByRating,
                                                ];
                                            })->values()->all(),
                                        ];
                                    })->values()->all(),
                                ];
                            })->values()->all(),
                        ];

                        $statusKey = strtolower(str_replace('-', '_', (string) ($uwp->status ?? '')));
                        $badge = match($statusKey) {
                            'returned' => ['bg'=>'bg-rose-500/10', 'text'=>'text-rose-300', 'border'=>'border-rose-500/20', 'label'=>'Returned'],
                            'submitted' => ['bg'=>'bg-blue-500/10', 'text'=>'text-blue-300', 'border'=>'border-blue-500/20', 'label'=>'Submitted'],
                            'consolidated' => ['bg'=>'bg-cyan-500/10', 'text'=>'text-cyan-300', 'border'=>'border-cyan-500/20', 'label'=>'Consolidated'],
                            'endorsed' => ['bg'=>'bg-violet-500/10', 'text'=>'text-violet-300', 'border'=>'border-violet-500/20', 'label'=>'Endorsed'],
                            'approved', 'pmt_approved' => ['bg'=>'bg-emerald-500/10', 'text'=>'text-emerald-300', 'border'=>'border-emerald-500/20', 'label'=>'Approved'],
                            'draft' => ['bg'=>'bg-slate-500/10', 'text'=>'text-slate-200', 'border'=>'border-slate-500/20', 'label'=>'Draft'],
                            default => ['bg'=>'bg-amber-500/10', 'text'=>'text-amber-300', 'border'=>'border-amber-500/20', 'label'=>ucwords(str_replace('_',' ', $statusKey !== '' ? $statusKey : 'unknown'))],
                        };
                    @endphp

                    <tr class="hover:bg-slate-800/40 transition" data-uwp-row="{{ (int) $uwp->id }}" data-uwp-row-id="{{ (int) $uwp->id }}">
                        <td class="px-4 py-3 text-sm text-slate-100 font-medium">
                            {{ $uwp->office?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            {{ $uwp->creator?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center text-slate-300">
                            Unit-Level Plan
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span data-status-badge class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <button type="button"
                                data-review-btn
                                data-uwp-id="{{ (int) $uwp->id }}"
                                data-uwp='@json($payload)'
                                class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg
                                border border-blue-500 text-blue-400 hover:bg-blue-500/10 transition">
                                Review UWP
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">
                            No Unit Work Plans found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Review UWP Modal (Flowbite-style) --}}
<div id="uwp-review-modal" data-modal-container tabindex="-1" aria-hidden="true"
     class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl px-6 my-10">
        <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100 max-h-[85vh] overflow-y-auto">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-8 py-6">
                <div>
                    <h2 class="text-xl font-semibold">Unit Work Plan</h2>
                    <p id="uwp-modal-subtitle" class="mt-1 text-sm text-slate-400">Select a UWP to view details</p>
                    <span id="uwp-modal-period" class="hidden">-</span>
                </div>
                <button type="button" data-review-close
                        class="inline-flex items-center justify-center rounded-lg border border-slate-800 bg-slate-950/60 px-2.5 py-2 text-slate-400 transition hover:bg-slate-900 hover:text-white">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="flex items-stretch gap-10 border-b border-slate-800 px-8 py-6">
                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Office / Unit</p>
                    <p id="uwp-modal-office" class="mt-1 font-medium">-</p>
                </div>

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Supervisor</p>
                    <p id="uwp-modal-supervisor" class="mt-1 font-medium">-</p>
                </div>

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Department Head</p>
                    <p id="uwp-modal-dept-head" class="mt-1 font-medium">-</p>
                </div>

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Status</p>
                    <span id="uwp-modal-status" class="mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold">-</span>
                </div>
            </div>

            <div id="uwp-return-remarks-wrap" class="hidden mx-8 mt-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-rose-200">
                <p class="text-sm font-semibold">Returned Remarks</p>
                <p id="uwp-return-remarks-meta" class="mt-1 text-xs text-rose-200/80"></p>
                <div id="uwp-return-remarks-text" class="mt-2 whitespace-pre-line text-sm text-rose-100">-</div>
            </div>

            <div class="px-8 py-6">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Planned Outputs</h3>
                <div class="overflow-hidden rounded-xl border border-slate-800">
                    <div class="max-h-[42vh] overflow-y-auto overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-900/60 text-xs uppercase tracking-widest text-slate-400">
                            <tr>
                                <th class="px-5 py-4 text-left">PPA / MFO</th>
                                <th class="px-5 py-4 text-center">Success Indicators</th>
                                <th class="px-5 py-4 text-center">Function</th>
                            </tr>
                            </thead>
                            <tbody id="uwp-outputs-tbody" class="divide-y divide-slate-800 bg-slate-950"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <form id="uwp-review-form"
                  method="POST"
                  action="{{ route('dept-head.uwp.review') }}"
                  data-endorse-action="{{ route('dept-head.uwp.review') }}"
                  data-return-action="{{ route('dept-head.uwp.return') }}">
                @csrf
                <input type="hidden" name="unit_work_plan_id" id="uwp-modal-uwp-id" value="">
                <input type="hidden" name="action" id="uwp-modal-action" value="">

                <div class="px-8 pb-6 space-y-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Review Remarks (required if returning)
                    </label>
                    <textarea
                        name="remarks"
                        id="uwp-modal-remarks"
                        rows="3"
                        class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        style="background:#0f172a;color:#e5e7eb;"
                        placeholder="Add clear instructions or justification for your decision..."></textarea>
                    <p class="text-[11px] text-slate-500 mt-2">
                        Consolidating will create or refresh the office OPCR using all submitted UWPs for this period.
                    </p>
                </div>

                <div class="flex items-center justify-between border-t border-slate-800 px-8 py-5">
                    <p class="text-xs text-slate-500">Department Head can endorse or return submitted UWPs only.</p>
                    <div class="flex items-center gap-3">
                        <button type="button" data-review-close class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button>

                        <button type="button"
                            id="btn-return-uwp"
                            data-admin-loading="true"
                            data-loading-text="Returning..."
                            class="inline-flex items-center gap-2 rounded-lg border border-rose-500/30 bg-rose-600/10 px-4 py-2 text-sm font-semibold text-rose-200 transition hover:bg-rose-600/20">
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-rose-200/40 border-t-rose-200"></span>
                            <span data-button-label>Return to Supervisor</span>
                        </button>

                        <button type="button"
                                id="btn-endorse-uwp"
                                data-admin-loading="true"
                                data-loading-text="Processing..."
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 transition hover:bg-emerald-500">
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                            <span data-button-label>Consolidate to OPCR</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- MODAL: Success Indicators --}}
{{-- ========================= --}}
<div id="uwp-indicators-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
    <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden relative">
        <div class="bg-slate-950/80 border-b border-slate-800 px-6 py-5 space-y-1">
            <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
            <h3 id="uwp-indicators-title" class="text-2xl font-semibold text-white">--</h3>
            <p class="text-sm text-slate-400">Read-only list of indicators for this output. One employee is assigned per success indicator.</p>
        </div>
        <button type="button" data-modal-hide="uwp-indicators-modal" class="absolute right-4 top-4 text-slate-400 hover:text-white">
            <span class="sr-only">Close</span>
            &times;
        </button>

        <div class="px-6 py-6">
            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-950/60">
                <div class="max-h-[420px] overflow-y-auto">
                    <table class="w-full text-sm text-slate-100">
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="px-5 py-4 text-left">Success Indicator</th>
                                <th class="px-5 py-4 text-left">Target Summary</th>
                                <th class="px-5 py-4 text-center">Standards</th>
                                <th class="px-5 py-4 text-left">Assigned Employee</th>
                            </tr>
                        </thead>
                        <tbody id="uwp-indicators-table-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button"
                        data-modal-hide="uwp-indicators-modal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- MODAL: Standards Viewer --}}
{{-- ========================= --}}
<div id="uwp-standards-viewer-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
    <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
        <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Target Difficulty / Standards</p>
                <h3 class="text-lg font-semibold text-white">Standards (Q/E/T)</h3>
                <p class="text-sm text-slate-400 mt-1">
                    Indicator: <span id="uwp-standards-indicator-label" class="font-semibold text-slate-100">--</span>
                </p>
            </div>
            <button type="button" data-modal-hide="uwp-standards-viewer-modal" class="text-slate-400 hover:text-white">
                <span class="sr-only">Close</span>
                &times;
            </button>
        </div>

        <div class="mt-4 space-y-4">
            <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
                <table class="w-full text-sm text-slate-100">
                    <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Rating</th>
                            <th class="px-4 py-3 text-left">Quality (Q)</th>
                            <th class="px-4 py-3 text-left">Efficiency (E)</th>
                            <th class="px-4 py-3 text-left">Timeliness (T)</th>
                        </tr>
                    </thead>
                    <tbody id="uwp-standards-table-body" class="divide-y divide-slate-800"></tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex justify-end border-t border-slate-800 pt-3">
            <button data-modal-hide="uwp-standards-viewer-modal"
                    class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                Close
            </button>
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- MODAL: Assigned Employees Viewer (show Success Indicator) --}}
{{-- ========================= --}}
<div id="uwp-assignees-viewer-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
    <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
        <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employees</p>
                <h3 class="text-lg font-semibold text-white">Employees under the selected Office/Unit</h3>
                <div class="mt-1 space-y-1 text-sm text-slate-400">
                    <p>
                        Office / Unit:
                        <span id="uwp-assignees-unit-label" class="font-semibold text-slate-100">--</span>
                    </p>
                    <p>
                        Success Indicator:
                        <span id="uwp-assignees-indicator-label" class="font-semibold text-slate-100">--</span>
                    </p>
                </div>
            </div>
            <button type="button" data-modal-hide="uwp-assignees-viewer-modal" class="text-slate-400 hover:text-white">
                <span class="sr-only">Close</span>
                &times;
            </button>
        </div>

        <div class="mt-4 space-y-4">
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <input type="text"
                           placeholder="Search employee…"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                           style="background:#0f172a;color:#e5e7eb;">
                </div>
                <div>
                    <select
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                        style="background:#0f172a;color:#e5e7eb;">
                        <option>All Status</option>
                        <option>Assigned</option>
                        <option>Unassigned</option>
                    </select>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
                <table class="w-full text-sm text-slate-100">
                    <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee Name</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Assigned For</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody id="uwp-assignees-table-body" class="divide-y divide-slate-800"></tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex justify-end border-t border-slate-800 pt-3">
            <button data-modal-hide="uwp-assignees-viewer-modal"
                    class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                Close
            </button>
        </div>
    </div>
</div>

<div id="uwp-review-workspace-modal" data-modal-container tabindex="-1" aria-hidden="true"
     class="fixed inset-0 z-[70] hidden items-start justify-center overflow-y-auto bg-black/70 px-4 py-4 backdrop-blur-sm sm:py-8">
    <div class="w-full max-w-[1200px]">
        <div class="flex h-[780px] max-h-[calc(100vh-2rem)] flex-col overflow-hidden rounded-[24px] border border-slate-800 bg-[#0f131d] text-slate-100 shadow-2xl sm:max-h-[calc(100vh-4rem)]">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-5 py-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-lg font-semibold text-white">UWP Review</h2>
                        <span class="rounded-full border border-indigo-500/20 bg-indigo-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-200">Stage I - Planning</span>
                    </div>
                    <div class="hidden">
                        <p id="uwp-workspace-subtitle" class="mt-2 text-sm text-slate-400">Select a UWP to view details</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-400">
                            <span id="uwp-workspace-office-inline">-</span>
                            <span class="text-slate-600">•</span>
                            <span id="uwp-workspace-period-inline">-</span>
                            <span class="text-slate-600">•</span>
                            <span id="uwp-workspace-supervisor-inline">-</span>
                            <span id="uwp-workspace-status-inline" class="ml-1 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold">-</span>
                            <span id="uwp-workspace-output-count-inline" class="inline-flex rounded-full bg-slate-800 px-2.5 py-1 text-xs font-semibold text-slate-200">0 outputs</span>
                        </div>
                    </div>
                </div>
                <button type="button" data-review-close
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-700 bg-slate-950/60 text-slate-400 transition hover:bg-slate-900 hover:text-white">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div id="uwp-workspace-return-remarks-wrap" class="hidden border-b border-rose-500/20 bg-rose-500/5 px-5 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-200">Returned Remarks</p>
                <p id="uwp-workspace-return-remarks-meta" class="mt-1.5 text-[11px] text-rose-200/80"></p>
                <div id="uwp-workspace-return-remarks-text" class="mt-1.5 whitespace-pre-line text-sm text-rose-100">-</div>
            </div>

            <div class="grid min-h-0 flex-1 lg:grid-cols-[300px_minmax(0,1fr)]">
                <aside class="flex min-h-0 flex-col border-b border-slate-800 lg:border-b-0 lg:border-r">
                    <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Planned Outputs</p>
                        <span id="uwp-workspace-output-count-badge" class="text-sm font-semibold text-blue-300">0</span>
                    </div>
                    <div class="flex border-b border-slate-800 px-2 pt-2">
                        <button type="button" data-workspace-function-tab="all" class="flex-1 border-b-2 border-blue-400 pb-2 text-xs font-semibold text-white transition">All</button>
                        <button type="button" data-workspace-function-tab="core" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 transition hover:text-slate-300">Core</button>
                        <button type="button" data-workspace-function-tab="support" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 transition hover:text-slate-300">Support</button>
                    </div>
                    <div id="uwp-workspace-output-list" class="min-h-0 space-y-2 overflow-y-auto px-2 py-2"></div>
                </aside>

                <section class="flex min-h-0 flex-col">
                    <div class="border-b border-slate-800 px-5 py-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 id="uwp-workspace-detail-title" class="text-lg font-semibold leading-tight text-white">No output selected</h3>
                            <span id="uwp-workspace-detail-function" class="hidden rounded-md border px-2 py-1 text-xs font-medium"></span>
                            <span id="uwp-workspace-detail-weight" class="hidden text-sm font-semibold text-slate-300"></span>
                        </div>
                    </div>

                    <div class="border-b border-slate-800 px-4">
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" data-uwp-workspace-tab="overview" class="border-b-2 border-blue-400 px-2.5 py-2.5 text-sm font-semibold text-white">Overview</button>
                            <button type="button" data-uwp-workspace-tab="indicators" class="border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Success Indicators</button>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                        <div data-uwp-workspace-panel="overview" class="space-y-5">
                            <div class="hidden">
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Target Summary</p>
                                <p id="uwp-workspace-target-summary" class="mt-2 text-lg leading-snug text-white">-</p>
                            </div>
                            <div class="hidden grid gap-5 sm:grid-cols-2">
                                <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Function Type</p><div id="uwp-workspace-function-copy" class="mt-2"></div></div>
                                <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Weight</p><p id="uwp-workspace-weight-copy" class="mt-2 text-lg font-semibold text-white">-</p></div>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Linked Success Indicators</p>
                                <div id="uwp-workspace-overview-indicators" class="mt-3 space-y-2.5"></div>
                            </div>
                        </div>

                        <div data-uwp-workspace-panel="indicators" class="hidden">
                            <div class="overflow-hidden rounded-xl border border-slate-800">
                                <table class="min-w-full text-sm text-slate-200">
                                    <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Success Indicator</th>
                                            <th class="px-4 py-3 text-left">Target Summary</th>
                                            <th class="px-4 py-3 text-center">Standards</th>
                                            <th class="px-4 py-3 text-center">Assigned</th>
                                        </tr>
                                    </thead>
                                    <tbody id="uwp-workspace-indicators-body" class="divide-y divide-slate-800"></tbody>
                                </table>
                            </div>
                        </div>

                        <div data-uwp-workspace-panel="standards" class="hidden space-y-4">
                            <div class="hidden"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Indicator</p><p id="uwp-workspace-standards-indicator" class="mt-1.5 text-base font-semibold text-white">-</p></div>
                            <div class="overflow-hidden rounded-xl border border-slate-800">
                                <table class="min-w-full text-sm text-slate-100">
                                    <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                                        <tr><th class="px-4 py-3 text-left">Rating</th><th class="px-4 py-3 text-left">Quality (Q)</th><th class="px-4 py-3 text-left">Efficiency (E)</th><th class="px-4 py-3 text-left">Timeliness (T)</th></tr>
                                    </thead>
                                    <tbody id="uwp-workspace-standards-body" class="divide-y divide-slate-800"></tbody>
                                </table>
                            </div>
                        </div>

                        <div data-uwp-workspace-panel="assignees" class="hidden space-y-4">
                            <div class="hidden"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Indicator</p><p id="uwp-workspace-assignees-indicator" class="mt-1.5 text-base font-semibold text-white">-</p></div>
                            <div class="overflow-hidden rounded-xl border border-slate-800">
                                <table class="min-w-full text-sm text-slate-100">
                                    <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                                        <tr><th class="px-4 py-3 text-left">Employee Name</th><th class="px-4 py-3 text-left">Office / Unit</th><th class="px-4 py-3 text-left">Assigned For</th><th class="px-4 py-3 text-left">Status</th></tr>
                                    </thead>
                                    <tbody id="uwp-workspace-assignees-body" class="divide-y divide-slate-800"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <form id="uwp-workspace-review-form" method="POST" action="{{ route('dept-head.uwp.review') }}" data-endorse-action="{{ route('dept-head.uwp.review') }}" data-return-action="{{ route('dept-head.uwp.return') }}">
                @csrf
                <input type="hidden" name="unit_work_plan_id" id="uwp-workspace-uwp-id" value="">
                <input type="hidden" name="action" id="uwp-workspace-action" value="">
                <input type="hidden" name="signature" id="uwp-workspace-signature" value="">
                <div class="grid shrink-0 gap-3 border-t border-slate-800 px-5 py-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Return Remarks</label>
                        <div class="mt-2 flex flex-col gap-2 lg:flex-row lg:items-center">
                            <textarea name="remarks" id="uwp-workspace-remarks" rows="2" class="min-h-[42px] flex-1 rounded-xl border border-slate-700 bg-slate-950/80 px-3.5 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Required if returning to supervisor..."></textarea>
                            <p class="text-[11px] leading-relaxed text-slate-500 lg:w-28">Required when returning</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-end gap-2.5">
                        <button type="button" data-review-close class="rounded-xl border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button>
                        <button type="button" id="btn-return-uwp-workspace" data-admin-loading="true" data-loading-text="Returning..." class="inline-flex items-center gap-2 rounded-xl border border-rose-500/30 bg-rose-600/10 px-4 py-2 text-sm font-semibold text-rose-200 transition hover:bg-rose-600/20"><span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-rose-200/40 border-t-rose-200"></span><span data-button-label>Return to Supervisor</span></button>
                        <button type="button" id="btn-endorse-uwp-workspace" data-admin-loading="true" data-loading-text="Processing..." class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 transition hover:bg-emerald-500"><span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span><span data-button-label>Consolidate to OPCR</span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.signature-pad-modal', [
    'modalId' => 'uwp-signature-modal',
    'canvasId' => 'uwp-signature-canvas',
    'clearButtonId' => 'uwp-signature-clear',
    'confirmButtonId' => 'uwp-signature-confirm',
    'cancelSelector' => 'data-signature-close',
    'title' => 'Department Head Signature Required',
    'message' => 'Please sign before consolidating this UWP into OPCR.',
    'confirmText' => 'Confirm and Consolidate',
])

@push('scripts')
<script>
    const standardRatings = [5, 4, 3, 2, 1];

    let indicatorStandardsBody;
    let standardsModal;
    let assigneesModal;
    const bodyOverflowClass = 'overflow-hidden';

    // runtime cache from selected UWP payload
    let selectedUwp = null;
    let selectedWorkspaceOutputIndex = 0;
    let selectedWorkspaceIndicatorIndex = 0;
    let activeWorkspaceTab = 'overview';
    let activeWorkspaceFunctionTab = 'all';
    let signaturePadContext = null;
    let signaturePadHasInk = false;
    let signaturePadPointerActive = false;

    function createEmptyStandardsRow() {
        return { Q: [], E: [], T: [] };
    }

    function renderIndicatorStandardsFromPayload(mfoTitle, indicatorText) {
        if (!indicatorStandardsBody || !selectedUwp) return;
        indicatorStandardsBody.innerHTML = '';

        const allMfos = (selectedUwp.functions || []).flatMap(fn => fn.mfos || []);
        const mfo = allMfos.find(x => (x.title || '') === (mfoTitle || ''));
        const indicators = (mfo && Array.isArray(mfo.success_indicators)) ? mfo.success_indicators : [];
        const indicator = indicators.find(si => (si.indicator_text || '') === (indicatorText || ''));

        const standardsByRating = (indicator && indicator.standards_by_rating) ? indicator.standards_by_rating : {};

        standardRatings.forEach((level) => {
            const row = standardsByRating[String(level)] || createEmptyStandardsRow();

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';

            const ratingTd = document.createElement('td');
            ratingTd.className = 'px-4 py-3 font-semibold';
            ratingTd.textContent = level;

            const makeListCell = (items) => {
                const td = document.createElement('td');
                td.className = 'px-4 py-3 align-top';
                if (!items || items.length === 0) {
                    td.textContent = '\u2014';
                    return td;
                }
                const ul = document.createElement('ul');
                ul.className = 'list-disc space-y-1 pl-4 text-slate-200';
                items.forEach((item) => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    ul.appendChild(li);
                });
                td.appendChild(ul);
                return td;
            };

            tr.append(
                ratingTd,
                makeListCell(row.Q || row.q || []),
                makeListCell(row.E || row.e || []),
                makeListCell(row.T || row.t || [])
            );
            indicatorStandardsBody.appendChild(tr);
        });
    }

    function showModal(modal) {
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add(bodyOverflowClass);
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.add('hidden');
        clearFlowbiteBackdrops();
    }

    function cleanupFlowbiteBackdrop() {
        document.querySelectorAll('[modal-backdrop], [data-modal-backdrop], .modal-backdrop').forEach((el) => el.remove());
        document.querySelectorAll('body > div').forEach((el) => {
            if (!(el instanceof HTMLDivElement)) return;
            const className = String(el.className || '');
            const looksLikeFlowbiteBackdrop =
                className.includes('bg-gray-900/50') &&
                className.includes('fixed') &&
                className.includes('inset-0') &&
                className.includes('z-40');

            if (looksLikeFlowbiteBackdrop) {
                el.remove();
            }
        });
        document.body.classList.remove(bodyOverflowClass);
    }

    function clearFlowbiteBackdrops() {
        cleanupFlowbiteBackdrop();
    }

    function openSignatureModal() {
        const modal = document.getElementById('uwp-signature-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeSignatureModal() {
        const modal = document.getElementById('uwp-signature-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        if (window.clearSignaturePad_uwp_signature_modal) {
            window.clearSignaturePad_uwp_signature_modal();
        }
        clearFlowbiteBackdrops();
    }

    function closeReviewModalSafely() {
        const modal = document.getElementById('uwp-review-workspace-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        clearFlowbiteBackdrops();
    }

    function initSignatureModal() {
        const modal = document.getElementById('uwp-signature-modal');
        const confirmButton = document.getElementById('uwp-signature-confirm');
        if (!modal || !confirmButton) return;

        modal.querySelectorAll('[data-signature-close]').forEach((button) => {
            button.addEventListener('click', closeSignatureModal);
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeSignatureModal();
            }
        });

        confirmButton.addEventListener('click', async () => {
            const signatureDataUrl = window.getSignatureData_uwp_signature_modal ? window.getSignatureData_uwp_signature_modal() : null;
            if (!signatureDataUrl) {
                window.PMSnackbar?.show({
                    type: 'error',
                    message: 'Please provide a signature first.',
                });
                return;
            }
            const form = document.getElementById('uwp-workspace-review-form');
            const btnEndorse = document.getElementById('btn-endorse-uwp-workspace');
            const btnReturn = document.getElementById('btn-return-uwp-workspace');

            if (!form || !btnEndorse) return;

            const url = String(form.dataset.endorseAction || '').trim();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                              form.querySelector('input[name="_token"]')?.value || '';
            const uwpIdValue = String(selectedUwp?.id || document.getElementById('uwp-workspace-uwp-id')?.value || '').trim();

            setButtonLoading(btnEndorse, true);
            confirmButton.disabled = true;
            confirmButton.classList.add('opacity-80', 'cursor-not-allowed');

            try {
                const fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('unit_work_plan_id', uwpIdValue);
                fd.append('action', 'endorse');
                fd.append('signature', signatureDataUrl);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: fd,
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (_) {
                    data = {};
                }

                if (!response.ok || !data?.success) {
                    throw new Error(data?.message || data?.error || 'Unable to endorse UWP');
                }

                const endorsedAt = data?.endorsed_at || new Date().toISOString();
                const endorsedStatus = data?.status || 'consolidated';
                const uwpId = Number(selectedUwp?.id || data?.uwp_id || uwpIdValue || 0);

                if (!selectedUwp) selectedUwp = {};
                selectedUwp.id = selectedUwp.id || uwpId;
                selectedUwp.status = endorsedStatus;
                selectedUwp.endorsed_at = endorsedAt;
                selectedUwp.return_remarks = '';
                selectedUwp.returned_at = null;
                selectedUwp.returned_by_role = null;

                hydrateReviewModal(selectedUwp);
                updateDeptHeadListRow(selectedUwp.id || uwpId, endorsedStatus, {
                    return_remarks: '',
                    returned_at: null,
                    returned_by_role: null,
                });

                const row = document.querySelector(`[data-uwp-row-id="${uwpId}"]`) || document.querySelector(`[data-uwp-row="${uwpId}"]`);
                const reviewBtn = row?.querySelector(`[data-uwp-id="${uwpId}"][data-uwp]`) || row?.querySelector('[data-review-btn][data-uwp]');
                if (reviewBtn) {
                    let nextPayload = {};
                    try {
                        nextPayload = JSON.parse(reviewBtn.getAttribute('data-uwp') || '{}');
                    } catch (_) {
                        nextPayload = {};
                    }
                    nextPayload.status = normalizeStatusKey(endorsedStatus);
                    nextPayload.endorsed_at = endorsedAt;
                    nextPayload.return_remarks = '';
                    nextPayload.returned_at = null;
                    nextPayload.returned_by_role = null;
                    reviewBtn.setAttribute('data-uwp', JSON.stringify(nextPayload));
                }

                closeSignatureModal();
                closeReviewModalSafely();

                window.PMSnackbar?.show({
                    type: 'success',
                    message: 'UWP consolidated into OPCR.',
                });
            } catch (error) {
                window.PMSnackbar?.show({
                    type: 'error',
                    message: error?.message || 'Unable to endorse UWP right now. Please try again.',
                });
            } finally {
                setButtonLoading(btnEndorse, false);
                confirmButton.disabled = false;
                confirmButton.classList.remove('opacity-80', 'cursor-not-allowed');
                clearFlowbiteBackdrops();
            }
        });

        window.addEventListener('resize', () => {
            if (!modal.classList.contains('hidden')) {
                resizeSignatureCanvas();
            }
        });
    }

    function openStandardsViewer(mfoTitle, indicatorText) {
        if (!mfoTitle || !indicatorText) return;
        if (!standardsModal) standardsModal = document.getElementById('uwp-standards-viewer-modal');
        if (!standardsModal) return;

        const label = document.getElementById('uwp-standards-indicator-label');
        if (label) label.textContent = indicatorText;

        renderIndicatorStandardsFromPayload(mfoTitle, indicatorText);
        showModal(standardsModal);
    }

    function renderAssigneesTable(unit, indicator, assignees) {
        const tbody = document.getElementById('uwp-assignees-table-body');
        if (!tbody) return;

        tbody.innerHTML = '';
        const list = Array.isArray(assignees) ? assignees : [];

        if (!list.length) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/30';
            tr.innerHTML = `
                <td class="px-4 py-3 text-slate-300" colspan="4">
                    <span class="text-slate-400">No assigned employee for this indicator.</span>
                </td>
            `;
            tbody.appendChild(tr);
            return;
        }

        list.forEach((name) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/30';
            tr.innerHTML = `
                <td class="px-4 py-3 text-slate-100">${escapeHtml(name)}</td>
                <td class="px-4 py-3 text-slate-300">${escapeHtml(unit)}</td>
                <td class="px-4 py-3 text-slate-300">${escapeHtml(indicator)}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                        Assigned
                    </span>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function openAssigneesViewer(unit, mfoTitle, indicatorText) {
        const currentUnit = unit || selectedUwp?.office?.name || '—';
        const currentIndicator = indicatorText || '--';

        if (!assigneesModal) assigneesModal = document.getElementById('uwp-assignees-viewer-modal');
        if (!assigneesModal) return;

        const unitLabel = document.getElementById('uwp-assignees-unit-label');
        if (unitLabel) unitLabel.textContent = currentUnit;

        const indicatorLabel = document.getElementById('uwp-assignees-indicator-label');
        if (indicatorLabel) indicatorLabel.textContent = currentIndicator;

        const allMfos = (selectedUwp?.functions || []).flatMap(fn => fn.mfos || []);
        const mfo = allMfos.find(x => (x.title || '') === (mfoTitle || ''));
        const indicators = (mfo && Array.isArray(mfo.success_indicators)) ? mfo.success_indicators : [];
        const indicator = indicators.find(si => (si.indicator_text || '') === (currentIndicator || ''));

        const assignees = (indicator && Array.isArray(indicator.assignees)) ? indicator.assignees : [];
        renderAssigneesTable(currentUnit, currentIndicator, assignees);
        showModal(assigneesModal);
    }

    function initModalHandlers() {
        const reviewModal = document.getElementById('uwp-review-workspace-modal');

        document.querySelectorAll('[data-review-close]').forEach((btn) => {
            btn.addEventListener('click', () => closeReviewModalSafely());
        });

        reviewModal?.addEventListener('click', (event) => {
            if (event.target === reviewModal) {
                closeReviewModalSafely();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            window.setTimeout(() => {
                const modalEl = document.getElementById('uwp-review-workspace-modal');
                if (modalEl?.classList.contains('hidden')) {
                    cleanupFlowbiteBackdrop();
                }
            }, 0);
        });

        document.querySelectorAll('[data-uwp-workspace-tab]').forEach((button) => {
            button.addEventListener('click', () => {
                setWorkspaceTab(button.getAttribute('data-uwp-workspace-tab') || 'overview');
                renderWorkspaceDetail();
            });
        });

        document.querySelectorAll('[data-workspace-function-tab]').forEach((button) => {
            button.addEventListener('click', () => {
                selectedWorkspaceOutputIndex = 0;
                selectedWorkspaceIndicatorIndex = 0;
                setWorkspaceFunctionTab(button.getAttribute('data-workspace-function-tab') || 'all');
                renderWorkspaceModal();
            });
        });
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function normalizeStatusKey(status) {
        return String(status || '').toLowerCase().replaceAll('-', '_');
    }

    function labelStatus(status) {
        const s = normalizeStatusKey(status);
        if (!s) return '-';
        return s.replaceAll('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    function statusBadgeClass(status) {
        const s = normalizeStatusKey(status);
        if (s === 'returned') {
            return 'border-rose-500/30 bg-rose-500/10 text-rose-300';
        }
        if (s === 'submitted') {
            return 'border-blue-500/30 bg-blue-500/10 text-blue-300';
        }
        if (s === 'consolidated') {
            return 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300';
        }
        if (s === 'endorsed') {
            return 'border-violet-500/30 bg-violet-500/10 text-violet-300';
        }
        if (s === 'approved' || s === 'pmt_approved') {
            return 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300';
        }
        if (s === 'draft') {
            return 'border-slate-500/30 bg-slate-500/10 text-slate-200';
        }

        return 'border-slate-500/30 bg-slate-500/10 text-slate-200';
    }

    function setButtonLoading(buttonEl, isLoading) {
        if (!buttonEl) return;

        const spinner = buttonEl.querySelector('[data-button-spinner]');
        const label = buttonEl.querySelector('[data-button-label]');

        if (label && !buttonEl.dataset.originalLabel) {
            buttonEl.dataset.originalLabel = label.textContent;
        }

        if (isLoading) {
            buttonEl.disabled = true;
            buttonEl.classList.add('opacity-80', 'cursor-not-allowed');
            if (spinner) spinner.classList.remove('hidden');
            if (label) label.textContent = buttonEl.dataset.loadingText || 'Processing...';
            return;
        }

        buttonEl.disabled = false;
        buttonEl.classList.remove('opacity-80', 'cursor-not-allowed');
        if (spinner) spinner.classList.add('hidden');
        if (label && buttonEl.dataset.originalLabel) {
            label.textContent = buttonEl.dataset.originalLabel;
        }
    }

    function setWorkspaceFunctionTab(tabName) {
        activeWorkspaceFunctionTab = tabName || 'all';
        document.querySelectorAll('[data-workspace-function-tab]').forEach((button) => {
            const active = button.getAttribute('data-workspace-function-tab') === activeWorkspaceFunctionTab;
            button.classList.toggle('border-blue-400', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('border-transparent', !active);
            button.classList.toggle('text-slate-400', !active);
        });
        renderWorkspaceOutputList();
    }

    function updateDeptHeadListRow(uwpId, newStatus, options = {}) {
        const row = document.querySelector(`[data-uwp-row-id="${uwpId}"]`) || document.querySelector(`[data-uwp-row="${uwpId}"]`);
        if (!row) return;

        const statusKey = normalizeStatusKey(newStatus);
        const statusBadge = row.querySelector('[data-status-badge]');
        if (statusBadge) {
            statusBadge.className = `inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border ${statusBadgeClass(statusKey)}`;
            statusBadge.textContent = labelStatus(statusKey);
        }

        const reviewBtn = row.querySelector(`[data-uwp-id="${uwpId}"][data-uwp]`) || row.querySelector('[data-review-btn]');
        if (!reviewBtn) return;

        try {
            const payload = JSON.parse(reviewBtn.getAttribute('data-uwp') || '{}');
            payload.status = statusKey;

            if (statusKey === 'returned') {
                payload.return_remarks = options.return_remarks ?? payload.return_remarks ?? '';
                payload.returned_at = options.returned_at ?? payload.returned_at ?? new Date().toISOString();
                payload.returned_by_role = 'dept-head';
            } else {
                if (Object.prototype.hasOwnProperty.call(options, 'return_remarks')) payload.return_remarks = options.return_remarks;
                if (Object.prototype.hasOwnProperty.call(options, 'returned_at')) payload.returned_at = options.returned_at;
                if (Object.prototype.hasOwnProperty.call(options, 'returned_by_role')) payload.returned_by_role = options.returned_by_role;
            }

            reviewBtn.setAttribute('data-uwp', JSON.stringify(payload));
        } catch (_) {
            // Keep existing payload if malformed
        }
    }

    function buildFunctionBadge(fnType) {
        const t = String(fnType || '').toLowerCase();
        if (t === 'core') {
            return `<span class="px-2 py-1 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Core</span>`;
        }
        if (t === 'support') {
            return `<span class="px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-300 border border-blue-400/30">Support</span>`;
        }
        return `<span class="px-2 py-1 rounded-md text-xs font-medium bg-slate-500/10 text-slate-200 border border-slate-500/20">${escapeHtml(labelStatus(fnType))}</span>`;
    }

    function computeIndicatorCount(mfo) {
        const list = (mfo && Array.isArray(mfo.success_indicators)) ? mfo.success_indicators : [];
        return list.length;
    }

    function normalizeTargetQuantity(value) {
        if (value === null || value === undefined || value === '') {
            return '';
        }

        const numeric = Number(value);
        if (!Number.isFinite(numeric)) {
            return String(value).trim();
        }

        return Number.isInteger(numeric)
            ? String(numeric)
            : numeric.toFixed(2).replace(/\.00$/, '').replace(/(\.\d*[1-9])0+$/, '$1');
    }

    function formatTargetTimelineDisplay(targetQuantity, targetTimeline) {
        const summary = String(targetTimeline || '').trim();
        if (summary.toLowerCase() === 'multiple indicator targets') {
            return summary;
        }

        const quantity = targetQuantity === null || targetQuantity === undefined || targetQuantity === ''
            ? ''
            : normalizeTargetQuantity(targetQuantity);
        const timeline = targetTimeline === null || targetTimeline === undefined || targetTimeline === ''
            ? ''
            : String(targetTimeline).trim();

        if (quantity !== '' && timeline !== '') {
            return `${quantity} ${timeline}`.trim();
        }

        if (quantity !== '') {
            return quantity;
        }

        if (timeline !== '') {
            return timeline;
        }

        return '-';
    }

    function getIndicatorTargetSummary(indicator) {
        return formatTargetTimelineDisplay(indicator?.target_quantity, indicator?.target_timeline);
    }

    function getMfoTargetSummary(mfo) {
        const indicators = Array.isArray(mfo?.success_indicators) ? mfo.success_indicators : [];
        const summaries = Array.from(new Set(
            indicators
                .map((indicator) => getIndicatorTargetSummary(indicator))
                .filter((value) => String(value || '').trim() !== '' && value !== '-')
        ));

        if (summaries.length === 1) {
            return summaries[0];
        }

        if (summaries.length > 1) {
            return 'Multiple indicator targets';
        }

        return formatTargetTimelineDisplay(mfo?.target_quantity, mfo?.target_timeline);
    }

    function normalizeWeightPercent(...values) {
        for (const value of values) {
            if (value === null || value === undefined || value === '') {
                continue;
            }

            const numeric = Number(value);
            if (Number.isFinite(numeric)) {
                return Number.isInteger(numeric)
                    ? String(numeric)
                    : numeric.toFixed(2).replace(/\.00$/, '').replace(/(\.\d*[1-9])0+$/, '$1');
            }

            const text = String(value).trim();
            if (text !== '') {
                return text;
            }
        }

        return '';
    }

    function getWorkspaceOutputs() {
        const functions = Array.isArray(selectedUwp?.functions) ? selectedUwp.functions : [];
        return functions.flatMap((fn) => {
            const mfos = Array.isArray(fn?.mfos) ? fn.mfos : [];
            return mfos.map((mfo) => ({
                title: mfo?.title || '-',
                function_type: fn?.function_type || '',
                weight_percent: normalizeWeightPercent(
                    mfo?.weight_percent,
                    mfo?.weight,
                    fn?.weight_percent,
                    fn?.weight
                ),
                target_summary: getMfoTargetSummary(mfo),
                success_indicators: Array.isArray(mfo?.success_indicators) ? mfo.success_indicators : [],
            }));
        });
    }

    function getSelectedWorkspaceOutput() {
        const outputs = getWorkspaceOutputs();
        if (!outputs.length) return null;
        selectedWorkspaceOutputIndex = Math.min(Math.max(selectedWorkspaceOutputIndex, 0), outputs.length - 1);
        return outputs[selectedWorkspaceOutputIndex] || null;
    }

    function getSelectedWorkspaceIndicator() {
        const output = getSelectedWorkspaceOutput();
        const indicators = Array.isArray(output?.success_indicators) ? output.success_indicators : [];
        if (!indicators.length) return null;
        selectedWorkspaceIndicatorIndex = Math.min(Math.max(selectedWorkspaceIndicatorIndex, 0), indicators.length - 1);
        return indicators[selectedWorkspaceIndicatorIndex] || null;
    }

    function setWorkspaceTab(tabName) {
        activeWorkspaceTab = tabName || 'overview';
        document.querySelectorAll('[data-uwp-workspace-tab]').forEach((button) => {
            const active = button.getAttribute('data-uwp-workspace-tab') === activeWorkspaceTab;
            button.classList.toggle('border-blue-400', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('font-semibold', active);
            button.classList.toggle('border-transparent', !active);
            button.classList.toggle('text-slate-400', !active);
            button.classList.toggle('font-medium', !active);
        });

        document.querySelectorAll('[data-uwp-workspace-panel]').forEach((panel) => {
            panel.classList.toggle('hidden', panel.getAttribute('data-uwp-workspace-panel') !== activeWorkspaceTab);
        });
    }

    function renderWorkspaceStandards() {
        const indicator = getSelectedWorkspaceIndicator();
        const label = document.getElementById('uwp-workspace-standards-indicator');
        const tbody = document.getElementById('uwp-workspace-standards-body');
        if (!label || !tbody) return;

        label.textContent = indicator?.indicator_text || 'No success indicator selected';
        tbody.innerHTML = '';

        const standardsByRating = indicator?.standards_by_rating || {};
        standardRatings.forEach((level) => {
            const row = standardsByRating[String(level)] || createEmptyStandardsRow();
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            const makeListCell = (items) => {
                const td = document.createElement('td');
                td.className = 'px-4 py-3 align-top';
                if (!items || !items.length) {
                    td.textContent = '-';
                    return td;
                }
                const ul = document.createElement('ul');
                ul.className = 'list-disc space-y-1 pl-4 text-slate-200';
                items.forEach((item) => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    ul.appendChild(li);
                });
                td.appendChild(ul);
                return td;
            };

            const ratingTd = document.createElement('td');
            ratingTd.className = 'px-4 py-3 font-semibold text-white';
            ratingTd.textContent = level;
            tr.append(ratingTd, makeListCell(row.Q || row.q || []), makeListCell(row.E || row.e || []), makeListCell(row.T || row.t || []));
            tbody.appendChild(tr);
        });
    }

    function renderWorkspaceAssignees() {
        const indicator = getSelectedWorkspaceIndicator();
        const tbody = document.getElementById('uwp-workspace-assignees-body');
        const label = document.getElementById('uwp-workspace-assignees-indicator');
        if (!tbody || !label) return;

        label.textContent = indicator?.indicator_text || 'No success indicator selected';
        tbody.innerHTML = '';

        const assignees = Array.isArray(indicator?.assignees) ? indicator.assignees : [];
        if (!assignees.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-slate-400">No assigned employee for this indicator.</td></tr>';
            return;
        }

        assignees.forEach((name) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 text-slate-100">${escapeHtml(name)}</td>
                <td class="px-4 py-3 text-slate-300">${escapeHtml(selectedUwp?.office?.name || '-')}</td>
                <td class="px-4 py-3 text-slate-300">${escapeHtml(indicator?.indicator_text || '-')}</td>
                <td class="px-4 py-3"><span class="inline-flex rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Assigned</span></td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderWorkspaceIndicators() {
        const tbody = document.getElementById('uwp-workspace-indicators-body');
        const overviewList = document.getElementById('uwp-workspace-overview-indicators');
        const output = getSelectedWorkspaceOutput();
        if (!tbody || !overviewList) return;

        tbody.innerHTML = '';
        overviewList.innerHTML = '';

        const indicators = Array.isArray(output?.success_indicators) ? output.success_indicators : [];
        if (!indicators.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-slate-400">No success indicators found for this output.</td></tr>';
            overviewList.innerHTML = '<p class="text-sm text-slate-400">No linked success indicators.</p>';
            return;
        }

        indicators.forEach((indicator, index) => {
            const isSelected = index === selectedWorkspaceIndicatorIndex;
            const row = document.createElement('tr');
            row.className = isSelected ? 'bg-slate-900/40' : 'hover:bg-slate-900/20';
            row.innerHTML = `
                <td class="px-4 py-3 align-top text-slate-100">${escapeHtml(indicator?.indicator_text || '-')}</td>
                <td class="px-4 py-3 align-top text-slate-300">${escapeHtml(getIndicatorTargetSummary(indicator))}</td>
                <td class="px-4 py-3 text-center"><button type="button" data-workspace-indicator-index="${index}" data-target-tab="standards" class="text-blue-300 hover:text-blue-200">View</button></td>
                <td class="px-4 py-3 text-center"><button type="button" data-workspace-indicator-index="${index}" data-target-tab="assignees" class="text-blue-300 hover:text-blue-200">(${Array.isArray(indicator?.assignees) ? indicator.assignees.length : 0})</button></td>
            `;
            tbody.appendChild(row);

            const item = document.createElement('button');
            item.type = 'button';
            item.className = `flex w-full items-start justify-between rounded-xl border px-4 py-3 text-left transition ${isSelected ? 'border-blue-500/30 bg-blue-500/10' : 'border-slate-800 bg-slate-950/50 hover:bg-slate-900/60'}`;
            item.innerHTML = `
                <span class="pr-4 text-sm text-slate-100">${escapeHtml(indicator?.indicator_text || '-')}</span>
            `;
            item.addEventListener('click', () => {
                selectedWorkspaceIndicatorIndex = index;
                setWorkspaceTab('indicators');
                renderWorkspaceDetail();
            });
            overviewList.appendChild(item);
        });

        tbody.querySelectorAll('[data-workspace-indicator-index]').forEach((button) => {
            button.addEventListener('click', () => {
                selectedWorkspaceIndicatorIndex = Number(button.getAttribute('data-workspace-indicator-index') || 0);
                setWorkspaceTab(button.getAttribute('data-target-tab') || 'indicators');
                renderWorkspaceDetail();
            });
        });
    }

    function renderWorkspaceOutputList() {
        const container = document.getElementById('uwp-workspace-output-list');
        const countEl = document.getElementById('uwp-workspace-output-count-inline');
        const countBadge = document.getElementById('uwp-workspace-output-count-badge');
        if (!container || !countEl || !countBadge) return;

        const outputs = getWorkspaceOutputs();
        let filteredOutputs = outputs;
        if (activeWorkspaceFunctionTab !== 'all') {
            filteredOutputs = outputs.filter(o => {
                const ft = String(o.function_type || '').toLowerCase();
                return ft.includes(activeWorkspaceFunctionTab);
            });
        }

        countEl.textContent = `${filteredOutputs.length} output${filteredOutputs.length === 1 ? '' : 's'}`;
        countBadge.textContent = String(filteredOutputs.length);
        container.innerHTML = '';

        if (filteredOutputs.length === 0) {
            container.innerHTML = '<p class="p-4 text-center text-sm text-slate-500">No outputs found.</p>';
            return;
        }

        filteredOutputs.forEach((output) => {
            const index = outputs.indexOf(output);
            const active = index === selectedWorkspaceOutputIndex;
            const button = document.createElement('button');
            button.type = 'button';
            const indicatorCount = Array.isArray(output.success_indicators) ? output.success_indicators.length : 0;
            button.className = `block w-full rounded-xl border px-3 py-3 text-left transition ${active ? 'border-blue-400/60 bg-blue-500/10 shadow-[inset_0_0_0_1px_rgba(96,165,250,0.18)]' : 'border-slate-800 bg-slate-950/30 hover:bg-slate-900/50'}`;
            button.innerHTML = `
                <div class="line-clamp-2 text-base font-semibold leading-snug text-white">${escapeHtml(output.title)}</div>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="text-xs text-slate-500">${indicatorCount} indicator${indicatorCount === 1 ? '' : 's'}</span>
                </div>
            `;
            button.addEventListener('click', () => {
                selectedWorkspaceOutputIndex = index;
                selectedWorkspaceIndicatorIndex = 0;
                renderWorkspaceModal();
            });
            container.appendChild(button);
        });
    }

    function renderWorkspaceDetail() {
        const output = getSelectedWorkspaceOutput();
        const titleEl = document.getElementById('uwp-workspace-detail-title');
        const functionEl = document.getElementById('uwp-workspace-detail-function');
        const functionCopyEl = document.getElementById('uwp-workspace-function-copy');
        const weightEl = document.getElementById('uwp-workspace-detail-weight');
        const weightCopyEl = document.getElementById('uwp-workspace-weight-copy');
        const targetSummaryEl = document.getElementById('uwp-workspace-target-summary');

        if (!titleEl || !functionEl || !functionCopyEl || !weightEl || !weightCopyEl || !targetSummaryEl) return;

        titleEl.textContent = output?.title || 'No output selected';
        const type = String(output?.function_type || '').toLowerCase();
        if (type) {
            functionEl.classList.remove('hidden');
            functionEl.className = `rounded-md border px-2 py-1 text-xs font-medium ${type === 'core' ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : type === 'support' ? 'border-blue-400/30 bg-blue-500/10 text-blue-300' : 'border-slate-500/20 bg-slate-500/10 text-slate-200'}`;
            functionEl.textContent = labelStatus(type);
            functionCopyEl.innerHTML = buildFunctionBadge(type);
        } else {
            functionEl.classList.add('hidden');
            functionCopyEl.textContent = '-';
        }

        const weightText = output && output.weight_percent !== '' ? `${output.weight_percent}%` : '-';
        weightEl.textContent = weightText;
        weightCopyEl.textContent = weightText;
        targetSummaryEl.textContent = output?.target_summary || '-';

        renderWorkspaceIndicators();
        renderWorkspaceStandards();
        renderWorkspaceAssignees();
    }

    function renderWorkspaceModal() {
        renderWorkspaceOutputList();
        renderWorkspaceDetail();
        setWorkspaceTab(activeWorkspaceTab);
    }

    function hydrateWorkspaceReviewModal(uwp) {
        selectedUwp = uwp || null;
        document.getElementById('uwp-workspace-uwp-id').value = selectedUwp?.id || '';

        const officeName = selectedUwp?.office?.name || '-';
        const supervisorName = selectedUwp?.supervisor?.name || '-';
        const periodName = selectedUwp?.period?.name || '-';
        const statusKey = String(selectedUwp?.status || '').toLowerCase();

        document.getElementById('uwp-workspace-office-inline').textContent = officeName;
        document.getElementById('uwp-workspace-supervisor-inline').textContent = supervisorName;
        document.getElementById('uwp-workspace-period-inline').textContent = periodName;
        document.getElementById('uwp-workspace-subtitle').textContent = `${officeName} \u2022 ${periodName}`;

        const statusEl = document.getElementById('uwp-workspace-status-inline');
        statusEl.className = `ml-1 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold ${statusBadgeClass(statusKey)}`;
        statusEl.textContent = labelStatus(statusKey);

        const returnWrap = document.getElementById('uwp-workspace-return-remarks-wrap');
        const returnText = document.getElementById('uwp-workspace-return-remarks-text');
        const returnMeta = document.getElementById('uwp-workspace-return-remarks-meta');
        const returnRemarks = String(selectedUwp?.return_remarks || '').trim();
        const returnedAt = String(selectedUwp?.returned_at || '').trim();
        const returnedByRole = String(selectedUwp?.returned_by_role || '').trim().toLowerCase();

        if (statusKey === 'returned' && returnRemarks !== '') {
            const roleLabel = returnedByRole === 'pmt' ? 'Returned by PMT' : returnedByRole === 'dept-head' ? 'Returned by Department Head' : 'Returned for Revision';
            returnWrap.classList.remove('hidden');
            returnText.textContent = returnRemarks;
            returnMeta.textContent = returnedAt ? `${roleLabel} \u2022 ${returnedAt}` : roleLabel;
        } else {
            returnWrap.classList.add('hidden');
            returnText.textContent = '-';
            returnMeta.textContent = '';
        }

        const canReview = statusKey === 'submitted';
        const btnEndorse = document.getElementById('btn-endorse-uwp-workspace');
        const btnReturn = document.getElementById('btn-return-uwp-workspace');
        [btnEndorse, btnReturn].forEach((button) => {
            if (!button) return;
            button.disabled = !canReview;
            button.classList.toggle('opacity-60', !canReview);
            button.classList.toggle('pointer-events-none', !canReview);
        });

        selectedWorkspaceOutputIndex = 0;
        selectedWorkspaceIndicatorIndex = 0;
        activeWorkspaceTab = 'overview';
        renderWorkspaceModal();
    }

    function openIndicatorsModal(title, unit, mfoTitle, successIndicators) {
        const modal = document.getElementById('uwp-indicators-modal');
        const titleEl = document.getElementById('uwp-indicators-title');
        const tableBody = document.getElementById('uwp-indicators-table-body');
        if (!modal || !titleEl || !tableBody) return;

        titleEl.textContent = title || '--';
        tableBody.innerHTML = '';

        (successIndicators || []).forEach((si) => {
            const indicator = (si?.indicator_text || '').trim();
            if (!indicator) return;

            const assignees = Array.isArray(si.assignees) ? si.assignees : [];
            const count = assignees.length;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/30';

            const indicatorTd = document.createElement('td');
            indicatorTd.className = 'px-5 py-5 text-slate-100';
            indicatorTd.textContent = indicator;

            const targetTd = document.createElement('td');
            targetTd.className = 'px-5 py-5 text-slate-300';
            targetTd.textContent = getIndicatorTargetSummary(si);

            const standardsTd = document.createElement('td');
            standardsTd.className = 'px-5 py-5 text-center';

            const standardsBtn = document.createElement('button');
            standardsBtn.type = 'button';
            standardsBtn.className = 'inline-flex items-center gap-2 text-slate-100/90 hover:text-white transition';
            standardsBtn.innerHTML = `
                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span class="text-sm font-semibold">View Standards</span>
            `;
            standardsBtn.addEventListener('click', () => openStandardsViewer(mfoTitle, indicator));
            standardsTd.appendChild(standardsBtn);

            const assigneesTd = document.createElement('td');
            assigneesTd.className = 'px-5 py-5 text-center';

            const viewBtn = document.createElement('button');
            viewBtn.type = 'button';
            viewBtn.className = 'inline-flex items-center gap-2 text-sm font-semibold text-slate-100 transition hover:text-white';
            viewBtn.innerHTML = `<i class="fa-regular fa-eye text-sm"></i><span>View (${count})</span>`;
            viewBtn.addEventListener('click', () => openAssigneesViewer(unit, mfoTitle, indicator));
            assigneesTd.appendChild(viewBtn);

            tr.append(indicatorTd, targetTd, standardsTd, assigneesTd);
            tableBody.appendChild(tr);
        });

        showModal(modal);
    }

    function hydrateReviewModal(uwp) {
        selectedUwp = uwp || null;

        const idEl = document.getElementById('uwp-modal-uwp-id');
        if (idEl) idEl.value = selectedUwp?.id || '';

        const officeName = selectedUwp?.office?.name || '-';
        const supervisorName = selectedUwp?.supervisor?.name || '-';
        const periodName = selectedUwp?.period?.name || '-';
        const deptHeadName = selectedUwp?.department_head?.name || '-';
        const statusKey = String(selectedUwp?.status || '').toLowerCase();

        const officeEl = document.getElementById('uwp-modal-office');
        if (officeEl) officeEl.textContent = officeName;

        const supEl = document.getElementById('uwp-modal-supervisor');
        if (supEl) supEl.textContent = supervisorName;

        const periodEl = document.getElementById('uwp-modal-period');
        if (periodEl) periodEl.textContent = periodName;

        const subtitleEl = document.getElementById('uwp-modal-subtitle');
        if (subtitleEl) subtitleEl.textContent = `${officeName} \u2022 ${periodName}`;

        const deptHeadEl = document.getElementById('uwp-modal-dept-head');
        if (deptHeadEl) deptHeadEl.textContent = deptHeadName;

        const statusEl = document.getElementById('uwp-modal-status');
        if (statusEl) {
            statusEl.className = `mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold ${statusBadgeClass(statusKey)}`;
            statusEl.textContent = labelStatus(statusKey);
        }

        const returnWrap = document.getElementById('uwp-return-remarks-wrap');
        const returnText = document.getElementById('uwp-return-remarks-text');
        const returnMeta = document.getElementById('uwp-return-remarks-meta');
        const returnRemarks = String(selectedUwp?.return_remarks || '').trim();
        const returnedAt = String(selectedUwp?.returned_at || '').trim();
        const returnedByRole = String(selectedUwp?.returned_by_role || '').trim().toLowerCase();

        if (returnWrap && returnText && returnMeta) {
            if (statusKey === 'returned' && returnRemarks !== '') {
                const roleLabel = returnedByRole === 'pmt'
                    ? 'Returned by PMT'
                    : returnedByRole === 'dept-head'
                        ? 'Returned by Department Head'
                        : 'Returned for Revision';

                returnWrap.classList.remove('hidden');
                returnText.textContent = returnRemarks;
                returnMeta.textContent = returnedAt ? `${roleLabel} \u2022 ${returnedAt}` : roleLabel;
            } else {
                returnWrap.classList.add('hidden');
                returnText.textContent = '-';
                returnMeta.textContent = '';
            }
        }

        const canReview = statusKey === 'submitted';
        const btnEndorse = document.getElementById('btn-endorse-uwp');
        const btnReturn = document.getElementById('btn-return-uwp');
        [btnEndorse, btnReturn].forEach((button) => {
            if (!button) return;
            button.disabled = !canReview;
            button.classList.toggle('opacity-60', !canReview);
            button.classList.toggle('pointer-events-none', !canReview);
        });

        const outputsTbody = document.getElementById('uwp-outputs-tbody');
        if (!outputsTbody) return;

        outputsTbody.innerHTML = '';

        const functions = Array.isArray(selectedUwp?.functions) ? selectedUwp.functions : [];

        functions.forEach((fn) => {
            const mfos = Array.isArray(fn.mfos) ? fn.mfos : [];
            mfos.forEach((mfo) => {
                const indicatorCount = computeIndicatorCount(mfo);

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-800/40 transition';

                const tdTitle = document.createElement('td');
                tdTitle.className = 'px-4 py-3 text-sm text-slate-100';
                tdTitle.textContent = mfo.title || '—';

                const tdIndicators = document.createElement('td');
                tdIndicators.className = 'px-4 py-3 text-sm text-slate-300 text-center';

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'inline-flex items-center gap-2 text-blue-300 hover:text-blue-200';
                btn.innerHTML = `
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span>(${indicatorCount})</span>
                `;
                btn.addEventListener('click', () => {
                    const officeName = selectedUwp?.office?.name || '—';
                    const indicators = Array.isArray(mfo.success_indicators) ? mfo.success_indicators : [];
                    openIndicatorsModal(mfo.title || '--', officeName, mfo.title || '', indicators);
                });

                tdIndicators.appendChild(btn);

                const tdFunction = document.createElement('td');
                tdFunction.className = 'px-4 py-3 text-sm text-center';
                tdFunction.innerHTML = buildFunctionBadge(fn.function_type);

                tr.append(tdTitle, tdIndicators, tdFunction);
                outputsTbody.appendChild(tr);
            });
        });
    }

    function initReviewModalTriggers() {
        if (!document.body.dataset.uwpReviewDelegated) {
            document.addEventListener('click', (event) => {
                const btn = event.target.closest('[data-review-btn][data-uwp]');
                if (!btn) return;

                let uwp = null;
                try {
                    uwp = JSON.parse(btn.getAttribute('data-uwp') || 'null');
                } catch (_) {
                    uwp = null;
                }
                hydrateWorkspaceReviewModal(uwp);
                showModal(document.getElementById('uwp-review-workspace-modal'));
            });
            document.body.dataset.uwpReviewDelegated = 'true';
        }

        const btnEndorse = document.getElementById('btn-endorse-uwp-workspace');
        const btnReturn = document.getElementById('btn-return-uwp-workspace');
        const remarks = document.getElementById('uwp-workspace-remarks');
        const form = document.getElementById('uwp-workspace-review-form');

        btnEndorse?.addEventListener('click', async () => {
            if (btnEndorse.disabled) return;

            const url = String(form.dataset.endorseAction || '').trim();
            if (!url) {
                window.PMSnackbar?.show({
                    type: 'error',
                    message: 'Endorse endpoint is missing.',
                });
                return;
            }
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                              form.querySelector('input[name="_token"]')?.value || '';
            const uwpIdValue = String(selectedUwp?.id || document.getElementById('uwp-workspace-uwp-id')?.value || '').trim();

            setButtonLoading(btnEndorse, true);
            if (btnReturn) {
                btnReturn.disabled = true;
                btnReturn.classList.add('opacity-80', 'cursor-not-allowed');
            }

            try {
                const fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('unit_work_plan_id', uwpIdValue);
                fd.append('action', 'endorse');

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: fd,
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (_) {
                    data = {};
                }

                if (!response.ok || !data?.success) {
                    throw new Error(data?.message || data?.error || 'Unable to endorse UWP');
                }

                const endorsedAt = data?.endorsed_at || new Date().toISOString();
                const endorsedStatus = data?.status || 'consolidated';
                const uwpId = Number(selectedUwp?.id || data?.uwp_id || uwpIdValue || 0);

                if (!selectedUwp) selectedUwp = {};
                selectedUwp.id = selectedUwp.id || uwpId;
                selectedUwp.status = endorsedStatus;
                selectedUwp.endorsed_at = endorsedAt;
                selectedUwp.return_remarks = '';
                selectedUwp.returned_at = null;
                selectedUwp.returned_by_role = null;

                hydrateWorkspaceReviewModal(selectedUwp);
                updateDeptHeadListRow(selectedUwp.id || uwpId, endorsedStatus, {
                    return_remarks: '',
                    returned_at: null,
                    returned_by_role: null,
                });

                const row = document.querySelector(`[data-uwp-row-id="${uwpId}"]`) || document.querySelector(`[data-uwp-row="${uwpId}"]`);
                const reviewBtn = row?.querySelector(`[data-uwp-id="${uwpId}"][data-uwp]`) || row?.querySelector('[data-review-btn][data-uwp]');
                if (reviewBtn) {
                    let nextPayload = {};
                    try {
                        nextPayload = JSON.parse(reviewBtn.getAttribute('data-uwp') || '{}');
                    } catch (_) {
                        nextPayload = {};
                    }
                    nextPayload.status = normalizeStatusKey(endorsedStatus);
                    nextPayload.return_remarks = '';
                    nextPayload.returned_at = null;
                    nextPayload.returned_by_role = null;
                    reviewBtn.setAttribute('data-uwp', JSON.stringify(nextPayload));
                }

                closeReviewModalSafely();
                window.PMSnackbar?.show({
                    type: 'success',
                    message: 'UWP consolidated to OPCR successfully.',
                });
            } catch (error) {
                window.PMSnackbar?.show({
                    type: 'error',
                    message: error?.message || 'Unable to consolidate UWP right now. Please try again.',
                });
            } finally {
                setButtonLoading(btnEndorse, false);
                if (btnReturn) {
                    btnReturn.disabled = false;
                    btnReturn.classList.remove('opacity-80', 'cursor-not-allowed');
                }
                clearFlowbiteBackdrops();
            }
        });


        btnReturn?.addEventListener('click', async () => {
            if (!form) return;
            const remarksValue = (remarks?.value || '').trim();
            if (!remarksValue) {
                remarks?.focus();
                window.PMSnackbar?.show({
                    type: 'error',
                    message: 'Remarks are required before returning this UWP.',
                });
                return;
            }

            if (btnReturn.disabled) return;

            const url = String(form.dataset.returnAction || '').trim();
            if (!url) {
                window.PMSnackbar?.show({
                    type: 'error',
                    message: 'Return endpoint is missing.',
                });
                return;
            }

            setButtonLoading(btnReturn, true);
            if (btnEndorse) {
                btnEndorse.disabled = true;
                btnEndorse.classList.add('opacity-80', 'cursor-not-allowed');
            }

            try {
                const csrfToken =
                    document.querySelector('meta[name="csrf-token"]')?.content ||
                    form.querySelector('input[name="_token"]')?.value ||
                    '';
                const uwpIdValue = String(selectedUwp?.id || document.getElementById('uwp-workspace-uwp-id')?.value || '').trim();

                const fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('unit_work_plan_id', uwpIdValue);
                fd.append('remarks', remarksValue);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: fd,
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (_) {
                    data = {};
                }

                if (!response.ok || !data?.success) {
                    throw new Error(data?.message || data?.error || 'Unable to return UWP');
                }

                const returnedAt = data?.returned_at || new Date().toISOString();
                const remarksText = data?.return_remarks || remarksValue;
                const returnedByRole = data?.returned_by_role || 'dept-head';
                const returnedStatus = data?.status || 'returned';
                const uwpId = Number(selectedUwp?.id || data?.uwp_id || uwpIdValue || 0);

                if (!selectedUwp) selectedUwp = {};
                selectedUwp.id = selectedUwp.id || uwpId;
                selectedUwp.status = returnedStatus;
                selectedUwp.return_remarks = remarksText;
                selectedUwp.returned_at = returnedAt;
                selectedUwp.returned_by_role = returnedByRole;

                hydrateReviewModal(selectedUwp);
                updateDeptHeadListRow(selectedUwp.id || uwpId, returnedStatus, {
                    return_remarks: selectedUwp.return_remarks,
                    returned_at: selectedUwp.returned_at,
                    returned_by_role: selectedUwp.returned_by_role,
                });

                const row = document.querySelector(`[data-uwp-row-id="${uwpId}"]`) || document.querySelector(`[data-uwp-row="${uwpId}"]`);
                const reviewBtn = row?.querySelector(`[data-uwp-id="${uwpId}"][data-uwp]`) || row?.querySelector('[data-review-btn][data-uwp]');
                if (reviewBtn) {
                    let nextPayload = {};
                    try {
                        nextPayload = JSON.parse(reviewBtn.getAttribute('data-uwp') || '{}');
                    } catch (_) {
                        nextPayload = {};
                    }
                    nextPayload.status = normalizeStatusKey(returnedStatus);
                    nextPayload.return_remarks = remarksText;
                    nextPayload.returned_at = returnedAt;
                    nextPayload.returned_by_role = returnedByRole;
                    reviewBtn.setAttribute('data-uwp', JSON.stringify(nextPayload));
                }

                if (remarks) {
                    remarks.value = '';
                }
                closeReviewModalSafely();
                window.PMSnackbar?.show({
                    type: 'success',
                    message: 'UWP returned to supervisor.',
                });
            } catch (error) {
                window.PMSnackbar?.show({
                    type: 'error',
                    message: error?.message || 'Unable to return UWP right now. Please try again.',
                });
            } finally {
                setButtonLoading(btnReturn, false);
                if (btnEndorse) {
                    btnEndorse.disabled = false;
                    btnEndorse.classList.remove('opacity-80', 'cursor-not-allowed');
                }
                clearFlowbiteBackdrops();
            }
        });
    }

    function boot() {
        initModalHandlers();
        initReviewModalTriggers();
        initSignatureModal();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
</script>
@endpush
@endsection
