@extends('layouts.dept-head')
{{-- Page Header --}}
@section('main-content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-100">Unit Work Plan Review</h1>
    <p class="text-sm text-slate-400 mt-1">
        Select an office/unit to review its submitted Unit Work Plan. Endorse to PMT or return with remarks.
    </p>
</div>

@if (session('success'))
    <div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
        {{ session('error') }}
    </div>
@endif

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
                <option value="endorsed" {{ $statusFilter === 'endorsed' ? 'selected' : '' }}>Endorsed</option>
                <option value="pmt_approved" {{ $statusFilter === 'pmt_approved' ? 'selected' : '' }}>PMT Approved</option>
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
                                    'mfos' => $fn->mfos->map(function ($mfo) {
                                        return [
                                            'id' => $mfo->id,
                                            'title' => $mfo->title,
                                            'target_timeline' => $mfo->target_timeline,
                                            'weight_percent' => (string) ($mfo->weight_percent ?? ''),
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
                                <th class="px-5 py-4 text-center">Timeline / Target</th>
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
                        Ensure targets, indicators, and weights are correct before endorsing to PMT.
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
                            <span data-button-label>Endorse to PMT</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- MODAL: Success Indicators (3 columns incl. Assigned Employees) --}}
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

@push('scripts')
<script>
    const standardRatings = [5, 4, 3, 2, 1];

    let indicatorStandardsBody;
    let standardsModal;
    let assigneesModal;
    const bodyOverflowClass = 'overflow-hidden';

    // runtime cache from selected UWP payload
    let selectedUwp = null;

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

    function closeReviewModalSafely() {
        const modal = document.getElementById('uwp-review-modal');
        if (modal) {
            modal.classList.add('hidden');
        }

        const ids = ['uwp-indicators-modal', 'uwp-standards-viewer-modal', 'uwp-assignees-viewer-modal'];
        ids.forEach((id) => {
            const m = document.getElementById(id);
            if (!m) return;
            m.classList.add('hidden');
        });

        clearFlowbiteBackdrops();
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
        indicatorStandardsBody = document.getElementById('uwp-standards-table-body');
        standardsModal = document.getElementById('uwp-standards-viewer-modal');
        assigneesModal = document.getElementById('uwp-assignees-viewer-modal');
        const indicatorsModal = document.getElementById('uwp-indicators-modal');
        const reviewModal = document.getElementById('uwp-review-modal');

        if (reviewModal && !reviewModal.classList.contains('flex')) {
            reviewModal.classList.add('flex');
        }

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
                const modalEl = document.getElementById('uwp-review-modal');
                if (modalEl?.classList.contains('hidden')) {
                    cleanupFlowbiteBackdrop();
                }
            }, 0);
        });

        document.querySelectorAll('[data-modal-hide="uwp-indicators-modal"]').forEach((btn) => {
            btn.addEventListener('click', () => closeModal(indicatorsModal));
        });
        document.querySelectorAll('[data-modal-hide="uwp-standards-viewer-modal"]').forEach((btn) => {
            btn.addEventListener('click', () => closeModal(standardsModal));
        });
        document.querySelectorAll('[data-modal-hide="uwp-assignees-viewer-modal"]').forEach((btn) => {
            btn.addEventListener('click', () => closeModal(assigneesModal));
        });

        indicatorsModal?.addEventListener('click', (event) => {
            if (event.target === indicatorsModal) closeModal(indicatorsModal);
        });
        standardsModal?.addEventListener('click', (event) => {
            if (event.target === standardsModal) closeModal(standardsModal);
        });
        assigneesModal?.addEventListener('click', (event) => {
            if (event.target === assigneesModal) closeModal(assigneesModal);
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

            tr.append(indicatorTd, standardsTd, assigneesTd);
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

                const tdTimeline = document.createElement('td');
                tdTimeline.className = 'px-4 py-3 text-sm text-center text-slate-100';
                tdTimeline.textContent = mfo.target_timeline || '—';

                const tdFunction = document.createElement('td');
                tdFunction.className = 'px-4 py-3 text-sm text-center';
                tdFunction.innerHTML = buildFunctionBadge(fn.function_type);

                tr.append(tdTitle, tdIndicators, tdTimeline, tdFunction);
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
                hydrateReviewModal(uwp);
                showModal(document.getElementById('uwp-review-modal'));
            });
            document.body.dataset.uwpReviewDelegated = 'true';
        }

        const btnEndorse = document.getElementById('btn-endorse-uwp');
        const btnReturn = document.getElementById('btn-return-uwp');
        const actionInput = document.getElementById('uwp-modal-action');
        const remarks = document.getElementById('uwp-modal-remarks');
        const form = document.getElementById('uwp-review-form');

        btnEndorse?.addEventListener('click', () => {
            if (!form) return;
            if (form.dataset.endorseAction) {
                form.action = form.dataset.endorseAction;
            }
            if (actionInput) actionInput.value = 'endorse';
            form.submit();
        });

        btnReturn?.addEventListener('click', async () => {
            if (!form) return;
            const remarksValue = (remarks?.value || '').trim();
            if (!remarksValue) {
                remarks?.focus();
                return;
            }

            if (btnReturn.disabled) return;

            const url = String(form.dataset.returnAction || '').trim();
            if (!url) {
                alert('Return endpoint is missing.');
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
                const uwpIdValue = String(selectedUwp?.id || document.getElementById('uwp-modal-uwp-id')?.value || '').trim();

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
            } catch (error) {
                alert(error?.message || 'Unable to return UWP right now. Please try again.');
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
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
</script>
@endpush
@endsection
