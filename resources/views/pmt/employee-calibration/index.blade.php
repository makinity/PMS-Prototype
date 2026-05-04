@extends('layouts.pmt')

@section('main-content')
    @php
        $submissionRows = is_array($rows ?? null) ? $rows : [];
        $periodLabelSafe = (string) ($periodLabel ?? '--');
        $searchTermSafe = trim((string) ($search ?? ''));

        $statusBadgeClassMap = [
            'pending_pmt_calibration' => 'inline-flex items-center rounded-full border border-sky-500/40 bg-sky-500/10 px-2.5 py-1 text-xs font-semibold text-sky-200',
            'approved_by_pmt' => 'inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-200',
            'adjusted_by_pmt' => 'inline-flex items-center rounded-full border border-violet-500/40 bg-violet-500/10 px-2.5 py-1 text-xs font-semibold text-violet-200',
            'released_by_pmt' => 'inline-flex items-center rounded-full border border-cyan-500/40 bg-cyan-500/10 px-2.5 py-1 text-xs font-semibold text-cyan-200',
            'returned_by_pmt' => 'inline-flex items-center rounded-full border border-rose-500/40 bg-rose-500/10 px-2.5 py-1 text-xs font-semibold text-rose-200',
        ];
    @endphp

    <section class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-white">IPCR Final Calibration</h1>
                <p class="text-sm text-slate-400">Calibrate recommended IPCR ratings, then explicitly release official results to employees and offices.</p>
                <p class="mt-1 text-xs text-slate-500">Active Performance Period: {{ $periodLabelSafe }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Records</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ count($submissionRows) }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
            <div class="border-b border-slate-800 px-5 py-4">
                <form method="GET" class="flex w-full max-w-xl items-end gap-2">
                    <div class="flex-1">
                        <label for="pmt-calibration-search" class="mb-2 block text-xs uppercase tracking-[0.14em] text-slate-400">Search</label>
                        <input
                            id="pmt-calibration-search"
                            name="search"
                            type="text"
                            value="{{ $searchTermSafe }}"
                            data-live-search
                            placeholder="Search employee, office, period, status..."
                            style="background-color: #020617 !important; color: #f1f5f9 !important;"
                            class="w-full rounded-xl border border-slate-700 px-4 py-3 text-sm placeholder:text-slate-500 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                    </div>
                    <button
                        type="submit"
                        aria-label="Search records"
                        style="background-color: #020617 !important; color: #f1f5f9 !important;"
                        class="inline-flex h-[50px] w-[50px] items-center justify-center rounded-xl border border-slate-700 text-slate-100 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path stroke-linecap="round" d="M20 20l-3.5-3.5"></path>
                        </svg>
                    </button>
                    @if ($searchTermSafe !== '')
                        <a href="{{ route('pmt.employee-calibration.index') }}"
                            class="inline-flex h-[50px] items-center rounded-xl border border-slate-700 px-4 text-sm font-medium text-slate-300 transition hover:bg-slate-800">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/70 text-xs uppercase tracking-[0.14em] text-slate-400">
                        <tr>
                            <th class="px-5 py-3 text-left">Employee</th>
                            <th class="px-5 py-3 text-left">Office</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-left">Computed Score</th>
                            <th class="px-5 py-3 text-left">Adjusted Score</th>
                            <th class="px-5 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($submissionRows as $row)
                            @php
                                $statusKey = strtolower((string) ($row['status'] ?? 'pending_pmt_calibration'));
                                $statusBadgeClasses = $statusBadgeClassMap[$statusKey] ?? 'inline-flex items-center rounded-full border border-slate-600 bg-slate-800/70 px-2.5 py-1 text-xs font-semibold text-slate-200';
                                $rowSearchText = collect([
                                    $row['employee_name'] ?? '',
                                    $row['office_name'] ?? '',
                                    $row['status_label'] ?? 'Pending',
                                ])->filter()->implode(' ');
                            @endphp
                            <tr class="bg-slate-900/40"
                                data-review-row
                                data-search-text="{{ \Illuminate\Support\Str::lower($rowSearchText) }}">
                                <td class="px-5 py-3 font-semibold text-slate-100">{{ $row['employee_name'] ?? '--' }}</td>
                                <td class="px-5 py-3">{{ $row['office_name'] ?? '--' }}</td>
                                <td class="px-5 py-3">
                                    <span class="{{ $statusBadgeClasses }}">{{ $row['status_label'] ?? 'Pending' }}</span>
                                </td>
                                <td class="px-5 py-3 font-medium text-slate-300">{{ $row['computed_score'] !== null ? number_format($row['computed_score'], 2) : '--' }}</td>
                                <td class="px-5 py-3 font-medium text-blue-300">{{ $row['adjusted_score'] !== null ? number_format($row['adjusted_score'], 2) : '--' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <button type="button"
                                            data-open-submission
                                            data-submission-id="{{ $row['id'] }}"
                                            aria-label="View submission"
                                            class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-2.5 py-2 text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                        Calibrate
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-slate-900/40">
                                <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-400">No IPCRs pending calibration, calibrated, or released found for the active period.</td>
                            </tr>
                        @endforelse
                        <tr id="pmt-submissions-no-match-row" class="hidden bg-slate-900/40">
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-400">No matching IPCRs found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script id="pmt-submissions-json" type="application/json">{!! json_encode($submissionPayloads ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

    <div id="pmt-submission-view-modal" data-preview-modal class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-sky-300">PMT Calibration</p>
                    <h3 class="text-lg font-semibold text-white">Employee Performance Calibration</h3>
                    <p class="mt-1 text-sm text-slate-400">Review PMT-recommended IPCR ratings, calibrate them, and release official results only after final PMT action.</p>
                </div>
                <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
            </div>

            <div class="mt-4 max-h-[68vh] space-y-4 overflow-y-auto pr-1 text-sm text-slate-200">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Status</p>
                        <div class="mt-2">
                            <span id="viewSubmissionStatus"
                                class="inline-flex items-center rounded-full border border-slate-600 bg-slate-800/70 px-3 py-1 text-xs font-semibold text-slate-200">--</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Computed Score</p>
                            <p id="viewSubmissionComputedScore" class="mt-2 text-xl font-semibold text-white">--</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Adjusted Score</p>
                            <p id="viewSubmissionAdjustedScore" class="mt-2 text-xl font-semibold text-blue-300">--</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Employee</p>
                        <p id="viewSubmissionEmployee" class="mt-2 text-base font-semibold text-white">--</p>
                    </div>

                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Office</p>
                        <p id="viewSubmissionOffice" class="mt-2 text-base font-semibold text-white">--</p>
                    </div>

                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Period</p>
                        <p id="viewSubmissionPeriod" class="mt-2 text-base font-semibold text-white">--</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">PMT Remarks</p>
                    <p id="viewSubmissionPmtRemarks" class="mt-2 whitespace-pre-line text-sm text-slate-200">--</p>
                </div>
                
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">PMT Adjustment Reason</p>
                    <p id="viewSubmissionAdjustmentReason" class="mt-2 whitespace-pre-line text-sm text-slate-200">--</p>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-semibold text-white">SMPOR - Monitoring Summary</h4>
                                <p id="viewSmporSource" class="mt-1 text-xs text-slate-400">Submitted MPORs snapshot.</p>
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

                <!-- Calibration Forms -->
                <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2" id="pmtCalibrationFormsContainer">
                    <div class="rounded-xl border border-slate-700 bg-slate-900/80 p-4">
                        <h4 class="text-sm font-semibold text-white">Adjust Rating</h4>
                        <form method="POST"
                            id="pmtSubmissionAdjustForm"
                            data-action-template="{{ route('pmt.employee-calibration.adjust', ['ipcr' => '__SUBMISSION_ID__']) }}"
                            action="{{ route('pmt.employee-calibration.adjust', ['ipcr' => 0]) }}"
                            class="mt-3 space-y-3">
                            @csrf
                            <div>
                                <label class="text-xs text-slate-400">Adjusted Score</label>
                                <input type="number" step="0.01" min="1" max="5" name="adjusted_score" id="adjustScoreInput" 
                                    style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                    class="w-full rounded border px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400">Adjusted Rating</label>
                                <select name="adjusted_rating" id="adjustRatingInput" 
                                    style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                    class="w-full rounded border px-3 py-2 text-sm" required>
                                    <option value="Outstanding">Outstanding</option>
                                    <option value="Very Satisfactory">Very Satisfactory</option>
                                    <option value="Satisfactory">Satisfactory</option>
                                    <option value="Unsatisfactory">Unsatisfactory</option>
                                    <option value="Poor">Poor</option>
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

                    <div class="rounded-xl border border-slate-700 bg-slate-900/80 p-4">
                        <h4 class="text-sm font-semibold text-white">Approve / Release / Return</h4>
                        <form method="POST"
                            id="pmtSubmissionApproveForm"
                            data-action-template="{{ route('pmt.employee-calibration.approve', ['ipcr' => '__SUBMISSION_ID__']) }}"
                            action="{{ route('pmt.employee-calibration.approve', ['ipcr' => 0]) }}"
                            class="mt-3 space-y-3">
                            @csrf
                            <div>
                                <label class="text-xs text-slate-400">PMT Remarks</label>
                                <textarea name="remarks" id="pmtRemarksInput" rows="3" 
                                    style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                    class="w-full rounded border px-3 py-2 text-sm"></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" id="pmtReturnBtn" class="rounded border border-rose-600 bg-rose-600/20 px-4 py-2 text-sm font-semibold text-rose-300 hover:bg-rose-600/30">Return</button>
                                <button type="button" id="pmtReleaseBtn" class="rounded border border-cyan-600 bg-cyan-600/20 px-4 py-2 text-sm font-semibold text-cyan-300 hover:bg-cyan-600/30">Release Official Result</button>
                                <button type="submit" class="rounded border border-emerald-600 bg-emerald-600/20 px-4 py-2 text-sm font-semibold text-emerald-300 hover:bg-emerald-600/30">Approve As Is</button>
                            </div>
                        </form>
                        <form method="POST"
                            id="pmtSubmissionReturnForm"
                            data-action-template="{{ route('pmt.employee-calibration.return', ['ipcr' => '__SUBMISSION_ID__']) }}"
                            action="{{ route('pmt.employee-calibration.return', ['ipcr' => 0]) }}"
                            class="hidden">
                            @csrf
                            <input type="hidden" name="remarks" id="pmtReturnRemarksInput">
                        </form>
                        <form method="POST"
                            id="pmtSubmissionReleaseForm"
                            data-action-template="{{ route('pmt.employee-calibration.release', ['ipcr' => '__SUBMISSION_ID__']) }}"
                            action="{{ route('pmt.employee-calibration.release', ['ipcr' => 0]) }}"
                            class="hidden">
                            @csrf
                            <input type="hidden" name="remarks" id="pmtReleaseRemarksInput">
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-4 border-t border-slate-800 pt-4">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    <!-- Smpor and Ipcr Preview Modals (Kept same as acc-review) -->
    <div id="pmt-smpor-preview-modal" data-preview-modal data-parent-modal-id="pmt-submission-view-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/60 px-4 py-6">
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

    <div id="pmt-ipcr-preview-modal" data-preview-modal data-parent-modal-id="pmt-submission-view-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 px-4 py-6">
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if (!empty($infoMessage ?? null))
                    if (window.PMSnackbar && !window.PMSnackbar.hasActive()) {
                        window.PMSnackbar.show({
                            type: 'info',
                            message: @json((string) $infoMessage),
                        });
                    }
                @endif

                const payloadScript = document.getElementById('pmt-submissions-json');
                const liveSearchInput = document.querySelector('[data-live-search]');
                const submissionRows = Array.from(document.querySelectorAll('[data-review-row]'));
                const noMatchRow = document.getElementById('pmt-submissions-no-match-row');
                const previewModals = Array.from(document.querySelectorAll('[data-preview-modal]'));
                const openPreviewStack = [];

                const submissionEmployeeEl = document.getElementById('viewSubmissionEmployee');
                const submissionOfficeEl = document.getElementById('viewSubmissionOffice');
                const submissionPeriodEl = document.getElementById('viewSubmissionPeriod');
                const submissionStatusEl = document.getElementById('viewSubmissionStatus');
                const submissionComputedScoreEl = document.getElementById('viewSubmissionComputedScore');
                const submissionAdjustedScoreEl = document.getElementById('viewSubmissionAdjustedScore');
                const submissionPmtRemarksEl = document.getElementById('viewSubmissionPmtRemarks');
                const submissionAdjustmentReasonEl = document.getElementById('viewSubmissionAdjustmentReason');
                const releaseButtonEl = document.getElementById('pmtReleaseBtn');
                
                const adjustFormEl = document.getElementById('pmtSubmissionAdjustForm');
                const approveFormEl = document.getElementById('pmtSubmissionApproveForm');
                const returnFormEl = document.getElementById('pmtSubmissionReturnForm');
                const releaseFormEl = document.getElementById('pmtSubmissionReleaseForm');
                const adjustScoreInput = document.getElementById('adjustScoreInput');
                
                const smporQuantityPanelEl = document.getElementById('smporQuantityPanel');
                const smporQualityPanelEl = document.getElementById('smporQualityPanel');
                const smporTimelinessPanelEl = document.getElementById('smporTimelinessPanel');
                const smporTabButtons = Array.from(document.querySelectorAll('[data-smpor-tab]'));
                const smporTabPanels = Array.from(document.querySelectorAll('[data-smpor-tab-panel]'));
                const ipcrSectionsContainerEl = document.getElementById('ipcrSectionsContainer');

                let payloadMap = {};
                let currentPayload = null;

                function applySubmissionLiveSearch() {
                    if (!liveSearchInput) return;
                    const term = String(liveSearchInput.value || '').trim().toLowerCase();
                    let visibleCount = 0;
                    submissionRows.forEach((row) => {
                        const haystack = String(row.dataset.searchText || '').toLowerCase();
                        const isVisible = term === '' || haystack.includes(term);
                        row.classList.toggle('hidden', !isVisible);
                        if (isVisible) visibleCount += 1;
                    });
                    if (noMatchRow) noMatchRow.classList.toggle('hidden', visibleCount > 0 || submissionRows.length === 0);
                }

                try {
                    const parsed = JSON.parse(payloadScript?.textContent || '{}');
                    payloadMap = parsed && typeof parsed === 'object' ? parsed : {};
                } catch (error) { payloadMap = {}; }

                function escapeHtml(value) {
                    return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
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

                function formatNumber(value, fixed = null) {
                    const numeric = Number(value ?? 0);
                    if (!Number.isFinite(numeric)) return fixed === 2 ? '0.00' : '0';
                    if (fixed === 2) return numeric.toFixed(2);
                    if (Math.floor(numeric) === numeric) return String(numeric);
                    return numeric.toFixed(2).replace(/\.00$/, '').replace(/(\.\d*[1-9])0+$/, '$1');
                }

                function statusBadgeClasses(status) {
                    switch ((status || '').toLowerCase()) {
                        case 'pending_pmt_calibration': return 'inline-flex items-center rounded-full border border-sky-500/40 bg-sky-500/10 px-2.5 py-1 text-xs font-semibold text-sky-200';
                        case 'approved_by_pmt': return 'inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-200';
                        case 'adjusted_by_pmt': return 'inline-flex items-center rounded-full border border-violet-500/40 bg-violet-500/10 px-2.5 py-1 text-xs font-semibold text-violet-200';
                        case 'released_by_pmt': return 'inline-flex items-center rounded-full border border-cyan-500/40 bg-cyan-500/10 px-2.5 py-1 text-xs font-semibold text-cyan-200';
                        case 'returned_by_pmt': return 'inline-flex items-center rounded-full border border-rose-500/40 bg-rose-500/10 px-2.5 py-1 text-xs font-semibold text-rose-200';
                        default: return 'inline-flex items-center rounded-full border border-slate-600 bg-slate-800/70 px-2.5 py-1 text-xs font-semibold text-slate-200';
                    }
                }

                function renderSubmissionModal(payload) {
                    currentPayload = payload || null;
                    if (!currentPayload) return;

                    if (submissionEmployeeEl) submissionEmployeeEl.textContent = payload.employee_name || '--';
                    if (submissionOfficeEl) submissionOfficeEl.textContent = payload.office_name || '--';
                    if (submissionPeriodEl) submissionPeriodEl.textContent = payload.period_label || '--';
                    if (submissionComputedScoreEl) submissionComputedScoreEl.textContent = payload.computed_score !== null ? formatNumber(payload.computed_score, 2) : '--';
                    if (submissionAdjustedScoreEl) submissionAdjustedScoreEl.textContent = payload.adjusted_score !== null ? formatNumber(payload.adjusted_score, 2) : '--';
                    if (submissionPmtRemarksEl) submissionPmtRemarksEl.textContent = (payload.pmt_remarks || '').trim() !== '' ? payload.pmt_remarks : '--';
                    if (submissionAdjustmentReasonEl) submissionAdjustmentReasonEl.textContent = (payload.adjustment_reason || '').trim() !== '' ? payload.adjustment_reason : '--';

                    if (submissionStatusEl) {
                        submissionStatusEl.className = statusBadgeClasses(payload.status || 'draft');
                        submissionStatusEl.textContent = payload.status_label || 'Draft';
                    }
                    
                    if (adjustScoreInput && payload.computed_score !== null) {
                        adjustScoreInput.value = payload.computed_score;
                    }

                    const submissionId = String(payload.id ?? '').trim();
                    [adjustFormEl, approveFormEl, returnFormEl, releaseFormEl].forEach(form => {
                        if (form) {
                            const actionTemplate = String(form.dataset.actionTemplate || '');
                            if (submissionId !== '' && actionTemplate.includes('__SUBMISSION_ID__')) {
                                form.action = actionTemplate.replace('__SUBMISSION_ID__', encodeURIComponent(submissionId));
                            }
                        }
                    });

                    if (releaseButtonEl) {
                        const status = String(payload.status || '').toLowerCase();
                        const canRelease = status === 'approved_by_pmt' || status === 'adjusted_by_pmt';
                        releaseButtonEl.disabled = !canRelease;
                        releaseButtonEl.classList.toggle('opacity-50', !canRelease);
                        releaseButtonEl.classList.toggle('cursor-not-allowed', !canRelease);
                    }

                    openPreviewModal('pmt-submission-view-modal');
                }

                document.getElementById('pmtReturnBtn')?.addEventListener('click', () => {
                    const remarks = document.getElementById('pmtRemarksInput')?.value || '';
                    if (!remarks.trim()) {
                        alert('Please provide PMT Remarks before returning.');
                        return;
                    }
                    document.getElementById('pmtReturnRemarksInput').value = remarks;
                    returnFormEl.submit();
                });

                document.getElementById('pmtReleaseBtn')?.addEventListener('click', () => {
                    const remarks = document.getElementById('pmtRemarksInput')?.value || '';
                    document.getElementById('pmtReleaseRemarksInput').value = remarks;
                    releaseFormEl.submit();
                });

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

                document.querySelectorAll('[data-open-submission]').forEach((trigger) => {
                    trigger.addEventListener('click', () => {
                        const submissionId = String(trigger.dataset.submissionId || '').trim();
                        if (submissionId === '') return;
                        const payload = payloadMap?.[submissionId] || null;
                        if (!payload) return;
                        renderSubmissionModal(payload);
                    });
                });

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
                
                liveSearchInput?.addEventListener('input', applySubmissionLiveSearch);
                applySubmissionLiveSearch();
                refreshPreviewModalZIndices();
            });
        </script>
    @endpush
@endsection
