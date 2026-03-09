@extends('layouts.supervisor')

@section('main-content')
<section class="space-y-6 admin-page">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-white">Office Performance Commitment and Review (OPCR)</h1>
            <p class="text-sm text-slate-400">Stage I - Performance Planning and Commitment</p>
            <p class="text-xs text-slate-500">Supervisor generates OPCR from PMT-approved UWP, submits to Department Head, and handles returned revisions.</p>
        </div>

        <button type="button"
                data-direct="true"
                data-opens-modal="create-opcr-modal"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 {{ $approvedUwps->isEmpty() ? 'opacity-60 pointer-events-none' : '' }}"
                {{ $approvedUwps->isEmpty() ? 'disabled' : '' }}>
            Create OPCR
        </button>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ session('error') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <div>
                <p class="text-sm font-semibold text-white">Generated OPCRs</p>
                <p class="text-xs text-slate-400">Derived from PMT-approved UWP records.</p>
            </div>
            <span class="rounded-full border border-slate-700 bg-slate-800/70 px-3 py-1 text-xs text-slate-300">
                Active Period: {{ $activePeriod?->name ?? 'No active period' }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-300">
                <thead class="text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-2 text-left">Office / Unit</th>
                        <th class="px-4 py-2 text-left">Period</th>
                        <th class="px-4 py-2 text-left">Source (Approved UWP)</th>
                        <th class="px-4 py-2 text-left">Outputs</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($opcrs as $opcr)
                        @php
                            $uwp = $opcr->unitWorkPlan;
                            $payload = $opcrPayloads[$opcr->id] ?? null;
                            $outputsCount = is_array($payload['derived_outputs'] ?? null) ? count($payload['derived_outputs']) : 0;
                            $status = strtolower((string) $opcr->status);
                            $canSubmit = $status === \App\Models\Opcr::STATUS_DRAFT;
                            $submitDisabledTitle = $status === \App\Models\Opcr::STATUS_RETURNED
                                ? 'Returned — revise UWP and regenerate OPCR.'
                                : 'Only Draft OPCR can be submitted';
                            $statusMeta = match ($opcr->status) {
                                \App\Models\Opcr::STATUS_DRAFT => ['label' => 'Draft', 'class' => 'border-slate-500/30 bg-slate-500/10 text-slate-200'],
                                \App\Models\Opcr::STATUS_SUBMITTED => ['label' => 'Submitted', 'class' => 'border-amber-500/30 bg-amber-500/10 text-amber-200'],
                                \App\Models\Opcr::STATUS_RETURNED => ['label' => 'Returned', 'class' => 'border-rose-500/30 bg-rose-500/10 text-rose-200'],
                                \App\Models\Opcr::STATUS_APPROVED => ['label' => 'Approved', 'class' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200'],
                                default => ['label' => ucwords(str_replace('_', ' ', (string) $opcr->status)), 'class' => 'border-slate-500/30 bg-slate-500/10 text-slate-200'],
                            };
                        @endphp
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">{{ $uwp?->office?->name ?? '�' }}</td>
                            <td class="px-4 py-3">{{ $uwp?->performancePeriod?->name ?? '�' }}</td>
                            <td class="px-4 py-3">PMT Approved UWP</td>
                            <td class="px-4 py-3">{{ $outputsCount }} output{{ $outputsCount === 1 ? '' : 's' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full border px-2 py-1 text-xs {{ $statusMeta['class'] }}">
                                    {{ $statusMeta['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <button type="button"
                                            data-direct="true"
                                            data-opens-modal="view-opcr-modal"
                                            data-opcr='@json($payload)'
                                            class="text-blue-400 hover:text-blue-300 {{ $payload ? '' : 'opacity-60 pointer-events-none' }}"
                                            {{ $payload ? '' : 'disabled' }}>
                                        View
                                    </button>

                                    @if ($canSubmit)
                                        <form method="POST" action="{{ route('stage1.opcr.submit', ['opcr' => $opcr->id]) }}">
                                            @csrf
                                            <button type="submit"
                                                    data-submit-loading
                                                    data-loading-text="Submitting..."
                                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500">
                                                <span data-button-label>Submit</span>
                                                <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button"
                                                disabled
                                                title="{{ $submitDisabledTitle }}"
                                                class="inline-flex cursor-not-allowed items-center gap-2 rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-500 opacity-50">
                                            Submit
                                        </button>
                                    @endif
                                </div>
                                @if ($status === \App\Models\Opcr::STATUS_RETURNED)
                                    <p class="mt-2 text-[11px] text-rose-300">Returned — revise UWP and regenerate OPCR.</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">No OPCR generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="create-opcr-modal" data-modal-container class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Generate OPCR</h2>
                    <p class="text-sm text-slate-400">Generate Office Performance Commitments derived from PMT-approved UWP (Stage I).</p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form id="generate-opcr-form" method="POST" action="{{ route('stage1.opcr.generate') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm text-slate-300">Approved Unit Work Plan (UWP)</label>
                    <select id="uwpSelect" name="unit_work_plan_id" style="background:#0f172a;color:#e5e7eb;" class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white">
                        <option value="">Select approved UWP</option>
                        @forelse($approvedUwpPayloads as $item)
                            <option value="{{ $item['id'] }}" data-uwp='@json($item['payload'])'>{{ $item['label'] }}</option>
                        @empty
                            <option value="" disabled>No PMT-approved UWP available</option>
                        @endforelse
                    </select>
                </div>

                <div id="derivedPreview" class="hidden rounded-lg border border-slate-800 bg-slate-900/50 p-4">
                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                        <p class="text-xs text-slate-400">Derived Office Performance Commitments (read-only preview)</p>
                        <p class="text-[11px] text-slate-500"><span id="derivedPreviewOffice">�</span> � <span id="derivedPreviewPeriod">�</span></p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-slate-300">
                            <thead class="text-slate-500 uppercase">
                                <tr>
                                    <th class="py-2 text-left">Output</th>
                                    <th class="py-2 text-left">Success Indicators</th>
                                    <th class="py-2 text-left">Timeline / Target</th>
                                    <th class="py-2 pr-4 text-left">Weight</th>
                                    <th class="py-2 pl-4 text-left">Function</th>
                                </tr>
                            </thead>
                            <tbody id="derived-preview-table-body" class="divide-y divide-slate-800"></tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Cancel</button>
                    <button type="submit" id="generate-opcr-button" data-submit-loading data-loading-text="Generating OPCR..." class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 {{ $approvedUwps->isEmpty() ? 'opacity-60 pointer-events-none' : '' }}" {{ $approvedUwps->isEmpty() ? 'disabled' : '' }}>
                        <span data-button-label>Generate OPCR</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div id="view-opcr-modal" data-modal-container class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl space-y-5">
            <div class="space-y-3">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-semibold text-white">Office Performance Commitment and Review</h3>
                        <p class="text-sm text-slate-400">Derived from PMT-approved Unit Work Plan (Stage I)</p>
                    </div>
                    <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-300">
                    <div class="hidden rounded-full bg-slate-800 px-3 py-1 text-slate-400 lg:flex">
                        Office / Unit:
                        <span id="viewOpcrOffice" class="ml-1 font-semibold text-white">�</span>
                    </div>
                    <div class="hidden rounded-full bg-slate-800 px-3 py-1 text-slate-400 lg:flex">
                        Period:
                        <span id="viewOpcrPeriod" class="ml-1 font-semibold text-white">�</span>
                    </div>
                    <div class="hidden rounded-full bg-slate-800 px-3 py-1 text-slate-400 lg:flex">
                        Source:
                        <span class="ml-1 font-semibold text-white">PMT Approved UWP</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-800 bg-slate-950/40 px-4 py-3 text-xs text-slate-300">
                <div class="flex flex-wrap gap-4 text-sm">
                    <span id="viewOpcrOfficeInline" class="font-semibold text-white">�</span>
                    <span>Period: <span id="viewOpcrPeriodInline" class="font-semibold text-white">�</span></span>
                    <span>Source: <span class="font-semibold text-white">PMT Approved UWP</span></span>
                </div>
                <span id="viewOpcrStatus" class="rounded-full border px-3 py-1 text-xs font-semibold">�</span>
            </div>

            <div class="max-h-[58vh] overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-inner">
                <div class="max-h-[58vh] overflow-y-auto overflow-x-auto">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="sticky top-0 z-10 bg-slate-900/90 border-b border-slate-800 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Output</th>
                                <th class="px-4 py-3 text-left">Success Indicators</th>
                                <th class="px-4 py-3 text-left">Target Summary</th>
                                <th class="px-4 py-3 text-left">Weight</th>
                                <th class="px-4 py-3 text-left">Function</th>
                            </tr>
                        </thead>
                        <tbody id="view-opcr-table-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('stage1.opcr.export.excel') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Export</a>
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    <div id="uwp-indicators-modal" data-modal-container class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-950 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <h3 id="uwp-indicators-title" class="text-xl font-semibold text-white">--</h3>
                    <p class="text-sm text-slate-400 mt-1">Read-only indicators for the selected output.</p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="px-6 py-5">
                <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-950/40">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-900/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                            <tr>
                                <th class="px-5 py-4 text-left">Success Indicator</th>
                                <th class="px-5 py-4 text-center">Standards</th>
                                <th class="px-5 py-4 text-center">Assigned Employee</th>
                            </tr>
                        </thead>
                        <tbody id="uwp-indicators-table-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end px-6 pb-6">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-5 py-2.5 text-sm text-slate-200 hover:bg-slate-900/60">Close</button>
            </div>
        </div>
    </div>

    <div id="uwp-standards-modal" data-modal-container class="fixed inset-0 z-[95] hidden items-center justify-center bg-black/70 px-4 py-8">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 class="text-lg font-semibold text-white">Target Difficulty / Standards</h3>
                    <p class="text-[11px] text-slate-400 mt-1">MFO: <span id="uwp-standards-title">--</span></p>
                    <p class="text-[11px] text-slate-400 mt-1">Indicator: <span id="uwp-standards-indicator">--</span></p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
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

            <div class="mt-5 flex justify-end border-t border-slate-800 pt-3">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    <div id="uwp-assignees-modal" data-modal-container class="fixed inset-0 z-[96] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-950 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employee</p>
                    <h3 class="text-lg font-semibold text-white">Employee assigned to the selected indicator</h3>
                    <p class="text-xs text-slate-400 mt-1">MFO: <span id="uwp-assignees-title">--</span></p>
                    <p class="text-xs text-slate-400 mt-1">Indicator: <span id="uwp-assignees-indicator" class="font-semibold text-slate-100">--</span></p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
                <table class="min-w-full text-sm text-slate-100">
                    <thead class="bg-slate-900/80 text-xs uppercase tracking-[0.2em] text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee Name</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody id="uwp-assignees-table-body" class="divide-y divide-slate-800"></tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ratingLevels = [5, 4, 3, 2, 1];

    const parseJson = (value, fallback = null) => {
        try {
            return JSON.parse(value);
        } catch (error) {
            return fallback;
        }
    };

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const formatTargetTimelineDisplay = (targetQuantity, targetTimeline) => {
        const quantity = targetQuantity === null || targetQuantity === undefined || targetQuantity === ''
            ? ''
            : String(targetQuantity).trim();
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
    };

    const statusLabel = (status) => {
        const key = String(status || '').toLowerCase();
        if (key === 'draft') return 'Draft';
        if (key === 'submitted') return 'Submitted';
        if (key === 'returned') return 'Returned';
        if (key === 'approved') return 'Approved';
        return key ? key.replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase()) : '�';
    };

    const statusClass = (status) => {
        const key = String(status || '').toLowerCase();
        if (key === 'draft') return 'border-slate-500/30 bg-slate-500/10 text-slate-200';
        if (key === 'submitted') return 'border-amber-500/30 bg-amber-500/10 text-amber-200';
        if (key === 'returned') return 'border-rose-500/30 bg-rose-500/10 text-rose-200';
        if (key === 'approved') return 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200';
        return 'border-slate-500/30 bg-slate-500/10 text-slate-200';
    };

    const functionBadge = (functionType) => {
        const type = String(functionType || '').toLowerCase();
        if (type === 'core') {
            return '<span class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300">Core</span>';
        }
        if (type === 'support') {
            return '<span class="rounded-md border border-blue-400/30 bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-300">Support</span>';
        }
        return '<span class="rounded-md border border-slate-600 bg-slate-700/50 px-2 py-1 text-xs font-medium text-slate-300">' + escapeHtml(type || 'Custom') + '</span>';
    };

    const openModal = (modalId) => {
        const modal = document.getElementById(modalId);
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

    const renderAssigneesModal = (mfoTitle, indicatorText, assignees) => {
        document.getElementById('uwp-assignees-title').textContent = mfoTitle || '--';
        document.getElementById('uwp-assignees-indicator').textContent = indicatorText || '--';

        const tbody = document.getElementById('uwp-assignees-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        const rows = Array.isArray(assignees) ? assignees : [];
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">No assigned employees.</td></tr>';
            openModal('uwp-assignees-modal');
            return;
        }

        rows.forEach((entry) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 text-slate-100">${escapeHtml(entry.name || '�')}</td>
                <td class="px-4 py-3 text-slate-200">${escapeHtml(entry.office || '�')}</td>
                <td class="px-4 py-3"><span class="inline-flex rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Assigned</span></td>
            `;
            tbody.appendChild(tr);
        });

        openModal('uwp-assignees-modal');
    };

    const standardsCell = (items) => {
        const values = Array.isArray(items) ? items : [];
        if (!values.length) return '�';
        return '<ul class="list-disc space-y-1 pl-4">' + values.map((item) => '<li>' + escapeHtml(item) + '</li>').join('') + '</ul>';
    };

    const renderStandardsModal = (mfoTitle, indicatorText, standardsByRating) => {
        document.getElementById('uwp-standards-title').textContent = mfoTitle || '--';
        document.getElementById('uwp-standards-indicator').textContent = indicatorText || '--';

        const tbody = document.getElementById('uwp-standards-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        ratingLevels.forEach((rating) => {
            const row = standardsByRating?.[String(rating)] || standardsByRating?.[rating] || {};
            const q = row?.Q ?? row?.q ?? [];
            const e = row?.E ?? row?.e ?? [];
            const t = row?.T ?? row?.t ?? [];

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 font-semibold">${rating}</td>
                <td class="px-4 py-3 align-top">${standardsCell(q)}</td>
                <td class="px-4 py-3 align-top">${standardsCell(e)}</td>
                <td class="px-4 py-3 align-top">${standardsCell(t)}</td>
            `;
            tbody.appendChild(tr);
        });

        openModal('uwp-standards-modal');
    };

    const openIndicatorsModal = (mfoTitle, indicators, fallbackOffice) => {
        const titleEl = document.getElementById('uwp-indicators-title');
        const tbody = document.getElementById('uwp-indicators-table-body');
        if (!titleEl || !tbody) return;

        titleEl.textContent = mfoTitle || '--';
        tbody.innerHTML = '';

        (Array.isArray(indicators) ? indicators : []).forEach((indicator) => {
            const indicatorText = indicator?.indicator_text || '�';
            const standards = indicator?.standards_by_rating || {};
            const sourceAssignees = Array.isArray(indicator?.assignees) ? indicator.assignees : [];
            const assignees = sourceAssignees.map((entry) => {
                if (entry && typeof entry === 'object') {
                    return { name: entry.name || '�', office: entry.office || fallbackOffice || '�' };
                }
                return { name: String(entry || '�'), office: fallbackOffice || '�' };
            }).filter((entry) => entry.name && entry.name !== '�');

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';

            const tdIndicator = document.createElement('td');
            tdIndicator.className = 'px-5 py-4 text-slate-100';
            tdIndicator.textContent = indicatorText;

            const tdStandards = document.createElement('td');
            tdStandards.className = 'px-5 py-4 text-center';
            const standardsBtn = document.createElement('button');
            standardsBtn.type = 'button';
            standardsBtn.className = 'inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800';
            standardsBtn.innerHTML = '<i class="fa-regular fa-eye text-sm"></i><span>View Standards</span>';
            standardsBtn.addEventListener('click', () => renderStandardsModal(mfoTitle, indicatorText, standards));
            tdStandards.appendChild(standardsBtn);

            const tdAssignee = document.createElement('td');
            tdAssignee.className = 'px-5 py-4 text-center';
            const assigneeBtn = document.createElement('button');
            assigneeBtn.type = 'button';
            assigneeBtn.className = 'inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800';
            assigneeBtn.innerHTML = '<i class="fa-regular fa-eye text-sm"></i><span>View (' + assignees.length + ')</span>';
            assigneeBtn.addEventListener('click', () => renderAssigneesModal(mfoTitle, indicatorText, assignees));
            tdAssignee.appendChild(assigneeBtn);

            tr.append(tdIndicator, tdStandards, tdAssignee);
            tbody.appendChild(tr);
        });

        if (!tbody.children.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">No success indicators found.</td></tr>';
        }

        openModal('uwp-indicators-modal');
    };

    const renderOutputsTable = (tbodyId, payload) => {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        tbody.innerHTML = '';
        const outputs = Array.isArray(payload?.derived_outputs) ? payload.derived_outputs : [];
        const officeName = payload?.office?.name || '�';

        outputs.forEach((output) => {
            const indicators = Array.isArray(output?.success_indicators) ? output.success_indicators : [];
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-4 text-slate-100">${escapeHtml(output?.mfo_title || '�')}</td>
                <td class="px-4 py-4">
                    <button type="button" class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200" data-indicators-btn>
                        <i class="fa-regular fa-eye text-sm"></i><span>(${indicators.length})</span>
                    </button>
                </td>
                <td class="px-4 py-4 text-slate-200">${escapeHtml(formatTargetTimelineDisplay(output?.target_quantity, output?.target_timeline))}</td>
                <td class="px-4 py-4 text-slate-200">${output?.weight_percent !== null && output?.weight_percent !== undefined && output?.weight_percent !== '' ? escapeHtml(String(output.weight_percent) + '%') : '�'}</td>
                <td class="px-4 py-4">${functionBadge(output?.function_type)}</td>
            `;

            tr.querySelector('[data-indicators-btn]')?.addEventListener('click', () => {
                openIndicatorsModal(output?.mfo_title || '--', indicators, officeName);
            });

            tbody.appendChild(tr);
        });

        if (!tbody.children.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No derived outputs found.</td></tr>';
        }
    };

    const renderCreatePreview = (payload) => {
        const preview = document.getElementById('derivedPreview');
        if (!preview) return;

        if (!payload) {
            preview.classList.add('hidden');
            renderOutputsTable('derived-preview-table-body', { derived_outputs: [] });
            return;
        }

        document.getElementById('derivedPreviewOffice').textContent = payload?.office?.name || '�';
        document.getElementById('derivedPreviewPeriod').textContent = payload?.period?.name || '�';
        renderOutputsTable('derived-preview-table-body', payload);
        preview.classList.remove('hidden');
    };

    const renderViewModal = (payload) => {
        document.getElementById('viewOpcrOffice').textContent = payload?.office?.name || '�';
        document.getElementById('viewOpcrPeriod').textContent = payload?.period?.name || '�';
        document.getElementById('viewOpcrOfficeInline').textContent = payload?.office?.name || '�';
        document.getElementById('viewOpcrPeriodInline').textContent = payload?.period?.name || '�';

        const badge = document.getElementById('viewOpcrStatus');
        const status = payload?.opcr_status || 'draft';
        badge.textContent = statusLabel(status);
        badge.className = 'rounded-full border px-3 py-1 text-xs font-semibold ' + statusClass(status);

        renderOutputsTable('view-opcr-table-body', payload || { derived_outputs: [] });
    };

    const uwpSelect = document.getElementById('uwpSelect');
    uwpSelect?.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const payload = selectedOption?.dataset?.uwp ? parseJson(selectedOption.dataset.uwp, null) : null;
        renderCreatePreview(payload);
    });

    document.querySelectorAll('[data-direct="true"][data-opens-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.dataset.opensModal;

            if (target === 'view-opcr-modal') {
                const payload = button.dataset.opcr ? parseJson(button.dataset.opcr, null) : null;
                renderViewModal(payload || { derived_outputs: [] });
            }

            if (target === 'create-opcr-modal') {
                if (uwpSelect && uwpSelect.value) {
                    const selectedOption = uwpSelect.options[uwpSelect.selectedIndex];
                    const payload = selectedOption?.dataset?.uwp ? parseJson(selectedOption.dataset.uwp, null) : null;
                    renderCreatePreview(payload);
                } else {
                    renderCreatePreview(null);
                }
            }

            openModal(target);
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

    document.querySelectorAll('form').forEach((form) => {
        if (form.id === 'generate-opcr-form') {
            return;
        }

        form.addEventListener('submit', () => {
            const submitBtn = form.querySelector('[data-submit-loading]');
            if (!submitBtn) return;

            const label = submitBtn.querySelector('[data-button-label]');
            const spinner = submitBtn.querySelector('[data-button-spinner]');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-wait');

            if (label) {
                label.textContent = submitBtn.dataset.loadingText || 'Submitting...';
            }
            if (spinner) {
                spinner.classList.remove('hidden');
            }
        });
    });

    const generateForm = document.getElementById('generate-opcr-form');
    const generateButton = document.getElementById('generate-opcr-button');
    generateForm?.addEventListener('submit', (event) => {
        if (!uwpSelect || !uwpSelect.value) {
            event.preventDefault();
            alert('Please select a PMT-approved UWP.');
            return;
        }

        if (!generateButton) return;
        const label = generateButton.querySelector('[data-button-label]');
        const spinner = generateButton.querySelector('[data-button-spinner]');

        generateButton.disabled = true;
        generateButton.classList.add('opacity-70', 'cursor-wait');

        if (label) {
            label.textContent = generateButton.dataset.loadingText || 'Generating OPCR...';
        }

        if (spinner) {
            spinner.classList.remove('hidden');
        }
    });
});
</script>
@endpush
@endsection

