<x-layouts.pmt>
    <section class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage IV – Performance Rewarding & Development Planning
                </p>
                <h1 class="text-2xl font-semibold text-white">Top Performance List</h1>
                <p class="text-sm text-slate-400">
                    Performance Period: January – June 2026
                </p>
            </div>
            <span id="praise-status"
                  class="rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-200">
                Pending PRAISE Submission
            </span>
        </div>

        <!-- SUMMARY -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">System-Generated Eligible List</h2>
                    <p class="text-xs text-slate-400">Read-only; generated from final official ratings.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Final IPCR Rating</th>
                            <th class="px-4 py-3 text-left">Performance Level</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-white">Ramon Reyes</td>
                            <td class="px-4 py-3 text-slate-200">Revenue Collection Unit</td>
                            <td class="px-4 py-3 text-slate-100">5.00</td>
                            <td class="px-4 py-3 text-slate-100">Outstanding</td>
                            <td class="px-4 py-3 text-slate-200">Eligible</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ACTION -->
        <div class="flex flex-col items-end gap-2">
            <button type="button"
                    id="submit-praise"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 transition hover:bg-emerald-500">
                <span id="submit-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                <span id="submit-label">Submit Top Performance List</span>
            </button>
            <p class="text-[11px] text-slate-500">
                Submission endorses the system-generated list to the PRAISE Committee. No edits or selections occur here.
            </p>
        </div>

    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const submitBtn = document.getElementById('submit-praise');
                const submitSpinner = document.getElementById('submit-spinner');
                const submitLabel = document.getElementById('submit-label');
                const statusBadge = document.getElementById('praise-status');

                submitBtn?.addEventListener('click', () => {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                    submitSpinner?.classList.remove('hidden');
                    if (submitLabel) submitLabel.textContent = 'Submitting...';

                    setTimeout(() => {
                        submitSpinner?.classList.add('hidden');
                        if (submitLabel) submitLabel.textContent = 'Submitted';
                        if (statusBadge) {
                            statusBadge.textContent = 'Submitted to PRAISE Committee';
                            statusBadge.className = 'rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200';
                        }
                    }, 1000);
                });
            });
        </script>
    @endpush
</x-layouts.pmt>
