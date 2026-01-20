<x-layouts.pmt>
    <section class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stage III – PMT Final Calibration</p>
                <h1 class="mt-1 text-2xl font-bold text-white">IPCR Calibration</h1>
                <p class="text-sm text-slate-400 mt-1">Rating Period: January – June 2026 • Status: Recommended (Pre-Calibration)</p>
                <p class="text-xs text-slate-500 mt-1">No re-entry of SMPOR data. Calibration adjustments are final and fully logged.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" data-calib-export class="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-800">
                    <i class="fa-solid fa-file-export text-slate-300"></i>
                    <span>Export Calibration Summary</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-white">Office / Unit Comparison (Reference)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs text-slate-200">
                        <thead class="bg-slate-800/60">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Office / Unit</th>
                                <th class="px-3 py-2 text-left font-semibold">Average Rating</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr><td class="px-3 py-2">Revenue Collection Unit</td><td class="px-3 py-2">5.00</td></tr>
                            <tr><td class="px-3 py-2">Records Management Unit</td><td class="px-3 py-2">4.25</td></tr>
                            <tr class="bg-slate-800/40 font-semibold"><td class="px-3 py-2">Organization Average</td><td class="px-3 py-2">4.63</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-xs text-slate-400 space-y-1">
                    <p>✔ High concentration at 5.00</p>
                    <p>✔ Revenue Collection Unit at upper bound</p>
                    <p>✔ Potential rating compression</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Calibration Table (Recommended IPCRs)</h3>
                <span class="text-[11px] text-slate-400">Rating Period: January – June 2026</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-800/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Employee Name</th>
                            <th class="px-4 py-3 text-left font-semibold">Office / Unit</th>
                            <th class="px-4 py-3 text-left font-semibold">Position</th>
                            <th class="px-4 py-3 text-center font-semibold">Supervisor Overall Rating</th>
                            <th class="px-4 py-3 text-center font-semibold">PMT Calibrated Rating</th>
                            <th class="px-4 py-3 text-center font-semibold">Delta (±)</th>
                            <th class="px-4 py-3 text-left font-semibold">Calibration Justification</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        <tr data-calib-row class="hover:bg-slate-800/40">
                            <td class="px-4 py-3">Ramon Reyes</td>
                            <td class="px-4 py-3 text-slate-300">Revenue Collection Unit</td>
                            <td class="px-4 py-3 text-slate-300">Records Management Officer</td>
                            <td class="px-4 py-3 text-center font-semibold text-emerald-300">5.00</td>
                            <td class="px-4 py-3 text-center">
                                <input style="background:#0f172a;color:#e5e7eb;" data-calib-input type="number" min="1" max="5" step="0.25" value="5.00" class="w-24 rounded border border-slate-700 bg-slate-800 text-center text-white text-sm">
                            </td>
                            <td class="px-4 py-3 text-center font-semibold" data-calib-delta>+0.00</td>
                            <td class="px-4 py-3">
                                <textarea style="background:#0f172a;color:#e5e7eb;" data-calib-justification class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 h-16" placeholder="Justification is required for any calibration adjustment."></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Justification is required for any calibration adjustment. All changes are logged for audit.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="sticky bottom-0 left-0 right-0 mt-6 border-t border-slate-800 bg-slate-900/80 backdrop-blur p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-xs text-slate-400">No re-entry of SMPOR data. Calibration adjustments are final and fully logged.</div>
        <div class="flex flex-wrap items-center gap-3">
            <button type="button"
                    data-calib-action
                    data-action="apply"
                    data-employee-action
                    data-action-title="Apply Calibration"
                    data-action-message="Applying calibration will lock all adjusted ratings and finalize calibration."
                    data-action-confirm="Apply"
                    data-action-loading="Applying..."
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                <span data-button-label>Apply Calibration</span>
                <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>
            <button type="button"
                    data-calib-action
                    data-action="reset"
                    data-employee-action
                    data-action-title="Reset Calibration Changes"
                    data-action-message="This will revert all PMT calibration adjustments to supervisor ratings."
                    data-action-confirm="Reset"
                    data-action-loading="Resetting..."
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800">
                <span data-button-label>Reset Changes</span>
                <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
            </button>

            <a
                class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800"
                href="{{ route('pmt.ipcr-calib-overview') }}">
                Back
            </a>
        </div>
    </div>

    <div id="calib-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="calib-modal-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="calib-modal-body" class="mt-1 text-sm text-slate-400">Prototype action preview.</p>
                </div>
                <button type="button" data-calib-modal-close class="text-slate-400 hover:text-white">x</button>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-calib-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
                <button type="button" id="calib-modal-confirm" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <span data-button-label>Proceed</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                function setButtonLoading(button, isLoading, loadingText) {
                    if (!button) return;
                    const label = button.querySelector('[data-button-label]');
                    const spinner = button.querySelector('[data-button-spinner]');
                    if (label && !button.dataset.originalLabel) {
                        button.dataset.originalLabel = label.textContent.trim();
                    }
                    if (isLoading) {
                        button.disabled = true;
                        button.classList.add('opacity-70', 'cursor-wait');
                        if (spinner) spinner.classList.remove('hidden');
                        if (label && loadingText) label.textContent = loadingText;
                    } else {
                        button.disabled = false;
                        button.classList.remove('opacity-70', 'cursor-wait');
                        if (spinner) spinner.classList.add('hidden');
                        if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
                    }
                }

                document.querySelectorAll('[data-employee-action]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const loadingText = btn.dataset.actionLoading || 'Working...';
                        setButtonLoading(btn, true, loadingText);
                        setTimeout(() => setButtonLoading(btn, false), 1000);
                    });
                });
            });
        </script>
    @endpush
</x-layouts.pmt>
