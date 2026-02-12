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
                    <p class="text-sm text-slate-400">System-generated summary based on MPOR (derived from submitted + rated ORS). Read-only.</p>
                </div>
                <div class="flex gap-2">
                    <!-- kani na button -->
                    <a href="#"
                       data-open-modal="smpor-preview-modal"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800 transition">
                        View SMPOR
                    </a>
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
                    <p class="mt-1 font-semibold">MPOR (derived from submitted + rated ORS)</p>
                </div>
            </div>
        </div>

        <!-- IPCR Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">IPCR Accomplishment Report</h2>
                    <p class="text-sm text-slate-400">System-generated accomplishments derived from SMPOR consolidation. Read-only.</p>
                </div>
                <div class="flex gap-2">
                    <!-- kani na button -->
                    <a href="#"
                       data-open-modal="ipcr-preview-modal"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800 transition">
                        View IPCR Accomplishment
                    </a>
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
                    <p class="mt-1 font-semibold">SMPOR (monitoring consolidation)</p>
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
                Back
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

    <!-- SMPOR Preview Modal -->
    <div id="smpor-preview-modal"
         data-preview-modal
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">SMPOR (Monitoring Summary)</p>
                    <h3 class="text-lg font-semibold text-white">SMPOR Preview &mdash; January&ndash;June 2026</h3>
                    <p class="text-sm text-slate-400 mt-1">System-generated, monitoring-only. Derived from MPOR (submitted + rated ORS).</p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 space-y-5 max-h-[65vh] overflow-y-auto text-sm text-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Employee</p>
                        <p class="mt-1 font-semibold">Ramon Reyes</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office/Unit</p>
                        <p class="mt-1 font-semibold">Revenue Collection Unit</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Period</p>
                        <p class="mt-1 font-semibold">January&ndash;June 2026</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Source</p>
                        <p class="mt-1 font-semibold">MPOR (submitted + rated ORS)</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-base font-semibold text-white">Monitoring Totals</h4>
                        <span class="text-xs text-slate-400">Quality Points = Quantity &times; Quality Rating &middot; Timeliness Points = Quantity &times; Timeliness Rating</span>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-slate-800">
                        <table class="min-w-full text-left text-sm text-slate-200">
                            <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-4 py-3">MFO</th>
                                    <th class="px-4 py-3 text-right">Total Quantity (Monitoring)</th>
                                    <th class="px-4 py-3 text-right">Total Quality Points</th>
                                    <th class="px-4 py-3 text-right">Total Timeliness Points</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr class="bg-slate-900/40">
                                    <td class="px-4 py-3 font-semibold">E-Bank Scanning and Encoding of Revenue Transactions</td>
                                    <td class="px-4 py-3 text-right">1</td>
                                    <td class="px-4 py-3 text-right">5</td>
                                    <td class="px-4 py-3 text-right">5</td>
                                </tr>
                                <tr class="bg-slate-900/30">
                                    <td class="px-4 py-3 font-semibold">Processing of Over-the-Counter Revenue Transactions</td>
                                    <td class="px-4 py-3 text-right">12</td>
                                    <td class="px-4 py-3 text-right">60</td>
                                    <td class="px-4 py-3 text-right">60</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-slate-950/70">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-100">Grand Total</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-100">13</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-100">65</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-100">65</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- IPCR Preview Modal -->
    <div id="ipcr-preview-modal"
         data-preview-modal
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR Accomplishment Report</p>
                    <h3 class="text-lg font-semibold text-white">IPCR Accomplishment Preview &mdash; January&ndash;June 2026</h3>
                    <p class="text-sm text-slate-400 mt-1">System-generated accomplishments derived from SMPOR consolidation (Stage II).</p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 space-y-5 max-h-[65vh] overflow-y-auto text-sm text-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Employee</p>
                        <p class="mt-1 font-semibold">Ramon Reyes</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office/Unit</p>
                        <p class="mt-1 font-semibold">Revenue Collection Unit</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Period</p>
                        <p class="mt-1 font-semibold">January&ndash;June 2026</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Source</p>
                        <p class="mt-1 font-semibold">SMPOR (monitoring consolidation)</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="text-base font-semibold text-white">Accomplishment Summary</h4>
                    <div class="overflow-x-auto rounded-xl border border-slate-800">
                        <table class="min-w-full text-left text-sm text-slate-200">
                            <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-4 py-3">MFO</th>
                                    <th class="px-4 py-3">Accomplishment Summary</th>
                                    <th class="px-4 py-3">Evidence</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr class="bg-slate-900/40">
                                    <td class="px-4 py-3 font-semibold">E-Bank Scanning and Encoding of Revenue Transactions</td>
                                    <td class="px-4 py-3 text-slate-300">Completed 1 e-bank scanning and encoding transaction within the period; monitoring reflects full quality and timeliness compliance based on rated ORS.</td>
                                    <td class="px-4 py-3 text-slate-300">Attached (reference)</td>
                                </tr>
                                <tr class="bg-slate-900/30">
                                    <td class="px-4 py-3 font-semibold">Processing of Over-the-Counter Revenue Transactions</td>
                                    <td class="px-4 py-3 text-slate-300">Processed 12 over-the-counter revenue transactions; monitoring indicates consistent quality and timeliness adherence across consolidated ORS entries.</td>
                                    <td class="px-4 py-3 text-slate-300">Attached (reference)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 text-xs text-slate-400">
                        Final IPCR rating is completed in Stage III. This preview is monitoring-derived and read-only.
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

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
                    <span data-modal-label>Submit Accomplishments</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('action-modal');
                const previewModals = Array.from(document.querySelectorAll('[data-preview-modal]'));
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
                    'confirm-submission': {
                        title: 'Submit Accomplishments',
                        body: 'Confirm formal submission of SMPOR & IPCR accomplishments for Stage III evaluation. After submission, uploads and remarks become read-only.',
                        showConfirm: true,
                    },
                };

                function isAnyModalOpen() {
                    const actionOpen = modal && modal.classList.contains('flex');
                    const previewOpen = previewModals.some((item) => item.classList.contains('flex'));
                    return actionOpen || previewOpen;
                }

                function syncBodyScroll() {
                    document.body.classList.toggle('overflow-hidden', isAnyModalOpen());
                }

                function setModalState(show) {
                    if (!modal) return;
                    modal.classList.toggle('hidden', !show);
                    modal.classList.toggle('flex', show);
                    syncBodyScroll();
                }

                function setSubmitState(submitted) {
                    if (statusBadge) {
                        statusBadge.textContent = submitted ? 'Submitted for supervisor approval' : 'Draft';
                        statusBadge.className = submitted
                            ? 'inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200 border border-emerald-500/40'
                            : 'inline-flex items-center rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-200 border border-amber-500/40';
                    }
                    if (supportingFiles) {
                        supportingFiles.disabled = submitted;
                        supportingFiles.classList.toggle('opacity-60', submitted);
                        supportingFiles.classList.toggle('cursor-not-allowed', submitted);
                    }
                    if (remarks) {
                        remarks.disabled = submitted;
                        remarks.classList.toggle('opacity-70', submitted);
                        remarks.classList.toggle('cursor-not-allowed', submitted);
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
                    if (modalLabel) modalLabel.textContent = 'Submit Accomplishments';
                }

                function openPreviewModal(modalId) {
                    if (!modalId) return;
                    const target = document.getElementById(modalId);
                    if (!target) return;
                    previewModals.forEach((item) => {
                        if (item !== target) {
                            item.classList.add('hidden');
                            item.classList.remove('flex');
                        }
                    });
                    target.classList.remove('hidden');
                    target.classList.add('flex');
                    syncBodyScroll();
                }

                function closePreviewModal(modalEl) {
                    if (!modalEl) return;
                    modalEl.classList.add('hidden');
                    modalEl.classList.remove('flex');
                    syncBodyScroll();
                }

                function closeAllPreviewModals() {
                    previewModals.forEach((item) => closePreviewModal(item));
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

                document.querySelectorAll('[data-open-modal]').forEach((trigger) => {
                    trigger.addEventListener('click', (e) => {
                        e.preventDefault();
                        openPreviewModal(trigger.getAttribute('data-open-modal'));
                    });
                });

                document.querySelectorAll('[data-close-modal]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const modalEl = btn.closest('[data-preview-modal]');
                        closePreviewModal(modalEl);
                    });
                });

                previewModals.forEach((previewModal) => {
                    previewModal.addEventListener('click', (e) => {
                        if (e.target === previewModal) closePreviewModal(previewModal);
                    });
                });

                modalConfirm?.addEventListener('click', handleConfirm);
                modalClose?.addEventListener('click', closeModal);
                modalCancel?.addEventListener('click', closeModal);
                modal?.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal();
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        closeAllPreviewModals();
                        closeModal();
                    }
                });
            });
        </script>
    @endpush

@endsection
