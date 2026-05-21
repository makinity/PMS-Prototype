
@extends('layouts.pmt')

@section('main-content')
<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">PMT Final OPCR Approval</h1>
            <p class="text-sm text-slate-400">Stage I - Performance Planning and Commitment</p>
            <p class="text-xs text-slate-500">Review Department Head-endorsed OPCRs and issue final approval to proceed to Stage II.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
            <form method="GET" action="{{ route('pmt.opcr.review.index') }}" id="pmt-opcr-search-form" class="flex flex-wrap items-end justify-start gap-2">
                <input type="hidden" name="status" value="{{ $selectedStatus }}">
                <div class="min-w-[260px]">
                    <label for="pmt-opcr-search" class="mb-1 block text-xs uppercase tracking-wide text-slate-500">Search</label>
                    <div class="flex items-center gap-2">
                        <input
                            id="pmt-opcr-search"
                            type="text"
                            name="search"
                            value="{{ $searchTerm ?? '' }}"
                            placeholder="Search office, period, OPCR ID..."
                            autocomplete="off"
                            style="background:#0f172a;color:#e5e7eb;"
                            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                            data-live-opcr-search>
                        <button
                            type="submit"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-700 bg-slate-950 text-slate-200 transition hover:bg-slate-800"
                            aria-label="Search OPCR records">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </button>
                    </div>
                </div>
                @if (($searchTerm ?? '') !== '')
                    <a href="{{ route('pmt.opcr.review.index', $selectedStatus !== '' ? ['status' => $selectedStatus] : []) }}"
                       class="rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-800">
                        Clear
                    </a>
                @endif
            </form>

            <form method="GET" action="{{ route('pmt.opcr.review.index') }}" class="flex items-end justify-end gap-2">
                <input type="hidden" name="search" value="{{ $searchTerm ?? '' }}">
                <div>
                    <label for="opcr-status" class="mb-1 block text-xs uppercase tracking-wide text-slate-500">Status</label>
                    <select id="opcr-status"
                            name="status"
                            onchange="this.form.submit()"
                            style="background:#0f172a;color:#e5e7eb;"
                            class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                        <option value="" {{ $selectedStatus === '' ? 'selected' : '' }}>All Status</option>
                        <option value="submitted" {{ $selectedStatus === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="endorsed" {{ $selectedStatus === 'endorsed' ? 'selected' : '' }}>Endorsed</option>
                        <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="returned" {{ $selectedStatus === 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-300">
                <thead class="text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-2 text-left">Office / Unit</th>
                        <th class="px-4 py-2 text-left">Period</th>
                        <th class="px-4 py-2 text-left">Referenced UWP</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($opcrs as $opcr)
                        @php
                            $payload = $opcrPayloads[$opcr->id] ?? null;
                            $isReviewable = in_array(strtolower((string) $opcr->status), ['endorsed', 'for_pmt_review'], true);
                            $statusMeta = match (strtolower((string) $opcr->status)) {
                                'endorsed', 'for_pmt_review' => ['label' => 'Endorsed', 'class' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300'],
                                'approved' => ['label' => 'Approved', 'class' => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300'],
                                'returned' => ['label' => 'Returned', 'class' => 'border-rose-500/30 bg-rose-500/10 text-rose-300'],
                                default => ['label' => 'Submitted', 'class' => 'border-amber-500/30 bg-amber-500/20 text-amber-300'],
                            };
                            $officeName = $opcr->office?->name ?? $opcr->unitWorkPlan?->office?->name ?? '-';
                            $periodName = $opcr->performancePeriod?->name ?? $opcr->unitWorkPlan?->performancePeriod?->name ?? '-';
                            $searchHaystack = strtolower(implode(' ', array_filter([
                                $officeName,
                                $periodName,
                                $statusMeta['label'],
                                'OPCR ' . $opcr->id,
                            ])));
                        @endphp
                        <tr class="border-t border-slate-800" data-opcr-row data-search-text="{{ $searchHaystack }}">
                            <td class="px-4 py-3 text-white">{{ $officeName }}</td>
                            <td class="px-4 py-3">{{ $periodName }}</td>
                            <td class="px-4 py-3">{{ $opcr->unitWorkPlans->count() ?: ($opcr->unitWorkPlan ? 1 : 0) }} UWP source(s)</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-open-review-opcr
                                        data-opcr='@json($payload)'
                                        class="text-blue-400 hover:text-blue-300 {{ $payload ? '' : 'opacity-60 pointer-events-none' }}"
                                        {{ $payload ? '' : 'disabled' }}>
                                    {{ $isReviewable ? 'Review' : 'View' }}
                                </button>
                                @unless ($isReviewable)
                                    <span class="ml-2 inline-flex items-center rounded-full border border-slate-600/60 bg-slate-700/30 px-2 py-0.5 text-[10px] font-semibold text-slate-300">Read-only</span>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr id="pmt-opcr-empty-row">
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No OPCR records found for PMT review.</td>
                        </tr>
                    @endforelse
                    @if($opcrs->isNotEmpty())
                        <tr id="pmt-opcr-no-match-row" class="hidden">
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No matching OPCR records found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div id="review-opcr-modal" data-modal-container class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Office Performance Commitment and Review</p>
                    <h2 id="dh-opcr-modal-title" class="mt-1 truncate text-lg font-semibold text-white">PMT Review OPCR -</h2>
                    <p class="text-sm text-slate-400">Derived from consolidated Unit Work Plans (Stage I)</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full border border-cyan-500/30 bg-cyan-500/10 px-2.5 py-1 text-[11px] font-semibold text-cyan-300">Final Approval</span>
                        <span id="dh-opcr-modal-status" class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold">-</span>
                    </div>
                </div>
                <button type="button" data-close-modal class="shrink-0 rounded-lg border border-slate-800 bg-slate-950/50 px-2.5 py-2 text-slate-400 hover:bg-slate-950 hover:text-white">&times;</button>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Office / Unit</p>
                    <p id="dh-opcr-modal-office" class="mt-1 text-sm font-semibold text-white">-</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Period</p>
                    <p id="dh-opcr-modal-period" class="mt-1 text-sm font-semibold text-white">-</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Referenced UWP</p>
                    <p id="dh-opcr-modal-uwp" class="mt-1 text-sm font-semibold text-white">-</p>
                </div>
            </div>

            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/40 overflow-hidden">
                <div class="max-h-[46vh] overflow-auto">
                    <table class="w-full text-sm text-slate-200">
                        <thead class="sticky top-0 z-10 bg-slate-950 text-xs uppercase text-slate-300">
                            <tr class="border-b border-slate-800">
                                <th class="w-[30%] px-4 py-3 text-left">Output</th>
                                <th class="w-[12%] px-4 py-3 text-center">Success Indicators</th>
                                <th class="w-[26%] px-4 py-3 text-left">Target Summary</th>
                                <th class="w-[8%] px-4 py-3 text-left">Weight</th>
                                <th class="w-[12%] px-4 py-3 text-left">Function</th>
                            </tr>
                        </thead>
                        <tbody id="dh-opcr-outputs-tbody" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>
            <form id="dh-opcr-review-form" method="POST" action="{{ route('pmt.opcr.review.action') }}" class="mt-5">
                @csrf
                <input type="hidden" name="opcr_id" id="dh-opcr-id">
                <input type="hidden" name="action" id="dh-opcr-action">
                <input type="hidden" name="signature" id="dh-opcr-signature">

                <div>
                    <label for="dh-opcr-remarks" class="mb-1 block text-sm text-slate-300">Remarks (required when returning)</label>
                    <textarea id="dh-opcr-remarks"
                              name="remarks"
                              rows="3"
                              style="background:#0f172a;color:#e5e7eb;"
                              class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"></textarea>
                    <p id="dh-opcr-remarks-error" class="mt-2 hidden text-[11px] text-rose-300">Remarks are required when returning the OPCR.</p>
                    <p class="mt-2 text-[11px] text-slate-500">Verify targets, weights, and indicator standards align with the consolidated UWPs.</p>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-800 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[11px] text-slate-500">Approve to finalize OPCR and proceed to Stage II; return sends all included UWPs back for supervisor correction.</p>
                    <div class="flex flex-wrap justify-end gap-3">
                        <button type="button" data-close-modal class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button>

                        <button type="button"
                                data-review-action="return"
                                data-loading-text="Returning..."
                                class="inline-flex items-center gap-2 rounded-lg border border-amber-600 px-4 py-2 text-sm font-semibold text-amber-300 hover:bg-amber-600/10">
                            <span data-button-label>Return</span>
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>

                        <button type="button"
                                data-review-action="approve"
                                data-loading-text="Approving..."
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                            <span data-button-label>Approve OPCR</span>
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="review-opcr-workspace-modal" data-modal-container class="fixed inset-0 z-[85] hidden items-start justify-center overflow-y-auto bg-black/70 px-4 py-4 backdrop-blur-sm sm:py-8">
        <div class="w-full max-w-[1280px]">
            <div class="flex h-[820px] max-h-[calc(100vh-2rem)] flex-col overflow-hidden rounded-[24px] border border-slate-800 bg-slate-900 text-slate-100 shadow-2xl sm:max-h-[calc(100vh-4rem)]">
                <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-5">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 id="dh-opcr-workspace-title" class="text-lg font-semibold text-white">PMT OPCR Review</h2>
                            <span class="rounded-full border border-indigo-500/20 bg-indigo-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-200">Stage I - Planning</span>
                        </div>
                        <div class="hidden">
                            <p id="dh-opcr-workspace-subtitle" class="mt-2 text-sm text-slate-400">Review Department Head-endorsed OPCRs and issue final approval.</p>
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-400">
                                <span id="dh-opcr-workspace-office-inline">-</span>
                                <span class="text-slate-600">•</span>
                                <span id="dh-opcr-workspace-period-inline">-</span>
                                <span id="dh-opcr-workspace-status" class="ml-1 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold">-</span>
                                <span id="dh-opcr-workspace-source-count" class="inline-flex rounded-full bg-slate-800 px-2.5 py-1 text-xs font-semibold text-slate-200">0 source UWPs</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" data-close-workspace-modal class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-700 bg-slate-950/60 text-slate-400 transition hover:bg-slate-900 hover:text-white">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="grid min-h-0 flex-1 lg:grid-cols-[320px_minmax(0,1fr)]">
                    <aside class="flex min-h-0 flex-col border-b border-slate-800 lg:border-b-0 lg:border-r">
                        <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Consolidated Outputs</p>
                            <span id="dh-opcr-workspace-output-count" class="text-sm font-semibold text-blue-300">0</span>
                        </div>
                        <div class="flex border-b border-slate-800 px-2 pt-2">
                            <button type="button" data-workspace-function-tab="all" class="flex-1 border-b-2 border-blue-400 pb-2 text-xs font-semibold text-white transition">All</button>
                            <button type="button" data-workspace-function-tab="core" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 transition hover:text-slate-300">Core</button>
                            <button type="button" data-workspace-function-tab="support" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 transition hover:text-slate-300">Support</button>
                        </div>
                        <div id="dh-opcr-workspace-output-list" class="min-h-0 space-y-2 overflow-y-auto px-2 py-2"></div>
                        <div class="hidden border-t border-slate-800 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Source UWP List</p>
                            <div id="dh-opcr-workspace-source-list" class="mt-3 space-y-2 text-sm text-slate-300"></div>
                        </div>
                    </aside>

                    <section class="flex min-h-0 flex-col">
                        <div class="border-b border-slate-800 px-6 py-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 id="dh-opcr-workspace-detail-title" class="text-lg font-semibold leading-tight text-white">No output selected</h3>
                                <span id="dh-opcr-workspace-detail-function" class="hidden rounded-md border px-2 py-1 text-xs font-medium"></span>
                                <span id="dh-opcr-workspace-detail-weight" class="hidden text-sm font-semibold text-slate-300"></span>
                            </div>
                        </div>

                        <div class="border-b border-slate-800 px-5">
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" data-opcr-workspace-tab="overview" class="border-b-2 border-blue-400 px-2.5 py-2.5 text-sm font-semibold text-white">Overview</button>
                                <button type="button" data-opcr-workspace-tab="indicators" class="border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Success Indicators</button>
                            </div>
                        </div>

                        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-4">
                            <div data-opcr-workspace-panel="overview" class="space-y-5">
                                <div class="hidden">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Target Summary</p>
                                    <p id="dh-opcr-workspace-target-summary" class="mt-2 text-lg leading-snug text-white">-</p>
                                </div>
                                <div class="hidden grid gap-5 sm:grid-cols-2">
                                    <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Function Type</p><div id="dh-opcr-workspace-function-copy" class="mt-2"></div></div>
                                    <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Weight</p><p id="dh-opcr-workspace-weight-copy" class="mt-2 text-lg font-semibold text-white">-</p></div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Linked Success Indicators</p>
                                    <div id="dh-opcr-workspace-overview-indicators" class="mt-3 space-y-2.5"></div>
                                </div>
                            </div>

                            <div data-opcr-workspace-panel="indicators" class="hidden">
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
                                        <tbody id="dh-opcr-workspace-indicators-body" class="divide-y divide-slate-800"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div data-opcr-workspace-panel="standards" class="hidden space-y-4">
                                <div class="hidden"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Indicator</p><p id="dh-opcr-workspace-standards-indicator" class="mt-1.5 text-base font-semibold text-white">-</p></div>
                                <div class="overflow-hidden rounded-xl border border-slate-800">
                                    <table class="min-w-full text-sm text-slate-100">
                                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                                            <tr><th class="px-4 py-3 text-left">Rating</th><th class="px-4 py-3 text-left">Quality (Q)</th><th class="px-4 py-3 text-left">Efficiency (E)</th><th class="px-4 py-3 text-left">Timeliness (T)</th></tr>
                                        </thead>
                                        <tbody id="dh-opcr-workspace-standards-body" class="divide-y divide-slate-800"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div data-opcr-workspace-panel="assignees" class="hidden space-y-4">
                                <div class="hidden"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Indicator</p><p id="dh-opcr-workspace-assignees-indicator" class="mt-1.5 text-base font-semibold text-white">-</p></div>
                                <div class="overflow-hidden rounded-xl border border-slate-800">
                                    <table class="w-full text-sm text-slate-100">
                                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                                            <tr>
                                                <th class="px-4 py-3 text-left">Employee Name</th>
                                                <th class="px-4 py-3 text-left">Office / Unit</th>
                                                <th class="px-4 py-3 text-left">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dh-opcr-workspace-assignees-body" class="divide-y divide-slate-800"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <form id="dh-opcr-workspace-review-form" method="POST" action="{{ route('pmt.opcr.review.action') }}" class="grid shrink-0 gap-3 border-t border-slate-800 px-6 py-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                    @csrf
                    <input type="hidden" name="opcr_id" id="dh-opcr-workspace-id">
                    <input type="hidden" name="action" id="dh-opcr-workspace-action">
                    <input type="hidden" name="signature" id="dh-opcr-workspace-signature">
                    <div>
                        <label for="dh-opcr-workspace-remarks" class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Return Remarks</label>
                        <div class="mt-2 flex flex-col gap-2 lg:flex-row lg:items-center">
                            <textarea id="dh-opcr-workspace-remarks" name="remarks" rows="2" class="min-h-[44px] flex-1 rounded-xl border border-slate-700 bg-slate-950/80 px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Required when returning to Department Head..."></textarea>
                            <p id="dh-opcr-workspace-remarks-error" class="hidden text-[11px] leading-relaxed text-rose-300 lg:w-40">Remarks are required when returning the OPCR.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-end gap-2.5">
                        <button type="button" data-close-workspace-modal class="rounded-xl border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button>
                        <button type="button" data-workspace-review-action="return" data-loading-text="Returning..." class="inline-flex items-center gap-2 rounded-xl border border-amber-600 px-4 py-2 text-sm font-semibold text-amber-300 hover:bg-amber-600/10">
                            <span data-button-label>Return to Dept. Head</span>
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>
                        <button type="button" data-workspace-review-action="approve" data-loading-text="Approving..." class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                            <span data-button-label>Approve OPCR</span>
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="dh-indicators-modal" data-modal-container class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-4">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="dh-indicators-title" class="mt-1 truncate text-xl font-semibold text-white">--</h3>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/50 overflow-hidden">
                <div class="max-h-[55vh] overflow-auto">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-900/70 text-xs uppercase text-slate-300">
                            <tr class="border-b border-slate-800">
                                <th class="w-[56%] px-4 py-3 text-left">Success Indicator</th>
                                <th class="w-[24%] px-4 py-3 text-left">Target Summary</th>
                                <th class="w-[20%] px-4 py-3 text-left">Standards</th>
                                <th class="w-[24%] px-4 py-3 text-left">Assigned Employee</th>
                            </tr>
                        </thead>
                        <tbody id="dh-indicators-table-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-5 py-2 text-sm text-slate-200 hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    <div id="dh-standards-modal" data-modal-container class="fixed inset-0 z-[91] hidden items-center justify-center bg-black/70 px-4 py-8">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 id="dh-standards-title" class="text-lg font-semibold text-white">Target Difficulty / Standards</h3>
                    <p class="mt-1 text-[11px] text-slate-400">Indicator: <span id="dh-standards-indicator" class="font-semibold text-slate-200">--</span></p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                <table class="w-full text-sm text-slate-100">
                    <thead class="bg-slate-900/70 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-3 py-2 text-left">Rating</th>
                            <th class="px-3 py-2 text-left">Quality (Q)</th>
                            <th class="px-3 py-2 text-left">Efficiency (E)</th>
                            <th class="px-3 py-2 text-left">Timeliness (T)</th>
                        </tr>
                    </thead>
                    <tbody id="dh-standards-table-body" class="divide-y divide-slate-800"></tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end border-t border-slate-800 pt-3">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    <div id="dh-indicator-assignee-modal" data-modal-container class="fixed inset-0 z-[92] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employees</p>
                    <h3 id="dh-assignee-title" class="text-lg font-semibold text-white">--</h3>
                    <p class="mt-1 text-[11px] text-slate-400">Indicator: <span id="dh-assignee-indicator" class="font-semibold text-slate-200">--</span></p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                <table class="w-full text-sm text-slate-100">
                    <thead class="bg-slate-900/70 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee Name</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody id="dh-assignee-body" class="divide-y divide-slate-800"></tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>
</section>

@include('partials.signature-pad-modal', [
    'modalId' => 'pmt-signature-modal',
    'title' => 'Approve OPCR (Final)',
    'message' => 'Your e-signature will be applied to the "Assessed by" block of the final OPCR Excel document.',
    'confirmText' => 'Sign & Approve'
])

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ratingLevels = [5, 4, 3, 2, 1];
    let selectedOpcr = null;
    let selectedWorkspaceOutputIndex = 0;
    let selectedWorkspaceIndicatorIndex = 0;
    let activeWorkspaceTab = 'overview';
    let activeWorkspaceFunctionTab = 'all';
    const liveSearchInput = document.querySelector('[data-live-opcr-search]');
    const opcrRows = Array.from(document.querySelectorAll('[data-opcr-row]'));
    const noMatchRow = document.getElementById('pmt-opcr-no-match-row');
    let liveSearchTimer = null;

    const applyLiveOpcrFilter = () => {
        if (!liveSearchInput || !opcrRows.length) return;

        const needle = liveSearchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        opcrRows.forEach((row) => {
            const haystack = String(row.getAttribute('data-search-text') || '').toLowerCase();
            const visible = needle === '' || haystack.includes(needle);
            row.classList.toggle('hidden', !visible);
            if (visible) {
                visibleCount += 1;
            }
        });

        if (noMatchRow) {
            noMatchRow.classList.toggle('hidden', visibleCount > 0);
        }
    };

    liveSearchInput?.addEventListener('input', () => {
        window.clearTimeout(liveSearchTimer);
        liveSearchTimer = window.setTimeout(applyLiveOpcrFilter, 120);
    });

    applyLiveOpcrFilter();

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const formatTargetSummaryDisplay = (targetQuantity, targetSummary) => {
        const quantity = targetQuantity === null || targetQuantity === undefined || targetQuantity === ''
            ? ''
            : String(targetQuantity).trim();
        const summary = targetSummary === null || targetSummary === undefined || targetSummary === ''
            ? ''
            : String(targetSummary).trim();

        if (summary.toLowerCase() === 'multiple indicator targets') {
            return summary;
        }

        if (quantity !== '' && summary !== '') {
            return `${quantity} ${summary}`.trim();
        }

        if (quantity !== '') {
            return quantity;
        }

        if (summary !== '') {
            return summary;
        }

        return '-';
    };

    const getIndicatorTargetSummary = (indicator) => formatTargetSummaryDisplay(
        indicator?.target_quantity,
        indicator?.target_timeline
    );

    const parseJson = (raw) => {
        try { return JSON.parse(raw); } catch (e) { return null; }
    };

    const openModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        const anyOpen = Array.from(document.querySelectorAll('[data-modal-container]'))
            .some((node) => !node.classList.contains('hidden'));

        if (!anyOpen) {
            document.body.classList.remove('overflow-hidden');
        }
    };

    const opcrStatusMeta = (status) => {
        const key = String(status || '').toLowerCase();
        if (key === 'endorsed' || key === 'for_pmt_review') return { label: 'For PMT Review', cls: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300' };
        if (key === 'approved') return { label: 'Final Approved', cls: 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300' };
        if (key === 'returned') return { label: 'Returned', cls: 'border-rose-500/30 bg-rose-500/10 text-rose-300' };
        return { label: 'Submitted', cls: 'border-amber-500/30 bg-amber-500/20 text-amber-300' };
    };

    const functionBadge = (functionType) => {
        const type = String(functionType || '').toLowerCase();
        if (type === 'core') return '<span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">Core</span>';
        if (type === 'support') return '<span class="rounded-md bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-300 border border-blue-400/30">Support</span>';
        return '<span class="rounded-md bg-slate-500/10 px-2 py-1 text-xs font-medium text-slate-300 border border-slate-500/20">' + escapeHtml(type || 'Custom') + '</span>';
    };

    const setButtonLoading = (button, loading, loadingText) => {
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
    };

    const renderOutputs = (outputs) => {
        const tbody = document.getElementById('dh-opcr-outputs-tbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        (Array.isArray(outputs) ? outputs : []).forEach((output) => {
            const indicators = Array.isArray(output.success_indicators) ? output.success_indicators : [];
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 align-top text-white">${escapeHtml(output.title || '-')}</td>
                <td class="px-4 py-3 align-top text-center">
                    <button type="button" class="inline-flex items-center justify-center gap-2 text-blue-300 hover:text-blue-200" data-indicators-btn>
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span class="text-xs">(${indicators.length})</span>
                    </button>
                </td>
                <td class="px-4 py-3 align-top text-slate-200">${escapeHtml(formatTargetSummaryDisplay(output.target_quantity, output.target_summary))}</td>
                <td class="px-4 py-3 align-top text-slate-200">${output.weight_percent !== null && output.weight_percent !== undefined && output.weight_percent !== '' ? escapeHtml(String(output.weight_percent) + '%') : '-'}</td>
                <td class="px-4 py-3 align-top">${functionBadge(output.function_type)}</td>
            `;

            tr.querySelector('[data-indicators-btn]')?.addEventListener('click', () => {
                openIndicatorsModal(output.title || '--', indicators);
            });

            tbody.appendChild(tr);
        });

        if (!tbody.children.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No OPCR outputs found.</td></tr>';
        }
    };

    const getWorkspaceOutputs = () => Array.isArray(selectedOpcr?.outputs) ? selectedOpcr.outputs : [];

    const getSelectedWorkspaceOutput = () => {
        const outputs = getWorkspaceOutputs();
        if (!outputs.length) return null;
        selectedWorkspaceOutputIndex = Math.min(Math.max(selectedWorkspaceOutputIndex, 0), outputs.length - 1);
        return outputs[selectedWorkspaceOutputIndex] || null;
    };

    const getSelectedWorkspaceIndicator = () => {
        const output = getSelectedWorkspaceOutput();
        const indicators = Array.isArray(output?.success_indicators) ? output.success_indicators : [];
        if (!indicators.length) return null;
        selectedWorkspaceIndicatorIndex = Math.min(Math.max(selectedWorkspaceIndicatorIndex, 0), indicators.length - 1);
        return indicators[selectedWorkspaceIndicatorIndex] || null;
    };

    const setWorkspaceTab = (tabName) => {
        activeWorkspaceTab = tabName || 'overview';
        document.querySelectorAll('[data-opcr-workspace-tab]').forEach((button) => {
            const active = button.getAttribute('data-opcr-workspace-tab') === activeWorkspaceTab;
            button.classList.toggle('border-blue-400', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('font-semibold', active);
            button.classList.toggle('border-transparent', !active);
            button.classList.toggle('text-slate-400', !active);
            button.classList.toggle('font-medium', !active);
        });

        document.querySelectorAll('[data-opcr-workspace-panel]').forEach((panel) => {
            panel.classList.toggle('hidden', panel.getAttribute('data-opcr-workspace-panel') !== activeWorkspaceTab);
        });
    };

    const setWorkspaceFunctionTab = (tabName) => {
        activeWorkspaceFunctionTab = tabName || 'all';
        document.querySelectorAll('[data-workspace-function-tab]').forEach((button) => {
            const active = button.getAttribute('data-workspace-function-tab') === activeWorkspaceFunctionTab;
            button.classList.toggle('border-blue-400', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('border-transparent', !active);
            button.classList.toggle('text-slate-400', !active);
        });
        renderWorkspaceOutputList();
    };

    const renderWorkspaceStandards = () => {
        const indicator = getSelectedWorkspaceIndicator();
        const tbody = document.getElementById('dh-opcr-workspace-standards-body');
        const label = document.getElementById('dh-opcr-workspace-standards-indicator');
        if (!tbody || !label) return;

        label.textContent = indicator?.indicator_text || 'No success indicator selected';
        tbody.innerHTML = '';

        const standardsByRating = indicator?.standards_by_rating || {};
        ratingLevels.forEach((rating) => {
            const row = standardsByRating?.[String(rating)] || standardsByRating?.[rating] || { Q: [], E: [], T: [] };
            const q = row.Q ?? row.q ?? [];
            const e = row.E ?? row.e ?? [];
            const t = row.T ?? row.t ?? [];
            const renderCell = (items) => {
                const values = Array.isArray(items) ? items : [];
                if (!values.length) return '<span class="text-slate-400">-</span>';
                return '<ul class="list-disc space-y-1 pl-4">' + values.map((item) => '<li>' + escapeHtml(item) + '</li>').join('') + '</ul>';
            };
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 font-semibold text-white">${rating}</td>
                <td class="px-4 py-3 align-top">${renderCell(q)}</td>
                <td class="px-4 py-3 align-top">${renderCell(e)}</td>
                <td class="px-4 py-3 align-top">${renderCell(t)}</td>
            `;
            tbody.appendChild(tr);
        });
    };

    const renderWorkspaceAssignees = () => {
        const indicator = getSelectedWorkspaceIndicator();
        const tbody = document.getElementById('dh-opcr-workspace-assignees-body');
        const label = document.getElementById('dh-opcr-workspace-assignees-indicator');
        if (!tbody || !label) return;

        label.textContent = indicator?.indicator_text || 'No success indicator selected';
        tbody.innerHTML = '';

        const unitName = selectedOpcr?.opcr?.office?.name || '-';
        const assignees = Array.isArray(indicator?.assignees) ? indicator.assignees : [];
        if (!assignees.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-slate-400">No assigned employees.</td></tr>';
            return;
        }

        assignees.forEach((name) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 text-slate-100">${escapeHtml(name)}</td>
                <td class="px-4 py-3 text-slate-300">${escapeHtml(unitName)}</td>
                <td class="px-4 py-3"><span class="inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2 py-1 text-[11px] font-semibold text-emerald-200">Assigned</span></td>
            `;
            tbody.appendChild(tr);
        });
    };

    const renderWorkspaceIndicators = () => {
        const tbody = document.getElementById('dh-opcr-workspace-indicators-body');
        const overview = document.getElementById('dh-opcr-workspace-overview-indicators');
        const output = getSelectedWorkspaceOutput();
        if (!tbody || !overview) return;

        tbody.innerHTML = '';
        overview.innerHTML = '';

        const indicators = Array.isArray(output?.success_indicators) ? output.success_indicators : [];
        if (!indicators.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-slate-400">No indicators found.</td></tr>';
            overview.innerHTML = '<p class="text-sm text-slate-400">No linked success indicators.</p>';
            return;
        }

        indicators.forEach((indicator, index) => {
            const assignees = Array.isArray(indicator?.assignees) ? indicator.assignees : [];
            const tr = document.createElement('tr');
            tr.className = index === selectedWorkspaceIndicatorIndex ? 'bg-slate-900/40' : 'hover:bg-slate-900/20';
            tr.innerHTML = `
                <td class="px-4 py-3 align-top text-slate-100">${escapeHtml(indicator?.indicator_text || '-')}</td>
                <td class="px-4 py-3 align-top text-slate-300">${escapeHtml(getIndicatorTargetSummary(indicator))}</td>
                <td class="px-4 py-3 text-center"><button type="button" data-workspace-indicator-index="${index}" data-target-tab="standards" class="text-blue-300 hover:text-blue-200">View</button></td>
                <td class="px-4 py-3 text-center"><button type="button" data-workspace-indicator-index="${index}" data-target-tab="assignees" class="text-blue-300 hover:text-blue-200">(${assignees.length})</button></td>
            `;
            tbody.appendChild(tr);

            const item = document.createElement('button');
            item.type = 'button';
            item.className = `flex w-full items-start justify-between rounded-xl border px-4 py-3 text-left transition ${index === selectedWorkspaceIndicatorIndex ? 'border-blue-500/30 bg-blue-500/10' : 'border-slate-800 bg-slate-950/50 hover:bg-slate-900/60'}`;
            item.innerHTML = `
                <span class="pr-4 text-sm text-slate-100">${escapeHtml(indicator?.indicator_text || '-')}</span>
            `;
            item.addEventListener('click', () => {
                selectedWorkspaceIndicatorIndex = index;
                setWorkspaceTab('indicators');
                renderWorkspaceDetail();
            });
            overview.appendChild(item);
        });

        tbody.querySelectorAll('[data-workspace-indicator-index]').forEach((button) => {
            button.addEventListener('click', () => {
                selectedWorkspaceIndicatorIndex = Number(button.getAttribute('data-workspace-indicator-index') || 0);
                setWorkspaceTab(button.getAttribute('data-target-tab') || 'indicators');
                renderWorkspaceDetail();
            });
        });
    };

    const renderWorkspaceOutputList = () => {
        const container = document.getElementById('dh-opcr-workspace-output-list');
        const count = document.getElementById('dh-opcr-workspace-output-count');
        const sourceList = document.getElementById('dh-opcr-workspace-source-list');
        if (!container || !count || !sourceList) return;

        const outputs = getWorkspaceOutputs();
        
        let filteredOutputs = outputs;
        if (activeWorkspaceFunctionTab !== 'all') {
            filteredOutputs = outputs.filter(o => {
                const ft = String(o.function_type || '').toLowerCase();
                return ft.includes(activeWorkspaceFunctionTab);
            });
        }

        count.textContent = String(filteredOutputs.length);
        container.innerHTML = '';

        if (filteredOutputs.length === 0) {
            container.innerHTML = '<p class="p-4 text-center text-sm text-slate-500">No outputs found.</p>';
        } else {
            filteredOutputs.forEach((output) => {
                const index = outputs.indexOf(output);
                const indicators = Array.isArray(output?.success_indicators) ? output.success_indicators : [];
                const button = document.createElement('button');
                button.type = 'button';
                button.className = `block w-full rounded-xl border px-3 py-3 text-left transition ${index === selectedWorkspaceOutputIndex ? 'border-blue-400/60 bg-blue-500/10 shadow-[inset_0_0_0_1px_rgba(96,165,250,0.18)]' : 'border-slate-800 bg-slate-950/30 hover:bg-slate-900/50'}`;
                button.innerHTML = `
                    <div class="line-clamp-2 text-base font-semibold leading-snug text-white">${escapeHtml(output?.title || '-')}</div>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="text-xs text-slate-500">${indicators.length} indicator${indicators.length === 1 ? '' : 's'}</span>
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

        const sourceIds = Array.from(new Set(filteredOutputs.map((output) => String(output?.source_uwp_id || '').trim()).filter(Boolean)));
        sourceList.innerHTML = sourceIds.length
            ? sourceIds.map((id) => `<div class="flex items-center justify-between gap-2"><span>UWP-${escapeHtml(id)}</span><span class="text-cyan-300">Consolidated</span></div>`).join('')
            : '<p class="text-slate-400">No source UWP reference.</p>';
    };

    const renderWorkspaceDetail = () => {
        const output = getSelectedWorkspaceOutput();
        const title = document.getElementById('dh-opcr-workspace-detail-title');
        const functionBadgeEl = document.getElementById('dh-opcr-workspace-detail-function');
        const functionCopy = document.getElementById('dh-opcr-workspace-function-copy');
        const weight = document.getElementById('dh-opcr-workspace-detail-weight');
        const weightCopy = document.getElementById('dh-opcr-workspace-weight-copy');
        const target = document.getElementById('dh-opcr-workspace-target-summary');
        if (!title || !functionBadgeEl || !functionCopy || !weight || !weightCopy || !target) return;

        title.textContent = output?.title || 'No output selected';
        const type = String(output?.function_type || '').toLowerCase();
        if (type) {
            functionBadgeEl.classList.remove('hidden');
            functionBadgeEl.className = type === 'core'
                ? 'rounded-md border px-2 py-1 text-xs font-medium border-emerald-500/20 bg-emerald-500/10 text-emerald-400'
                : 'rounded-md border px-2 py-1 text-xs font-medium border-blue-400/30 bg-blue-500/10 text-blue-300';
            functionBadgeEl.textContent = type.charAt(0).toUpperCase() + type.slice(1);
            functionCopy.innerHTML = functionBadge(type);
        } else {
            functionBadgeEl.classList.add('hidden');
            functionCopy.textContent = '-';
        }

        const weightText = output?.weight_percent !== null && output?.weight_percent !== undefined && output?.weight_percent !== '' ? `${output.weight_percent}%` : '-';
        weight.textContent = weightText;
        weightCopy.textContent = weightText;
        target.textContent = formatTargetSummaryDisplay(output?.target_quantity, output?.target_summary);

        renderWorkspaceIndicators();
        renderWorkspaceStandards();
        renderWorkspaceAssignees();
    };

    const renderWorkspaceModal = () => {
        renderWorkspaceOutputList();
        renderWorkspaceDetail();
        setWorkspaceTab(activeWorkspaceTab);
    };

    const renderStandardsModal = (mfoTitle, indicatorText, standardsByRating) => {
        document.getElementById('dh-standards-title').textContent = mfoTitle || '--';
        document.getElementById('dh-standards-indicator').textContent = indicatorText || '--';

        const tbody = document.getElementById('dh-standards-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        const renderCell = (items) => {
            const values = Array.isArray(items) ? items : [];
            if (!values.length) return '-';
            return '<ul class="list-disc space-y-1 pl-4">' + values.map((item) => '<li>' + escapeHtml(item) + '</li>').join('') + '</ul>';
        };

        ratingLevels.forEach((rating) => {
            const row = standardsByRating?.[String(rating)] || standardsByRating?.[rating] || { Q: [], E: [], T: [] };
            const q = row.Q ?? row.q ?? [];
            const e = row.E ?? row.e ?? [];
            const t = row.T ?? row.t ?? [];

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-3 py-2 text-left font-semibold text-white">${rating}</td>
                <td class="px-3 py-2 align-top">${renderCell(q)}</td>
                <td class="px-3 py-2 align-top">${renderCell(e)}</td>
                <td class="px-3 py-2 align-top">${renderCell(t)}</td>
            `;
            tbody.appendChild(tr);
        });

        openModal('dh-standards-modal');
    };

    const renderAssigneeModal = (mfoTitle, indicatorText, assignees) => {
        document.getElementById('dh-assignee-title').textContent = mfoTitle || '--';
        document.getElementById('dh-assignee-indicator').textContent = indicatorText || '--';

        const tbody = document.getElementById('dh-assignee-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        const unitName = selectedOpcr?.opcr?.office?.name || '-';
        const names = Array.isArray(assignees) ? assignees : [];

        if (!names.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">No assigned employees.</td></tr>';
            openModal('dh-indicator-assignee-modal');
            return;
        }

        names.forEach((name) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-2">${escapeHtml(name)}</td>
                <td class="px-4 py-2">${escapeHtml(unitName)}</td>
                <td class="px-4 py-2"><span class="inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2 py-1 text-[11px] font-semibold text-emerald-200">Assigned</span></td>
            `;
            tbody.appendChild(tr);
        });

        openModal('dh-indicator-assignee-modal');
    };

    const openIndicatorsModal = (mfoTitle, indicators) => {
        document.getElementById('dh-indicators-title').textContent = mfoTitle || '--';

        const tbody = document.getElementById('dh-indicators-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        (Array.isArray(indicators) ? indicators : []).forEach((indicator) => {
            const indicatorText = indicator?.indicator_text || '-';
            const standards = indicator?.standards_by_rating || {};
            const assignees = Array.isArray(indicator?.assignees) ? indicator.assignees : [];

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 align-top text-slate-100">${escapeHtml(indicatorText)}</td>
                <td class="px-4 py-3 align-top text-slate-300">${escapeHtml(getIndicatorTargetSummary(indicator))}</td>
                <td class="px-4 py-3 align-top">
                    <button type="button" class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200" data-standards-btn>
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>View Standards</span>
                    </button>
                </td>
                <td class="px-4 py-3 align-top">
                    <button type="button" class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200" data-assignee-btn>
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>View (${assignees.length})</span>
                    </button>
                </td>
            `;

            tr.querySelector('[data-standards-btn]')?.addEventListener('click', () => {
                renderStandardsModal(mfoTitle, indicatorText, standards);
            });

            tr.querySelector('[data-assignee-btn]')?.addEventListener('click', () => {
                renderAssigneeModal(mfoTitle, indicatorText, assignees);
            });

            tbody.appendChild(tr);
        });

        if (!tbody.children.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No indicators found.</td></tr>';
        }

        openModal('dh-indicators-modal');
    };

    const hydrateReviewModal = (payload) => {
        selectedOpcr = payload;

        const office = payload?.opcr?.office?.name || '-';
        const period = payload?.opcr?.period?.name || '-';
        const opcrStatus = String(payload?.opcr?.status || '').toLowerCase();
        const sourceUwpId = payload?.opcr?.source_uwp?.id || '-';
        const sourceUwpStatus = payload?.opcr?.source_uwp?.status || '-';

        document.getElementById('dh-opcr-modal-title').textContent = `PMT Review OPCR - ${office}`;
        document.getElementById('dh-opcr-modal-office').textContent = office;
        document.getElementById('dh-opcr-modal-period').textContent = period;
        document.getElementById('dh-opcr-modal-uwp').textContent = `UWP source(s): ${sourceUwpId} (${String(sourceUwpStatus).replaceAll('_', ' ')})`;

        const statusEl = document.getElementById('dh-opcr-modal-status');
        const meta = opcrStatusMeta(opcrStatus);
        statusEl.textContent = meta.label;
        statusEl.className = `inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold ${meta.cls}`;

        const remarks = document.getElementById('dh-opcr-remarks');
        const remarksError = document.getElementById('dh-opcr-remarks-error');
        const opcrId = document.getElementById('dh-opcr-id');
        const actionInput = document.getElementById('dh-opcr-action');

        if (remarks) remarks.value = '';
        if (remarksError) remarksError.classList.add('hidden');
        if (opcrId) opcrId.value = payload?.opcr?.id || '';
        if (actionInput) actionInput.value = '';

        const canReview = opcrStatus === 'endorsed' || opcrStatus === 'for_pmt_review';

        document.querySelectorAll('[data-review-action]').forEach((btn) => {
            setButtonLoading(btn, false);
            btn.classList.remove('opacity-70', 'cursor-wait');
            btn.disabled = !canReview;

            if (!canReview) {
                btn.classList.add('opacity-60', 'pointer-events-none');
            } else {
                btn.classList.remove('opacity-60', 'pointer-events-none');
            }
        });

        renderOutputs(payload?.outputs || []);
    };

    const hydrateWorkspaceReviewModal = (payload) => {
        selectedOpcr = payload;
        selectedWorkspaceOutputIndex = 0;
        selectedWorkspaceIndicatorIndex = 0;
        activeWorkspaceTab = 'overview';

        const office = payload?.opcr?.office?.name || '-';
        const period = payload?.opcr?.period?.name || '-';
        const opcrStatus = String(payload?.opcr?.status || '').toLowerCase();
        const sourceUwpIds = String(payload?.opcr?.source_uwp?.id || '').split(',').map((value) => value.trim()).filter(Boolean);
        const statusMeta = opcrStatusMeta(opcrStatus);

        document.getElementById('dh-opcr-workspace-title').textContent = `PMT OPCR Review`;
        document.getElementById('dh-opcr-workspace-subtitle').textContent = `${office} • ${period}`;
        document.getElementById('dh-opcr-workspace-office-inline').textContent = office;
        document.getElementById('dh-opcr-workspace-period-inline').textContent = period;

        const statusEl = document.getElementById('dh-opcr-workspace-status');
        statusEl.textContent = statusMeta.label;
        statusEl.className = `ml-1 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold ${statusMeta.cls}`;
        document.getElementById('dh-opcr-workspace-source-count').textContent = `${sourceUwpIds.length || 0} source UWP${sourceUwpIds.length === 1 ? '' : 's'}`;

        const remarks = document.getElementById('dh-opcr-workspace-remarks');
        const remarksError = document.getElementById('dh-opcr-workspace-remarks-error');
        const opcrId = document.getElementById('dh-opcr-workspace-id');
        const actionInput = document.getElementById('dh-opcr-workspace-action');
        if (remarks) remarks.value = '';
        if (remarksError) remarksError.classList.add('hidden');
        if (opcrId) opcrId.value = payload?.opcr?.id || '';
        if (actionInput) actionInput.value = '';

        const canReview = opcrStatus === 'endorsed' || opcrStatus === 'for_pmt_review';
        document.querySelectorAll('[data-workspace-review-action]').forEach((btn) => {
            setButtonLoading(btn, false);
            btn.disabled = !canReview;
            btn.classList.toggle('opacity-60', !canReview);
            btn.classList.toggle('pointer-events-none', !canReview);
        });

        renderWorkspaceModal();
    };

    document.querySelectorAll('[data-open-review-opcr]').forEach((button) => {
        button.addEventListener('click', () => {
            const payload = parseJson(button.getAttribute('data-opcr') || 'null');
            if (!payload) return;
            hydrateWorkspaceReviewModal(payload);
            openModal('review-opcr-workspace-modal');
        });
    });

    document.querySelectorAll('[data-opcr-workspace-tab]').forEach((button) => {
        button.addEventListener('click', () => {
            setWorkspaceTab(button.getAttribute('data-opcr-workspace-tab') || 'overview');
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

    document.querySelectorAll('[data-close-workspace-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            closeModal(document.getElementById('review-opcr-workspace-modal'));
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            closeModal(button.closest('[data-modal-container]'));
        });
    });

    document.querySelectorAll('[data-modal-container]').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const openModals = Array.from(document.querySelectorAll('[data-modal-container]')).filter((modal) => !modal.classList.contains('hidden'));
        if (!openModals.length) return;
        closeModal(openModals[openModals.length - 1]);
    });

    let activeReviewForm = null;

    document.querySelectorAll('[data-review-action], [data-workspace-review-action]').forEach((button) => {
        button.addEventListener('click', () => {
            const form = button.closest('form');
            if (!form) return;

            const action = button.getAttribute('data-review-action') || button.getAttribute('data-workspace-review-action');
            const actionInput = form.querySelector('[name="action"]');
            if (actionInput) actionInput.value = action || '';

            const opcrIdInput = form.querySelector('[name="opcr_id"]');
            if (!opcrIdInput || !opcrIdInput.value) return;

            const remarksEl = form.querySelector('[name="remarks"]');
            const remarksErrorEl = form.querySelector('[id$="-remarks-error"]');

            if (remarksErrorEl) remarksErrorEl.classList.add('hidden');

            if (action === 'return') {
                const remarks = (remarksEl?.value || '').trim();
                if (!remarks) {
                    if (remarksErrorEl) remarksErrorEl.classList.remove('hidden');
                    remarksEl?.focus();
                    return;
                }
            }

            if (action === 'approve') {
                submitReviewForm(form, button);
                return;
            }

            // For return, submit immediately
            submitReviewForm(form, button);
        });
    });

    const submitReviewForm = (form, triggeringButton) => {
        const loadingText = triggeringButton ? (triggeringButton.getAttribute('data-loading-text') || 'Processing...') : 'Processing...';
        if (triggeringButton) setButtonLoading(triggeringButton, true, loadingText);

        document.querySelectorAll('[data-review-action], [data-workspace-review-action]').forEach((peer) => {
            if (peer !== triggeringButton) {
                peer.disabled = true;
                peer.classList.add('opacity-70', 'cursor-wait');
            }
        });

        form.submit();
    };

    const pmtSigConfirm = document.getElementById('signature-pad-confirm');
    if (pmtSigConfirm) {
        pmtSigConfirm.addEventListener('click', function() {
            const signature = window.getSignatureData_pmt_signature_modal();
            if (!signature) {
                alert('Please provide your signature before approving.');
                return;
            }

            if (activeReviewForm) {
                const sigInput = activeReviewForm.querySelector('[name="signature"]');
                if (sigInput) {
                    sigInput.value = signature;
                }
                // Find the approve button inside the form to show loading state
                const approveBtn = activeReviewForm.querySelector('[data-review-action="approve"], [data-workspace-review-action="approve"]');
                
                // Hide signature modal
                const sigModal = document.getElementById('pmt-signature-modal');
                if (sigModal) {
                    sigModal.classList.add('hidden');
                    sigModal.classList.remove('flex');
                }
                
                submitReviewForm(activeReviewForm, approveBtn);
            }
        });
    }

    document.querySelectorAll('[data-signature-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = document.getElementById('pmt-signature-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });
});
</script>
@endpush
@endsection
