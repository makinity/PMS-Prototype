@extends('layouts.pmt')

@section('main-content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-100">PMT Review / UWP Final Approval</h1>
    <p class="mt-1 text-sm text-slate-400">Final Stage I review of endorsed Unit Work Plans. Approval sets permanent lock.</p>
</div>

@if (session('success'))
    <div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ session('error') }}</div>
@endif

<div class="mb-6 rounded-2xl border border-slate-800 bg-slate-900/80 p-5">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Performance Period</p>
            <p class="font-medium text-slate-100">{{ $activePeriod->name ?? '—' }}</p>
        </div>
        <form method="GET" action="{{ route('pmt.uwp.index') }}" class="flex items-center gap-3">
            <label for="pmt-status-filter" class="text-xs uppercase tracking-wide text-slate-400">Status</label>
            @php
                $statusFilter = strtolower((string) ($selectedStatus ?? request('status', '')));
            @endphp
            <select id="pmt-status-filter" name="status" onchange="this.form.submit()" style="background:#0f172a;color:#e5e7eb;" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                <option value="" {{ $statusFilter === '' ? 'selected' : '' }}>All Status</option>
                <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="endorsed" {{ $statusFilter === 'endorsed' ? 'selected' : '' }}>Endorsed</option>
                <option value="pmt_approved" {{ $statusFilter === 'pmt_approved' ? 'selected' : '' }}>PMT Approved</option>
                <option value="returned" {{ $statusFilter === 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
        </form>
    </div>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80">
    <div class="border-b border-slate-800 px-5 py-4">
        <h2 class="text-lg font-medium text-white">Unit Work Plans</h2>
        <p class="mt-1 text-sm text-slate-400">Only endorsed UWPs should be approved at this step.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-800/60">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">Office / Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">Supervisor</th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wide text-slate-400">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wide text-slate-400">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($uwps as $uwp)
                    @php
                        $payload = [
                            'id' => $uwp->id,
                            'status' => $uwp->status,
                            'office' => ['id' => $uwp->office?->id, 'name' => $uwp->office?->name],
                            'period' => ['id' => $uwp->performancePeriod?->id, 'name' => $uwp->performancePeriod?->name],
                            'supervisor' => ['id' => $uwp->creator?->id, 'name' => $uwp->creator?->name],
                            'department_head' => ['id' => $uwp->office?->head?->id, 'name' => $uwp->office?->head?->name],
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
                                                foreach ([5, 4, 3, 2, 1] as $r) {
                                                    $standardsByRating[(string) $r] = ['Q' => [], 'E' => [], 'T' => []];
                                                }
                                                foreach ($si->qetStandards as $st) {
                                                    $dim = strtolower((string) $st->dimension);
                                                    $rating = (string) $st->rating;
                                                    if (!isset($standardsByRating[$rating])) continue;
                                                    if (in_array($dim, ['q', 'quality'], true)) $standardsByRating[$rating]['Q'][] = $st->standard_text;
                                                    if (in_array($dim, ['e', 'efficiency'], true)) $standardsByRating[$rating]['E'][] = $st->standard_text;
                                                    if (in_array($dim, ['t', 'timeliness'], true)) $standardsByRating[$rating]['T'][] = $st->standard_text;
                                                }
                                                $assignees = $si->assignments->map(fn ($a) => $a->employee?->name)->filter()->values()->all();
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
                            'draft' => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-200', 'border' => 'border-slate-500/20', 'label' => 'Draft'],
                            'submitted' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-300', 'border' => 'border-blue-500/20', 'label' => 'Submitted'],
                            'returned' => ['bg' => 'bg-rose-500/10', 'text' => 'text-rose-300', 'border' => 'border-rose-500/20', 'label' => 'Returned'],
                            'endorsed' => ['bg' => 'bg-violet-500/10', 'text' => 'text-violet-300', 'border' => 'border-violet-500/20', 'label' => 'Endorsed'],
                            'approved' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/20', 'label' => 'Approved'],
                            'pmt_approved' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/20', 'label' => 'PMT Approved'],
                            default => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-200', 'border' => 'border-slate-500/20', 'label' => ucwords(str_replace('_', ' ', (string) $uwp->status))],
                        };
                    @endphp
                    <tr class="transition hover:bg-slate-800/40" data-uwp-row-id="{{ (int) $uwp->id }}">
                        <td class="px-4 py-3 text-slate-100">{{ $uwp->office?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $uwp->creator?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center"><span data-status-badge class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">{{ $badge['label'] }}</span></td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" data-review-btn data-uwp-id="{{ (int) $uwp->id }}" data-uwp='@json($payload)' class="inline-flex items-center justify-center rounded-lg border border-blue-500 px-3 py-2 text-sm font-medium text-blue-300 transition hover:bg-blue-500/10">Review UWP</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">No Unit Work Plans found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div id="pmt-review-modal" data-modal-container tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-5xl px-6">
        <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-8 py-6">
                <div>
                    <h2 class="text-xl font-semibold">Unit Work Plan</h2>
                    <p id="pmt-modal-subtitle" class="mt-1 text-sm text-slate-400">Select a UWP to view details</p>
                    <span id="pmt-modal-period" class="hidden">—</span>
                </div>
                <button type="button" data-modal-hide="pmt-review-modal" class="inline-flex items-center justify-center rounded-lg border border-slate-800 bg-slate-950/60 px-2.5 py-2 text-slate-400 transition hover:bg-slate-900 hover:text-white">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="flex items-stretch gap-10 border-b border-slate-800 px-8 py-6">
                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Office / Unit</p>
                    <p id="pmt-modal-office" class="mt-1 font-medium">-</p>
                </div>

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Supervisor</p>
                    <p id="pmt-modal-supervisor" class="mt-1 font-medium">-</p>
                </div>

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Department Head</p>
                    <p id="pmt-modal-dept-head" class="mt-1 font-medium">-</p>
                </div>

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Status</p>
                    <span id="pmt-modal-status" class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                        -
                    </span>
                </div>
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
                            <tbody id="pmt-outputs-tbody" class="divide-y divide-slate-800 bg-slate-950"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="px-8 pb-6">
                <form id="pmt-return-form" method="POST" action="{{ route('pmt.uwp.return') }}" class="space-y-2">
                    @csrf
                    <input type="hidden" name="unit_work_plan_id" id="pmt-modal-return-uwp-id" value="">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Review Remarks (required if returning)
                    </label>
                    <textarea name="remarks"
                              id="pmt-modal-remarks"
                              rows="3"
                              style="background:#0f172a;color:#e5e7eb;"
                              class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              placeholder="Required when returning..."></textarea>
                    <p id="pmt-modal-remarks-error" class="hidden text-[11px] text-rose-300">Remarks are required when returning a UWP.</p>
                    <p class="text-[11px] text-slate-500">Note: remarks are required for return.</p>
                </form>
            </div>

            <div class="flex items-center justify-between border-t border-slate-800 px-8 py-5">
                <p class="text-xs text-slate-500">PMT can approve or return endorsed UWPs only.</p>
                <div class="flex gap-3 justify-end">
                    <button type="button" data-modal-hide="pmt-review-modal" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button>
                    <button type="button"
                            id="btn-pmt-return"
                            data-loading-text="Returning..."
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-600 px-4 py-2 text-sm font-semibold text-amber-300 transition hover:bg-amber-600/10">
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        <span data-button-label>Return to Supervisor</span>
                    </button>
                    <form id="pmt-approve-form" method="POST" action="{{ route('pmt.uwp.approve') }}">
                        @csrf
                        <input type="hidden" name="unit_work_plan_id" id="pmt-modal-uwp-id" value="">
                        <button type="submit" id="btn-pmt-approve" data-loading-text="Approving..." class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                            <span data-button-label>Approve UWP</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="pmt-indicators-modal" data-modal-container tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur" data-modal-hide="pmt-indicators-modal"></div>
    <div class="relative z-10 w-full max-w-4xl px-4">
        <div class="rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="pmt-indicators-title" class="text-lg font-semibold text-white">--</h3>
                    <p class="mt-1 text-xs text-slate-400">Read-only indicator details for this MFO.</p>
                </div>
                <button type="button" data-modal-hide="pmt-indicators-modal" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="px-6 py-5">
                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                    <div class="max-h-[380px] overflow-y-auto">
                        <table class="w-full text-sm text-slate-100">
                            <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Success Indicator</th>
                                    <th class="px-4 py-3 text-center">Standards</th>
                                    <th class="px-4 py-3 text-center">Assigned Employee</th>
                                </tr>
                            </thead>
                            <tbody id="pmt-indicators-table-body" class="divide-y divide-slate-800"></tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 flex justify-end"><button type="button" data-modal-hide="pmt-indicators-modal" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button></div>
            </div>
        </div>
    </div>
</div>
<div id="pmt-standards-modal" data-modal-container tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur" data-modal-hide="pmt-standards-modal"></div>
    <div class="relative z-10 w-full max-w-4xl px-4">
        <div class="rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 class="text-lg font-semibold text-white">Target Difficulty / Standards</h3>
                    <p class="mt-1 text-xs text-slate-400">MFO: <span id="pmt-standards-mfo" class="font-semibold text-slate-100">--</span></p>
                    <p class="mt-1 text-xs text-slate-400">Indicator: <span id="pmt-standards-indicator" class="font-semibold text-slate-100">--</span></p>
                </div>
                <button type="button" data-modal-hide="pmt-standards-modal" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="px-6 py-5">
                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                    <table class="w-full text-sm text-slate-100">
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Rating</th>
                                <th class="px-4 py-3 text-left">Quality (Q)</th>
                                <th class="px-4 py-3 text-left">Efficiency (E)</th>
                                <th class="px-4 py-3 text-left">Timeliness (T)</th>
                            </tr>
                        </thead>
                        <tbody id="pmt-standards-table-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end"><button type="button" data-modal-hide="pmt-standards-modal" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button></div>
            </div>
        </div>
    </div>
</div>

<div id="pmt-assignees-modal" data-modal-container tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur" data-modal-hide="pmt-assignees-modal"></div>
    <div class="relative z-10 w-full max-w-3xl px-4">
        <div class="rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employees</p>
                    <h3 class="text-lg font-semibold text-white">Indicator Assignments</h3>
                    <p class="mt-1 text-xs text-slate-400">MFO: <span id="pmt-assignees-mfo" class="font-semibold text-slate-100">--</span></p>
                    <p class="mt-1 text-xs text-slate-400">Indicator: <span id="pmt-assignees-indicator" class="font-semibold text-slate-100">--</span></p>
                </div>
                <button type="button" data-modal-hide="pmt-assignees-modal" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="px-6 py-5">
                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                    <table class="w-full text-sm text-slate-100">
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Employee Name</th>
                                <th class="px-4 py-3 text-left">Office / Unit</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody id="pmt-assignees-table-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end"><button type="button" data-modal-hide="pmt-assignees-modal" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedUwp = null;
    const ratingLevels = [5, 4, 3, 2, 1];

    function escapeHtml(str) {
        return String(str ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
    }
    function normalizeStatusKey(status) {
        return String(status || '').toLowerCase().replaceAll('-', '_');
    }
    function labelStatus(status) {
        const s = normalizeStatusKey(status);
        if (!s) return '—';
        return s.replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase());
    }
    function tableBadgeClass(status) {
        const s = normalizeStatusKey(status);
        return {
            submitted: 'bg-blue-500/10 text-blue-300 border-blue-500/20',
            returned: 'bg-rose-500/10 text-rose-300 border-rose-500/20',
            endorsed: 'bg-violet-500/10 text-violet-300 border-violet-500/20',
            approved: 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
            pmt_approved: 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
        }[s] || 'bg-slate-500/10 text-slate-200 border-slate-500/20';
    }
    function statusBadgeClass(status) {
        const s = normalizeStatusKey(status);
        return {
            draft: 'border-slate-500/30 bg-slate-500/10 text-slate-200',
            submitted: 'border-blue-500/30 bg-blue-500/10 text-blue-300',
            returned: 'border-rose-500/30 bg-rose-500/10 text-rose-300',
            endorsed: 'border-violet-500/30 bg-violet-500/10 text-violet-300',
            approved: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
            pmt_approved: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
        }[s] || 'border-slate-500/30 bg-slate-500/10 text-slate-200';
    }
    function updatePmtListRow(uwpId, newStatus, options = {}) {
        const row = document.querySelector(`[data-uwp-row-id="${uwpId}"]`);
        if (!row) return;

        const normalizedStatus = normalizeStatusKey(newStatus);
        const badge = row.querySelector('[data-status-badge]');
        if (badge) {
            badge.className = 'inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium ' + tableBadgeClass(normalizedStatus);
            badge.textContent = labelStatus(normalizedStatus);
        }

        const reviewButton = row.querySelector(`[data-review-btn][data-uwp-id="${uwpId}"]`);
        if (!reviewButton) return;

        try {
            const payload = JSON.parse(reviewButton.getAttribute('data-uwp') || '{}');
            payload.status = normalizedStatus;

            if (normalizedStatus === 'returned') {
                payload.return_remarks = options.return_remarks ?? payload.return_remarks ?? '';
                payload.returned_at = options.returned_at ?? payload.returned_at ?? new Date().toISOString();
                payload.returned_by_role = options.returned_by_role ?? 'pmt';
            } else if (normalizedStatus === 'approved' || normalizedStatus === 'pmt_approved') {
                payload.return_remarks = '';
                payload.returned_at = null;
                payload.returned_by_role = null;
            }

            reviewButton.setAttribute('data-uwp', JSON.stringify(payload));
        } catch (_) {
            // Ignore invalid payload to keep UI responsive.
        }
    }
    function functionBadge(type) {
        const t = String(type || '').toLowerCase();
        if (t === 'core') return '<span class="inline-flex rounded-md border border-emerald-500/30 bg-emerald-500/10 px-2 py-1 text-xs font-semibold text-emerald-300">Core</span>';
        if (t === 'support') return '<span class="inline-flex rounded-md border border-blue-500/30 bg-blue-500/10 px-2 py-1 text-xs font-semibold text-blue-300">Support</span>';
        return '<span class="inline-flex rounded-md border border-slate-500/30 bg-slate-500/10 px-2 py-1 text-xs font-semibold text-slate-200">' + escapeHtml(labelStatus(type)) + '</span>';
    }
    function setButtonLoading(button, loading, loadingText) {
        if (!button) return;
        const label = button.querySelector('[data-button-label]');
        const spinner = button.querySelector('[data-button-spinner]');

        if (loading) {
            if (label) {
                if (!button.dataset.originalLabel) {
                    button.dataset.originalLabel = label.textContent || '';
                }
                label.textContent = loadingText || 'Processing...';
            }
            if (spinner) spinner.classList.remove('hidden');
            button.disabled = true;
            button.classList.add('opacity-70', 'cursor-wait');
            return;
        }

        if (label && button.dataset.originalLabel) {
            label.textContent = button.dataset.originalLabel;
        }
        if (spinner) spinner.classList.add('hidden');
        button.disabled = false;
        button.classList.remove('opacity-70', 'cursor-wait');
    }
    function cleanupFlowbiteBackdrops() {
        document.querySelectorAll('[modal-backdrop]').forEach((el) => el.remove());
        document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
    }
    function openModalById(id) { const modal = document.getElementById(id); if (!modal) return; modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.classList.add('overflow-hidden'); }
    function closeModalById(id) { const modal = document.getElementById(id); if (!modal) return; modal.classList.add('hidden'); modal.classList.remove('flex'); if (!document.querySelector('[data-modal-container].flex')) document.body.classList.remove('overflow-hidden'); cleanupFlowbiteBackdrops(); }
    function openReviewModal(uwp) {
        selectedUwp = uwp || null;
        const office = selectedUwp?.office?.name || '—';
        const supervisor = selectedUwp?.supervisor?.name || '—';
        const period = selectedUwp?.period?.name || '—';
        const deptHead = selectedUwp?.department_head?.name || '—';
        const status = selectedUwp?.status || '';

        document.getElementById('pmt-modal-office').textContent = office;
        document.getElementById('pmt-modal-supervisor').textContent = supervisor;
        document.getElementById('pmt-modal-period').textContent = period;
        document.getElementById('pmt-modal-dept-head').textContent = deptHead;
        const subtitle = document.getElementById('pmt-modal-subtitle');
        if (subtitle) {
            subtitle.textContent = `${office} • ${period}`;
        }

        const statusEl = document.getElementById('pmt-modal-status');
        statusEl.textContent = labelStatus(status);
        statusEl.className = 'mt-1 inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold ' + statusBadgeClass(status);

        const remarksInput = document.getElementById('pmt-modal-remarks');
        if (remarksInput) remarksInput.value = '';
        const remarksError = document.getElementById('pmt-modal-remarks-error');
        if (remarksError) remarksError.classList.add('hidden');

        const canReview = String(status).toLowerCase() === 'endorsed';
        const approveIdInput = document.getElementById('pmt-modal-uwp-id');
        const returnIdInput = document.getElementById('pmt-modal-return-uwp-id');
        if (approveIdInput) approveIdInput.value = selectedUwp?.id || '';
        if (returnIdInput) returnIdInput.value = selectedUwp?.id || '';

        const returnBtn = document.getElementById('btn-pmt-return');
        const approveBtn = document.getElementById('btn-pmt-approve');
        [returnBtn, approveBtn].forEach((button) => {
            if (!button) return;
            setButtonLoading(button, false);
            button.disabled = !canReview;
            if (!canReview) {
                button.classList.add('opacity-60', 'pointer-events-none');
            } else {
                button.classList.remove('opacity-60', 'pointer-events-none');
            }
        });

        renderOutputsTable(selectedUwp?.functions || []);
        openModalById('pmt-review-modal');
    }

    function renderOutputsTable(functions) {
        const tbody = document.getElementById('pmt-outputs-tbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        (Array.isArray(functions) ? functions : []).forEach((fn) => {
            const mfos = Array.isArray(fn.mfos) ? fn.mfos : [];
            mfos.forEach((mfo) => {
                const indicators = Array.isArray(mfo.success_indicators) ? mfo.success_indicators : [];

                const tr = document.createElement('tr');
                tr.className = 'transition hover:bg-slate-800/40';

                const tdTitle = document.createElement('td');
                tdTitle.className = 'px-4 py-3 text-slate-100';
                tdTitle.textContent = mfo.title || '—';

                const tdIndicators = document.createElement('td');
                tdIndicators.className = 'px-4 py-3 text-center';
                const indicatorsBtn = document.createElement('button');
                indicatorsBtn.type = 'button';
                indicatorsBtn.className = 'inline-flex items-center gap-1.5 text-blue-300 transition hover:text-blue-200';
                indicatorsBtn.innerHTML = '<i class="fa-regular fa-eye text-sm"></i><span>(' + indicators.length + ')</span>';
                indicatorsBtn.addEventListener('click', () => openIndicatorsModal(mfo.title || '--', selectedUwp?.office?.name || '—', indicators));
                tdIndicators.appendChild(indicatorsBtn);

                const tdTimeline = document.createElement('td');
                tdTimeline.className = 'px-4 py-3 text-center text-slate-200';
                tdTimeline.textContent = mfo.target_timeline || '—';

                const tdFn = document.createElement('td');
                tdFn.className = 'px-4 py-3 text-center';
                tdFn.innerHTML = functionBadge(fn.function_type);

                tr.append(tdTitle, tdIndicators, tdTimeline, tdFn);
                tbody.appendChild(tr);
            });
        });
    }

    function openIndicatorsModal(mfoTitle, unitName, indicators) {
        const titleEl = document.getElementById('pmt-indicators-title');
        const tbody = document.getElementById('pmt-indicators-table-body');
        if (!titleEl || !tbody) return;

        titleEl.textContent = mfoTitle || '--';
        tbody.innerHTML = '';

        (Array.isArray(indicators) ? indicators : []).forEach((indicator) => {
            const text = indicator?.indicator_text || '—';
            const assignees = Array.isArray(indicator?.assignees) ? indicator.assignees : [];
            const standardsByRating = indicator?.standards_by_rating || {};

            const tr = document.createElement('tr');
            tr.className = 'transition hover:bg-slate-900/40';

            const tdText = document.createElement('td');
            tdText.className = 'px-4 py-3 text-slate-100';
            tdText.textContent = text;

            const tdStandards = document.createElement('td');
            tdStandards.className = 'px-4 py-3 text-center';
            const standardsBtn = document.createElement('button');
            standardsBtn.type = 'button';
            standardsBtn.className = 'inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-800';
            standardsBtn.innerHTML = '<i class="fa-regular fa-eye text-sm"></i><span>View Standards</span>';
            standardsBtn.addEventListener('click', () => openStandardsModal(mfoTitle, text, standardsByRating));
            tdStandards.appendChild(standardsBtn);

            const tdAssignees = document.createElement('td');
            tdAssignees.className = 'px-4 py-3 text-center';
            const assigneesBtn = document.createElement('button');
            assigneesBtn.type = 'button';
            assigneesBtn.className = 'inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-800';
            assigneesBtn.innerHTML = '<i class="fa-regular fa-eye text-sm"></i><span>View (' + assignees.length + ')</span>';
            assigneesBtn.addEventListener('click', () => openAssigneesModal(mfoTitle, text, unitName, assignees));
            tdAssignees.appendChild(assigneesBtn);

            tr.append(tdText, tdStandards, tdAssignees);
            tbody.appendChild(tr);
        });

        openModalById('pmt-indicators-modal');
    }

    function renderStandardList(items) {
        const values = Array.isArray(items) ? items : [];
        if (!values.length) return '—';
        const ul = document.createElement('ul');
        ul.className = 'list-disc space-y-1 pl-4 text-slate-200';
        values.forEach((item) => { const li = document.createElement('li'); li.textContent = item; ul.appendChild(li); });
        const wrapper = document.createElement('div'); wrapper.appendChild(ul); return wrapper.innerHTML;
    }

    function openStandardsModal(mfoTitle, indicatorText, standardsByRating) {
        document.getElementById('pmt-standards-mfo').textContent = mfoTitle || '--';
        document.getElementById('pmt-standards-indicator').textContent = indicatorText || '--';
        const tbody = document.getElementById('pmt-standards-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        ratingLevels.forEach((level) => {
            const row = standardsByRating?.[String(level)] || { Q: [], E: [], T: [] };
            const tr = document.createElement('tr');
            tr.className = 'transition hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 font-semibold">${level}</td>
                <td class="px-4 py-3 align-top">${renderStandardList(row.Q || row.q || [])}</td>
                <td class="px-4 py-3 align-top">${renderStandardList(row.E || row.e || [])}</td>
                <td class="px-4 py-3 align-top">${renderStandardList(row.T || row.t || [])}</td>
            `;
            tbody.appendChild(tr);
        });

        openModalById('pmt-standards-modal');
    }

    function openAssigneesModal(mfoTitle, indicatorText, unitName, assignees) {
        document.getElementById('pmt-assignees-mfo').textContent = mfoTitle || '--';
        document.getElementById('pmt-assignees-indicator').textContent = indicatorText || '--';
        const tbody = document.getElementById('pmt-assignees-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        const list = Array.isArray(assignees) ? assignees : [];
        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-slate-400">No assigned employees.</td></tr>';
            openModalById('pmt-assignees-modal');
            return;
        }

        list.forEach((name) => {
            const tr = document.createElement('tr');
            tr.className = 'transition hover:bg-slate-900/40';
            tr.innerHTML = `<td class="px-4 py-3 text-slate-100">${escapeHtml(name)}</td><td class="px-4 py-3 text-slate-300">${escapeHtml(unitName || '—')}</td><td class="px-4 py-3"><span class="inline-flex rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Assigned</span></td>`;
            tbody.appendChild(tr);
        });

        openModalById('pmt-assignees-modal');
    }

    function initReviewTriggers() {
        document.querySelectorAll('[data-review-btn][data-uwp]').forEach((button) => {
            button.addEventListener('click', () => {
                let uwp = null;
                try { uwp = JSON.parse(button.getAttribute('data-uwp') || 'null'); } catch (error) { uwp = null; }
                openReviewModal(uwp);
            });
        });
    }

    function initModalHideHandlers() {
        document.querySelectorAll('[data-modal-hide]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-modal-hide');
                if (target) closeModalById(target);
            });
        });
        window.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            document.querySelectorAll('[data-modal-container].flex').forEach((modal) => closeModalById(modal.id));
        });
    }

    function initApproveLoading() {
        const form = document.getElementById('pmt-approve-form');
        const button = document.getElementById('btn-pmt-approve');
        const idInput = document.getElementById('pmt-modal-uwp-id');
        if (!form || !button) return;

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!idInput || !idInput.value) {
                alert('Please select a UWP from the list before approving.');
                return;
            }

            const returnButton = document.getElementById('btn-pmt-return');
            setButtonLoading(button, true, button.dataset.loadingText || 'Approving...');
            if (returnButton) returnButton.disabled = true;

            try {
                const csrfToken =
                    document.querySelector('meta[name="csrf-token"]')?.content ||
                    form.querySelector('input[name="_token"]')?.value ||
                    '';
                const fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('unit_work_plan_id', idInput.value);

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
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
                    throw new Error(data?.message || data?.error || 'Unable to approve UWP.');
                }

                const uwpId = Number(selectedUwp?.id || data?.uwp_id || idInput.value || 0);
                if (!selectedUwp) selectedUwp = {};
                selectedUwp.id = selectedUwp.id || uwpId;
                selectedUwp.status = data?.status || 'pmt_approved';
                selectedUwp.return_remarks = '';
                selectedUwp.returned_at = null;
                selectedUwp.returned_by_role = null;

                updatePmtListRow(uwpId, selectedUwp.status, {
                    return_remarks: '',
                    returned_at: null,
                    returned_by_role: null,
                });

                const remarksInput = document.getElementById('pmt-modal-remarks');
                const remarksError = document.getElementById('pmt-modal-remarks-error');
                if (remarksInput) remarksInput.value = '';
                if (remarksError) remarksError.classList.add('hidden');

                closeModalById('pmt-review-modal');
            } catch (error) {
                alert(error?.message || 'Unable to approve UWP.');
            } finally {
                setButtonLoading(button, false);
                if (returnButton) returnButton.disabled = false;
            }
        });
    }

    function initReturnAction() {
        const form = document.getElementById('pmt-return-form');
        const button = document.getElementById('btn-pmt-return');
        const idInput = document.getElementById('pmt-modal-return-uwp-id');
        const remarksInput = document.getElementById('pmt-modal-remarks');
        const remarksError = document.getElementById('pmt-modal-remarks-error');
        if (!form || !button) return;

        button.addEventListener('click', async () => {
            if (!idInput || !idInput.value) {
                alert('Please select a UWP from the list before returning.');
                return;
            }

            const remarks = (remarksInput?.value || '').trim();
            if (!remarks) {
                if (remarksError) remarksError.classList.remove('hidden');
                remarksInput?.focus();
                return;
            }

            if (remarksError) remarksError.classList.add('hidden');
            setButtonLoading(button, true, button.dataset.loadingText || 'Returning...');

            const approveButton = document.getElementById('btn-pmt-approve');
            if (approveButton) approveButton.disabled = true;

            try {
                const csrfToken =
                    document.querySelector('meta[name="csrf-token"]')?.content ||
                    form.querySelector('input[name="_token"]')?.value ||
                    '';

                const fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('unit_work_plan_id', idInput.value);
                fd.append('remarks', remarks);

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
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
                    throw new Error(data?.message || data?.error || 'Unable to return UWP.');
                }

                const uwpId = Number(selectedUwp?.id || data?.uwp_id || idInput.value || 0);
                const returnedAt = data?.returned_at || new Date().toISOString();
                const returnedRemarks = data?.return_remarks || remarks;

                if (!selectedUwp) selectedUwp = {};
                selectedUwp.id = selectedUwp.id || uwpId;
                selectedUwp.status = data?.status || 'returned';
                selectedUwp.return_remarks = returnedRemarks;
                selectedUwp.returned_at = returnedAt;
                selectedUwp.returned_by_role = data?.returned_by_role || 'pmt';

                updatePmtListRow(uwpId, selectedUwp.status, {
                    return_remarks: selectedUwp.return_remarks,
                    returned_at: selectedUwp.returned_at,
                    returned_by_role: selectedUwp.returned_by_role,
                });

                if (remarksInput) remarksInput.value = '';
                if (remarksError) remarksError.classList.add('hidden');

                closeModalById('pmt-review-modal');
            } catch (error) {
                alert(error?.message || 'Unable to return UWP.');
            } finally {
                setButtonLoading(button, false);
                if (approveButton) approveButton.disabled = false;
            }
        });
    }

    function boot() { initReviewTriggers(); initModalHideHandlers(); initApproveLoading(); initReturnAction(); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
</script>
@endpush
@endsection
