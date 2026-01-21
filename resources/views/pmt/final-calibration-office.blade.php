<x-layouts.pmt>
    <section class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Final Calibration – Office Review</p>
                <h1 class="text-2xl font-semibold text-white">Revenue Collection Unit</h1>
                <p class="text-sm text-slate-400">Period: January – June 2026</p>
                <p class="text-[11px] text-slate-500 mt-1">
                    This is the final opportunity to adjust ratings for equity purposes. All changes require justification.
                </p>
            </div>
            <span id="office-status"
                  class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                Ready
            </span>
        </div>

        <!-- OFFICE SUMMARY STRIP -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="grid grid-cols-1 gap-3 text-sm text-slate-200 sm:grid-cols-2 md:grid-cols-4">
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Employees Rated</p>
                    <p class="text-xl font-semibold text-white">1</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Avg Final Rating</p>
                    <p class="text-xl font-semibold text-white">5.00</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Org Mean Comparison</p>
                    <p class="text-xl font-semibold text-white">Equal to organization mean</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Flag Status</p>
                    <p class="text-xl font-semibold text-white">None</p>
                </div>
            </div>
        </div>

        <!-- EMPLOYEE FINAL RATING TABLE -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Employee Final Ratings</h2>
                <span class="text-xs text-slate-400">Final adjustments require justification</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee Name</th>
                            <th class="px-4 py-3 text-left">Supervisor Rating</th>
                            <th class="px-4 py-3 text-left">PMT Calibrated Rating</th>
                            <th class="px-4 py-3 text-left">Final Rating</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">Juan Dela Cruz</td>
                            <td class="px-4 py-3">5.00</td>
                            <td class="px-4 py-3">5.00</td>
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="1" max="5" value="5.00"
                                        style="background:#0f172a;color:#e5e7eb;"
                                       id="final-rating"
                                       class="w-28 rounded-lg border border-slate-700 bg-slate-950 px-2 py-1 text-sm text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        id="adjust-btn"
                                        class="inline-flex items-center gap-2 rounded-lg border border-blue-500 text-blue-300 px-3 py-1.5 text-xs font-semibold hover:bg-blue-500/10 transition">
                                    Adjust
                                </button>
                                <div id="justification-status" class="mt-1 text-[11px] text-slate-400"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="flex flex-col items-end gap-2">
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('pmt.final-calib') }}"
                   class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Back to Overview
                </a>
                <button type="button"
                        id="apply-final"
                        disabled
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 transition disabled:opacity-60 disabled:cursor-not-allowed hover:bg-emerald-500">
                    Apply Final Adjustments
                </button>
            </div>
            <p class="text-[11px] text-slate-500">
                Finalizing ratings will permanently lock all results. No further edits will be allowed.
            </p>
        </div>

    </section>

    <!-- JUSTIFICATION MODAL -->
    <div id="justification-modal"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Justification Required</p>
                    <h3 class="text-lg font-semibold text-white">Final Rating Adjustment</h3>
                </div>
                <button type="button" id="justification-close" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 space-y-3 text-sm text-slate-200">
                <p>Provide a justification for adjusting the final rating. This is required for audit and equity purposes.</p>
                <textarea id="justification-text"
                          rows="3"
                          class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                          placeholder="Reason for adjustment and equity/normalization basis..."></textarea>
            </div>

            <div class="mt-4 flex justify-end gap-2 border-t border-slate-800 pt-4">
                <button type="button"
                        id="justification-cancel"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Cancel
                </button>
                <button type="button"
                        id="justification-save"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 transition">
                    Save Adjustment
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('justification-modal');
                const closeButtons = [document.getElementById('justification-close'), document.getElementById('justification-cancel')];
                const saveBtn = document.getElementById('justification-save');
                const applyBtn = document.getElementById('apply-final');
                const statusBadge = document.getElementById('office-status');
                const justificationText = document.getElementById('justification-text');
                const finalInput = document.getElementById('final-rating');
                const adjustBtn = document.getElementById('adjust-btn');
                const justificationStatus = document.getElementById('justification-status');

                const originalRating = '5.00';
                let pendingRating = originalRating;
                let justificationSaved = false;

                function openModal() {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    justificationText.value = '';
                }

                function closeModal() {
                    modal?.classList.add('hidden');
                    modal?.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                function updateApplyState() {
                    if (!applyBtn) return;
                    const ratingChanged = pendingRating !== originalRating;
                    applyBtn.disabled = ratingChanged && !justificationSaved;
                }

                finalInput?.addEventListener('change', () => {
                    pendingRating = finalInput.value || originalRating;
                    justificationSaved = false;
                    justificationStatus.textContent = '';
                    openModal();
                });

                adjustBtn?.addEventListener('click', () => {
                    pendingRating = finalInput?.value || originalRating;
                    justificationSaved = false;
                    justificationStatus.textContent = '';
                    openModal();
                });

                saveBtn?.addEventListener('click', () => {
                    if (!justificationText.value.trim()) {
                        justificationText.focus();
                        return;
                    }
                    justificationSaved = true;
                    justificationStatus.textContent = 'Justification saved for adjustment.';
                    updateApplyState();
                    closeModal();
                });

                closeButtons.forEach((btn) => {
                    btn?.addEventListener('click', () => {
                        if (pendingRating !== originalRating && !justificationSaved && finalInput) {
                            finalInput.value = originalRating;
                            pendingRating = originalRating;
                        }
                        updateApplyState();
                        closeModal();
                    });
                });

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        if (pendingRating !== originalRating && !justificationSaved && finalInput) {
                            finalInput.value = originalRating;
                            pendingRating = originalRating;
                        }
                        updateApplyState();
                        closeModal();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        if (pendingRating !== originalRating && !justificationSaved && finalInput) {
                            finalInput.value = originalRating;
                            pendingRating = originalRating;
                        }
                        updateApplyState();
                        closeModal();
                    }
                });

                applyBtn?.addEventListener('click', () => {
                    if (applyBtn.disabled) return;
                    applyBtn.disabled = true;
                    applyBtn.classList.add('opacity-70', 'cursor-not-allowed');
                    if (statusBadge) {
                        statusBadge.textContent = 'Ready';
                        statusBadge.className = 'rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200';
                    }
                });

                updateApplyState();
            });
        </script>
    @endpush
</x-layouts.pmt>
