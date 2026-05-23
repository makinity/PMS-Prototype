@extends('layouts.pmt')

@section('main-content')
@php
    $currentIpcr = $ipcr ?? null;
    $currentPayload = $payload ?? [];
    $ipcrStatus = strtolower((string) ($currentIpcr?->status ?? ''));
    $canCalibrate = in_array($ipcrStatus, [\App\Models\Ipcr::STATUS_PENDING_PMT_CALIBRATION, \App\Models\Ipcr::STATUS_APPROVED_BY_PMT, \App\Models\Ipcr::STATUS_ADJUSTED_BY_PMT], true);
    $canRelease = in_array($ipcrStatus, [\App\Models\Ipcr::STATUS_APPROVED_BY_PMT, \App\Models\Ipcr::STATUS_ADJUSTED_BY_PMT], true);
    $defaultAdjustedScore = old('adjusted_score', $currentIpcr->pmt_adjusted_score ?? $currentPayload['computed_score']);
    $selectedAdjustedRating = old('adjusted_rating', $currentIpcr->pmt_adjusted_rating ?? $currentPayload['computed_rating']);
@endphp

<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('pmt.employee-calibration.index') }}" class="mb-2 inline-flex items-center text-sm font-semibold text-blue-400 hover:text-blue-300">
                &larr; Back to Calibration List
            </a>
            <h1 class="text-2xl font-bold text-white">Employee Performance Calibration</h1>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
            <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Active Period</p>
            <p class="mt-1 text-sm font-semibold text-white">{{ $currentPayload['period_label'] ?? '-' }}</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-800 px-5 py-4">
            <div>
                <h2 class="text-lg font-semibold text-white">Submission Overview</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                @if ($currentIpcr)
                    @php
                        $ipcrBadge = match ($ipcrStatus) {
                            \App\Models\Ipcr::STATUS_PENDING_PMT_CALIBRATION => 'border-sky-500/30 bg-sky-500/10 text-sky-300',
                            \App\Models\Ipcr::STATUS_APPROVED_BY_PMT => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
                            \App\Models\Ipcr::STATUS_ADJUSTED_BY_PMT => 'border-violet-500/30 bg-violet-500/10 text-violet-300',
                            \App\Models\Ipcr::STATUS_RELEASED_BY_PMT => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300',
                            \App\Models\Ipcr::STATUS_RETURNED_BY_PMT => 'border-rose-500/30 bg-rose-500/10 text-rose-300',
                            default => 'border-slate-500/30 bg-slate-500/10 text-slate-300',
                        };
                    @endphp
                    <span class="rounded-full border px-3 py-1 {{ $ipcrBadge }}">
                        {{ $currentPayload['status_label'] ?? 'Draft' }}
                    </span>
                @else
                    <span class="rounded-full border border-slate-700 bg-slate-950/70 px-3 py-1 text-slate-300">No IPCR yet</span>
                @endif
            </div>
        </div>

        @if ($currentIpcr)
            <div class="grid gap-3 border-b border-slate-800 px-5 py-4 sm:grid-cols-4">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Employee</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $currentPayload['employee_name'] ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Office / Unit</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $currentPayload['office_name'] ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Computed Score</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $currentPayload['computed_score'] !== null ? number_format((float) $currentPayload['computed_score'], 2) : '-' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Adjusted Score</p>
                    <p class="mt-1 text-sm font-semibold text-blue-300">{{ $currentPayload['adjusted_score'] !== null ? number_format((float) $currentPayload['adjusted_score'], 2) : '-' }}</p>
                </div>
            </div>

            <div class="grid gap-3 border-b border-slate-800 px-5 py-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">PMT Remarks</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-slate-200">{{ trim($currentPayload['pmt_remarks'] ?? '') !== '' ? $currentPayload['pmt_remarks'] : '--' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">PMT Adjustment Reason</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-slate-200">{{ trim($currentPayload['adjustment_reason'] ?? '') !== '' ? $currentPayload['adjustment_reason'] : '--' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 border-b border-slate-800 px-5 py-4 md:grid-cols-2">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">SMPOR - Monitoring Summary</h4>
                            <p class="mt-1 text-xs text-slate-400">{{ $currentPayload['smporSourceLabel'] ?? 'Submitted MPORs snapshot.' }}</p>
                        </div>
                        <button type="button" data-open-smpor-preview aria-label="Open SMPOR preview" class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-2.5 py-2 text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                            View
                        </button>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">IPCR Accomplishment Report</h4>
                            <p class="mt-1 text-xs text-slate-400">Success indicators and standards snapshot.</p>
                        </div>
                        <button type="button" data-open-ipcr-preview aria-label="Open IPCR preview" class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-2.5 py-2 text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                            View
                        </button>
                    </div>
                </div>
            </div>

            @if ($canCalibrate)
                <div class="bg-slate-950/80 px-5 py-4">
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                            <h4 class="text-sm font-semibold text-white">Adjust Rating</h4>
                            <form method="POST" action="{{ route('pmt.employee-calibration.adjust', $currentIpcr->id) }}" class="mt-3 space-y-3">
                                @csrf
                                <div>
                                    <label class="text-xs text-slate-400">Adjusted Score</label>
                                    <input type="number" step="0.01" min="1" max="5" name="adjusted_score" value="{{ $defaultAdjustedScore }}"
                                        style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                        class="w-full rounded border px-3 py-2 text-sm" required>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-400">Adjusted Rating</label>
                                    <select name="adjusted_rating"
                                        style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                        class="w-full rounded border px-3 py-2 text-sm" required>
                                        <option value="Outstanding" @selected($selectedAdjustedRating === 'Outstanding')>Outstanding</option>
                                        <option value="Very Satisfactory" @selected($selectedAdjustedRating === 'Very Satisfactory')>Very Satisfactory</option>
                                        <option value="Satisfactory" @selected($selectedAdjustedRating === 'Satisfactory')>Satisfactory</option>
                                        <option value="Unsatisfactory" @selected($selectedAdjustedRating === 'Unsatisfactory')>Unsatisfactory</option>
                                        <option value="Poor" @selected($selectedAdjustedRating === 'Poor')>Poor</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-400">Adjustment Reason</label>
                                    <textarea name="adjustment_reason" rows="2"
                                        style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                        class="w-full rounded border px-3 py-2 text-sm" required></textarea>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="rounded border border-blue-600 bg-blue-600/20 px-4 py-2 text-sm font-semibold text-blue-300 hover:bg-blue-600/30">Submit Adjustment</button>
                                </div>
                            </form>
                        </div>

                        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                            <h4 class="text-sm font-semibold text-white">Approve / Release / Return</h4>
                            <form method="POST" action="{{ route('pmt.employee-calibration.approve', $currentIpcr->id) }}" class="mt-3 space-y-3">
                                @csrf
                                <div>
                                    <label class="text-xs text-slate-400">PMT Remarks</label>
                                    <textarea name="remarks" id="pmtRemarksInput" rows="3"
                                        style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                        class="w-full rounded border px-3 py-2 text-sm"></textarea>
                                </div>
                                <div class="mt-4 flex flex-wrap items-center justify-end gap-3">
                                    <button type="button" id="pmtReturnBtn" class="rounded border border-rose-600 bg-rose-600/20 px-4 py-2 text-sm font-semibold text-rose-300 hover:bg-rose-600/30">Return to Employee</button>
                                    @if ($canRelease)
                                        <button type="button" id="pmtReleaseBtn" class="rounded border border-cyan-600 bg-cyan-600/20 px-4 py-2 text-sm font-semibold text-cyan-300 hover:bg-cyan-600/30">Release Official Result</button>
                                    @endif
                                    <button type="submit" class="rounded border border-emerald-600 bg-emerald-600/20 px-4 py-2 text-sm font-semibold text-emerald-300 hover:bg-emerald-600/30">Approve As Is</button>
                                </div>
                            </form>

                            <form method="POST" id="pmtSubmissionReturnForm" action="{{ route('pmt.employee-calibration.return', $currentIpcr->id) }}" class="hidden">
                                @csrf
                                <input type="hidden" name="remarks" id="pmtReturnRemarksInput">
                            </form>
                            <form method="POST" id="pmtSubmissionReleaseForm" action="{{ route('pmt.employee-calibration.release', $currentIpcr->id) }}" class="hidden">
                                @csrf
                                <input type="hidden" name="remarks" id="pmtReleaseRemarksInput">
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="px-5 py-10 text-center">
                <p class="text-sm font-medium text-white">No consolidated IPCR preview yet.</p>
            </div>
        @endif
    </section>
</section>

<!-- Modals from index.blade.php ported over -->
<div id="pmt-smpor-preview-modal" data-preview-modal class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/60 px-4 py-6">
    <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
        <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-blue-300">SMPOR (Monitoring Summary)</p>
                <h3 class="text-lg font-semibold text-white">SMPOR Preview</h3>
            </div>
            <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
        </div>
        <div class="mt-4 max-h-[66vh] space-y-5 overflow-y-auto pr-1 text-sm text-slate-200">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" data-smpor-tab="quantity" class="rounded-lg border border-sky-500/40 bg-sky-500/20 px-3 py-1.5 text-xs font-semibold text-sky-200 transition">Efficiency/Quantity</button>
                    <button type="button" data-smpor-tab="quality" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-300 transition hover:bg-slate-800">Quality/Effectiveness</button>
                    <button type="button" data-smpor-tab="timeliness" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-300 transition hover:bg-slate-800">Timeliness</button>
                </div>
            </div>
            <div id="smporQuantityPanel" data-smpor-tab-panel="quantity" class="overflow-x-auto rounded-xl border border-slate-800"></div>
            <div id="smporQualityPanel" data-smpor-tab-panel="quality" class="hidden overflow-x-auto rounded-xl border border-slate-800"></div>
            <div id="smporTimelinessPanel" data-smpor-tab-panel="timeliness" class="hidden overflow-x-auto rounded-xl border border-slate-800"></div>
        </div>
        <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-800 pt-4"><button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button></div>
    </div>
</div>

<div id="pmt-ipcr-preview-modal" data-preview-modal class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 px-4 py-6">
    <div class="w-full max-w-7xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
        <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR (Accomplishment Report)</p>
                <h3 class="text-lg font-semibold text-white">IPCR Preview</h3>
            </div>
            <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
        </div>
        <div class="mt-4 max-h-[66vh] space-y-4 overflow-y-auto pr-1 text-sm text-slate-200">
            <div id="ipcrSectionsContainer" class="space-y-4"></div>
        </div>
        <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-800 pt-4"><button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button></div>
    </div>
</div>

<script id="pmt-current-payload-json" type="application/json">{!! json_encode($currentPayload ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
        if (window.PMSnackbar) {
            window.PMSnackbar.show({ type: 'success', message: @json(session('success')) });
        }
    @endif
    @if(session('error'))
        if (window.PMSnackbar) {
            window.PMSnackbar.show({ type: 'error', message: @json(session('error')) });
        }
    @endif

    document.getElementById('pmtReturnBtn')?.addEventListener('click', () => {
        const remarks = document.getElementById('pmtRemarksInput')?.value || '';
        if (!remarks.trim()) {
            alert('Please provide PMT Remarks before returning.');
            return;
        }
        document.getElementById('pmtReturnRemarksInput').value = remarks;
        document.getElementById('pmtSubmissionReturnForm').submit();
    });
    document.getElementById('pmtReleaseBtn')?.addEventListener('click', () => {
        const remarks = document.getElementById('pmtRemarksInput')?.value || '';
        document.getElementById('pmtReleaseRemarksInput').value = remarks;
        document.getElementById('pmtSubmissionReleaseForm').submit();
    });

    const payloadScript = document.getElementById('pmt-current-payload-json');
    let currentPayload = null;
    try {
        currentPayload = JSON.parse(payloadScript?.textContent || '{}');
    } catch (e) {}

    const openPreviewStack = [];
    const smporTabButtons = Array.from(document.querySelectorAll('[data-smpor-tab]'));
    const smporTabPanels = Array.from(document.querySelectorAll('[data-smpor-tab-panel]'));
    const smporQuantityPanelEl = document.getElementById('smporQuantityPanel');
    const smporQualityPanelEl = document.getElementById('smporQualityPanel');
    const smporTimelinessPanelEl = document.getElementById('smporTimelinessPanel');
    const ipcrSectionsContainerEl = document.getElementById('ipcrSectionsContainer');

    function escapeHtml(value) {
        return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
    function formatNumber(value, fixed = null) {
        const numeric = Number(value ?? 0);
        if (!Number.isFinite(numeric)) return fixed === 2 ? '0.00' : '0';
        if (fixed === 2) return numeric.toFixed(2);
        if (Math.floor(numeric) === numeric) return String(numeric);
        return numeric.toFixed(2).replace(/\.00$/, '').replace(/(\.\d*[1-9])0+$/, '$1');
    }
    function isAnyModalOpen() { return openPreviewStack.length > 0; }
    function syncBodyScroll() { document.body.classList.toggle('overflow-hidden', isAnyModalOpen()); }
    function refreshPreviewModalZIndices() {
        const baseZ = 80;
        openPreviewStack.forEach((modalEl, index) => { modalEl.style.zIndex = String(baseZ + (index * 10)); });
    }
    function openPreviewModal(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        const existingIndex = openPreviewStack.indexOf(modalEl);
        if (existingIndex !== -1) openPreviewStack.splice(existingIndex, 1);
        modalEl.classList.remove('hidden');
        modalEl.classList.add('flex');
        openPreviewStack.push(modalEl);
        refreshPreviewModalZIndices();
        syncBodyScroll();
    }
    function closePreviewModal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.add('hidden');
        modalEl.classList.remove('flex');
        const index = openPreviewStack.indexOf(modalEl);
        if (index !== -1) openPreviewStack.splice(index, 1);
        refreshPreviewModalZIndices();
        syncBodyScroll();
    }

    function buildSmporTable(mode, months, sections) {
        const monthLabels = Array.isArray(months) && months.length > 0 ? months : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const dataSections = Array.isArray(sections) ? sections : [];
        const isQuantity = mode === 'quantity';
        const valueKey = mode === 'quality' ? 'quality' : (mode === 'timeliness' ? 'timeliness' : 'quantity');
        const totalKey = mode === 'quality' ? 'quality_total' : (mode === 'timeliness' ? 'timeliness_total' : 'quantity_total');
        const colspan = monthLabels.length + 2;

        let tableHtml = `<table class="min-w-full text-left text-sm text-slate-200"><thead class="bg-slate-950/70 text-xs uppercase text-slate-400"><tr><th class="px-4 py-3">Expected Outputs</th>${monthLabels.map((label) => `<th class="px-4 py-3 text-right">${escapeHtml(label)}</th>`).join('')}<th class="px-4 py-3 text-right">Total</th></tr></thead><tbody class="divide-y divide-slate-800">`;

        if (dataSections.length === 0) {
            tableHtml += `<tr class="bg-slate-900/40"><td colspan="${colspan}" class="px-4 py-3 text-center text-slate-400">No SMPOR snapshot data available.</td></tr>`;
        } else {
            dataSections.forEach((section) => {
                const sectionTitle = String(section?.title || 'Section').trim() || 'Section';
                const sectionRows = Array.isArray(section?.rows) ? section.rows : [];
                tableHtml += `<tr class="bg-slate-950/60"><td colspan="${colspan}" class="px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] text-slate-100">${escapeHtml(sectionTitle)}</td></tr>`;

                sectionRows.forEach((row) => {
                    const monthlyValues = row?.[valueKey] && typeof row[valueKey] === 'object' ? row[valueKey] : {};
                    const totalValue = row?.[totalKey] ?? 0;
                    tableHtml += '<tr class="bg-slate-900/40">';
                    tableHtml += `<td class="px-4 py-3 font-semibold">${escapeHtml(row?.expected_output || '--')}</td>`;
                    monthLabels.forEach((monthLabel) => {
                        const cellValue = monthlyValues?.[monthLabel] ?? 0;
                        tableHtml += `<td class="px-4 py-3 text-right">${isQuantity ? formatNumber(cellValue) : formatNumber(cellValue, 2)}</td>`;
                    });
                    tableHtml += `<td class="px-4 py-3 text-right">${isQuantity ? formatNumber(totalValue) : formatNumber(totalValue, 2)}</td>`;
                    tableHtml += '</tr>';
                });
            });
        }
        tableHtml += '</tbody></table>';
        return tableHtml;
    }

    function setSmporTab(activeTab) {
        smporTabButtons.forEach((button) => {
            const isActive = button.dataset.smporTab === activeTab;
            button.classList.toggle('border-sky-500/40', isActive);
            button.classList.toggle('bg-sky-500/20', isActive);
            button.classList.toggle('text-sky-200', isActive);
            button.classList.toggle('border-slate-700', !isActive);
            button.classList.toggle('text-slate-300', !isActive);
            button.classList.toggle('hover:bg-slate-800', !isActive);
        });
        smporTabPanels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.smporTabPanel !== activeTab);
        });
    }

    function renderSmporPreview(payload) {
        if (!payload) return;
        const months = Array.isArray(payload.smporMonths) && payload.smporMonths.length > 0 ? payload.smporMonths : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const sections = Array.isArray(payload.smporSections) ? payload.smporSections : [];
        if (smporQuantityPanelEl) smporQuantityPanelEl.innerHTML = buildSmporTable('quantity', months, sections);
        if (smporQualityPanelEl) smporQualityPanelEl.innerHTML = buildSmporTable('quality', months, sections);
        if (smporTimelinessPanelEl) smporTimelinessPanelEl.innerHTML = buildSmporTable('timeliness', months, sections);
        setSmporTab('quantity');
        openPreviewModal('pmt-smpor-preview-modal');
    }

    function renderIpcrPreview(payload) {
        if (!payload) return;
        const ipcrSections = Array.isArray(payload.ipcrSections) ? payload.ipcrSections : [];
        if (!ipcrSectionsContainerEl) return;

        if (ipcrSections.length === 0) {
            ipcrSectionsContainerEl.innerHTML = '<div class="rounded-xl border border-slate-800 bg-slate-950/60 px-4 py-8 text-center text-slate-400">No IPCR commitments found for this submission.</div>';
        } else {
            ipcrSectionsContainerEl.innerHTML = ipcrSections.map((section) => {
                const rows = Array.isArray(section?.rows) ? section.rows : [];
                const weight = Number(section?.weight_percent ?? 0);
                const weightLabel = Number.isFinite(weight) && weight > 0 ? ` (${formatNumber(weight)}%)` : '';

                const rowsHtml = rows.length === 0
                    ? '<tr class="bg-slate-900/40"><td colspan="4" class="px-4 py-3 text-center text-slate-400">No major outputs found.</td></tr>'
                    : rows.map((row) => {
                        return `<tr class="bg-slate-900/40 align-top"><td class="px-4 py-3 font-semibold text-slate-100">${escapeHtml(row?.major_output || '--')}</td><td class="px-4 py-3 text-slate-200">${escapeHtml(row?.target_summary || '--')}</td><td class="px-4 py-3 text-slate-300">${escapeHtml(row?.timeline || '--')}</td></tr>`;
                    }).join('');

                return `<div class="rounded-xl border border-slate-800 bg-slate-950/60"><div class="border-b border-slate-800 px-4 py-3"><h4 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-100">${escapeHtml(String(section?.title || 'Section') + weightLabel)}</h4></div><div class="overflow-x-auto"><table class="min-w-full text-left text-sm text-slate-200"><thead class="bg-slate-950/70 text-xs uppercase text-slate-400"><tr><th class="px-4 py-3">Major Output</th><th class="px-4 py-3">Target Summary</th><th class="px-4 py-3">Timeline</th></tr></thead><tbody class="divide-y divide-slate-800">${rowsHtml}</tbody></table></div></div>`;
            }).join('');
        }
        openPreviewModal('pmt-ipcr-preview-modal');
    }

    document.querySelector('[data-open-smpor-preview]')?.addEventListener('click', () => {
        if (currentPayload) renderSmporPreview(currentPayload);
    });

    document.querySelector('[data-open-ipcr-preview]')?.addEventListener('click', () => {
        if (currentPayload) renderIpcrPreview(currentPayload);
    });

    smporTabButtons.forEach((button) => {
        button.addEventListener('click', () => setSmporTab(button.dataset.smporTab));
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => closePreviewModal(button.closest('[data-preview-modal]')));
    });
});
</script>
@endpush
@endsection
