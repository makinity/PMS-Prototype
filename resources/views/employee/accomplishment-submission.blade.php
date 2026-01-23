@extends('layouts.employee')

@section('main-content')
    <section class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-white">SMPOR &amp; IPCR Accomplishment Submission</h1>
                <p class="text-sm text-slate-400">Formal end-of-period submission of accomplishments</p>
                <p class="text-xs text-slate-500 mt-1">Performance Period: January&ndash;June 2026</p>
            </div>
            <div class="flex items-center gap-2">
                <span id="status-badge" class="inline-flex items-center rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-200 border border-amber-500/40">
                    Draft
                </span>
            </div>
        </div>

        <!-- SMPOR Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">SMPOR &ndash; Monitoring Summary</h2>
                    <p class="text-sm text-slate-400">System-generated summary based on validated MPOR. Read-only.</p>
                </div>
                <div class="flex gap-2">
                    <!-- kani na button -->
                    <button type="button"
                            data-action="view-smpor"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800 transition">
                        View SMPOR
                    </button>
                    <!-------------------->

                    <a href="{{ route('stage2.smpor.export.pdf') }}"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                            Export PDF
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-slate-200">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Period</p>
                    <p class="mt-1 font-semibold">January&ndash;June 2026</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Status</p>
                    <p class="mt-1 font-semibold">System-generated, monitoring-only</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Data Source</p>
                    <p class="mt-1 font-semibold">Validated MPOR (locked)</p>
                </div>
            </div>
        </div>

        <!-- IPCR Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">IPCR Accomplishment Report</h2>
                    <p class="text-sm text-slate-400">System-generated accomplishments derived from SMPOR. Read-only.</p>
                </div>
                <div class="flex gap-2">
                    <!-- kani na button -->
                    <button type="button"
                            data-action="view-ipcr-accomplishment"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800 transition">
                        View IPCR Accomplishment
                    </button>
                    <!-------------------->

                    <a href="{{ route('stage2.ipcr.export.pdf') }}"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                            Export PDF
                    </a>

                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-slate-200">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Rating Period</p>
                    <p class="mt-1 font-semibold">January&ndash;June 2026</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Status</p>
                    <p class="mt-1 font-semibold">System-generated, read-only</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Data Source</p>
                    <p class="mt-1 font-semibold">SMPOR (locked)</p>
                </div>
            </div>
        </div>

        <!-- Supporting Documents -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Supporting Documents (Optional)</h2>
                    <p class="text-sm text-slate-400">Uploads are optional and disabled after submission.</p>
                </div>
            </div>
            <input id="supporting-files"
                   type="file"
                   multiple
                   class="block w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-slate-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-200">
        </div>

        <!-- Remarks -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Employee Remarks (Optional)</h2>
                    <p class="text-sm text-slate-400">Remarks become read-only after submission.</p>
                </div>
            </div>
            <textarea id="employee-remarks"
                        style="background:#0f172a;color:#e5e7eb;"
                      rows="3"
                      class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500"
                      placeholder="Add clarifications or context (optional)"></textarea>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
            <button type="button"
                    class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                Cancel
            </button>
            <button type="button"
                    id="submit-accomplishments"
                    data-action="confirm-submission"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-emerald-600 transition">
                <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-emerald-900/30 border-t-emerald-900"></span>
                <span data-button-label>Submit Accomplishments</span>
            </button>
        </div>

    </section>

    <!-- Generic Modal -->
    <div id="action-modal"
         class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p id="modal-eyebrow" class="text-xs uppercase tracking-[0.2em] text-blue-300">Action</p>
                    <h3 id="modal-title" class="text-lg font-semibold text-white">--</h3>
                </div>
                <button type="button" id="modal-close" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 space-y-3 max-h-[60vh] overflow-y-auto text-sm text-slate-200">
                <p id="modal-body" class="text-slate-200"></p>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        id="modal-cancel"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
                <button type="button"
                        id="modal-confirm"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 transition">
                    <span data-modal-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                    <span data-modal-label>Proceed</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('action-modal');
                const modalTitle = document.getElementById('modal-title');
                const modalBody = document.getElementById('modal-body');
                const modalConfirm = document.getElementById('modal-confirm');
                const modalCancel = document.getElementById('modal-cancel');
                const modalClose = document.getElementById('modal-close');
                const modalSpinner = modalConfirm?.querySelector('[data-modal-spinner]');
                const modalLabel = modalConfirm?.querySelector('[data-modal-label]');
                const statusBadge = document.getElementById('status-badge');
                const submitBtn = document.getElementById('submit-accomplishments');
                const submitSpinner = submitBtn?.querySelector('[data-button-spinner]');
                const submitLabel = submitBtn?.querySelector('[data-button-label]');
                const supportingFiles = document.getElementById('supporting-files');
                const remarks = document.getElementById('employee-remarks');
                let activeAction = null;

                const modalContent = {
                    'view-smpor': {
                        title: 'System-Generated SMPOR',
                        body: 'Read-only summary of validated MPOR data for January–June 2026. No edits or ratings are allowed at this stage.',
                        showConfirm: false,
                    },
                    'view-ipcr-accomplishment': {
                        title: 'IPCR Accomplishment (System-Generated)',
                        body: 'Read-only accomplishments derived from SMPOR for January–June 2026. Targets and ratings are locked for Stage III evaluation.',
                        showConfirm: false,
                    },
                    'confirm-submission': {
                        title: 'Submit Accomplishments',
                        body: 'Confirm formal submission of SMPOR & IPCR accomplishments for Stage III evaluation. After submission, uploads and remarks become read-only.',
                        showConfirm: true,
                    },
                };

                function setModalState(show) {
                    if (!modal) return;
                    modal.classList.toggle('hidden', !show);
                    modal.classList.toggle('flex', show);
                    document.body.classList.toggle('overflow-hidden', show);
                }

                function setSubmitState(submitted) {
                    if (statusBadge) {
                        statusBadge.textContent = submitted ? 'Submitted for Stage III Evaluation' : 'Draft';
                        statusBadge.className = submitted
                            ? 'inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200 border border-emerald-500/40'
                            : 'inline-flex items-center rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-200 border border-amber-500/40';
                    }
                    if (supportingFiles) supportingFiles.disabled = submitted;
                    if (remarks) {
                        remarks.disabled = submitted;
                        remarks.classList.toggle('opacity-70', submitted);
                    }
                    if (submitBtn) {
                        submitBtn.disabled = submitted;
                        submitBtn.classList.toggle('opacity-70', submitted);
                        submitBtn.classList.toggle('cursor-not-allowed', submitted);
                    }
                }

                function openModal(action) {
                    const content = modalContent[action] || modalContent['confirm-submission'];
                    activeAction = action;
                    if (modalTitle) modalTitle.textContent = content.title;
                    if (modalBody) modalBody.textContent = content.body;
                    if (modalConfirm) modalConfirm.classList.toggle('hidden', !content.showConfirm);
                    setModalState(true);
                }

                function closeModal() {
                    setModalState(false);
                    activeAction = null;
                    if (modalSpinner) modalSpinner.classList.add('hidden');
                    if (modalLabel) modalLabel.textContent = 'Proceed';
                }

                function handleConfirm() {
                    if (activeAction !== 'confirm-submission' || !submitBtn) {
                        closeModal();
                        return;
                    }
                    if (modalSpinner) modalSpinner.classList.remove('hidden');
                    if (modalLabel) modalLabel.textContent = 'Submitting...';
                    if (submitSpinner) submitSpinner.classList.remove('hidden');
                    if (submitLabel) submitLabel.textContent = 'Submitting...';
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-70', 'cursor-wait');

                    setTimeout(() => {
                        setSubmitState(true);
                        if (submitSpinner) submitSpinner.classList.add('hidden');
                        if (submitLabel) submitLabel.textContent = 'Submit Accomplishments';
                        submitBtn.classList.remove('cursor-wait');
                        closeModal();
                    }, 900);
                }

                document.querySelectorAll('[data-action]').forEach((btn) => {
                    btn.addEventListener('click', () => openModal(btn.dataset.action));
                });

                modalConfirm?.addEventListener('click', handleConfirm);
                modalClose?.addEventListener('click', closeModal);
                modalCancel?.addEventListener('click', closeModal);
                modal?.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal();
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') closeModal();
                });
            });
        </script>
    @endpush

@endsection
