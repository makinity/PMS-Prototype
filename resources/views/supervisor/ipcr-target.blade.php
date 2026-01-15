<x-layouts.supervisor>
    <section class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">IPCR Target Review</h1>
                <p class="text-sm text-slate-400 mt-1">
                    Stage I – Performance Planning and Commitment
                </p>
                <p class="text-xs text-slate-500">
                    Review employee IPCR targets derived from approved OPCR.
                    This stage confirms alignment only; no performance rating is performed.
                </p>
            </div>

            <span class="rounded-full border border-blue-600/40 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                Supervisor Review
            </span>
        </div>

        {{-- IPCR LIST --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 overflow-hidden">
            <div class="border-b border-slate-800 p-4">
                <h2 class="text-lg font-semibold text-white">Submitted IPCRs</h2>
                <p class="text-xs text-slate-400">
                    Select an IPCR to review employee targets before endorsement.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/80 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Employee</th>
                            <th class="px-4 py-3 text-left font-semibold">Position</th>
                            <th class="px-4 py-3 text-left font-semibold">Rating Period</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold w-32">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-100">
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-semibold">Ramon Reyes</td>
                            <td class="px-4 py-3 text-slate-300">Records Management Officer</td>
                            <td class="px-4 py-3 text-slate-300">Jan – Dec 2026</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full border border-blue-500/40 bg-blue-500/10 px-2.5 py-1 text-xs font-semibold text-blue-200">
                                    For Review
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-review-ipcr
                                        data-employee="Ramon Reyes"
                                        data-position="Records Management Officer"
                                        data-period="Jan – Dec 2026"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                    <span>Review</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- REVIEW IPCR MODAL --}}
    <div id="ipcr-review-modal"
         class="fixed inset-0 z-[70] hidden flex items-start justify-center bg-black/70 px-4 py-8">

        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 shadow-xl">
            <div class="flex items-start justify-between border-b border-slate-800 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-blue-300">Stage I</p>
                    <h2 class="text-lg font-semibold text-white">IPCR Target Review</h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Review employee targets derived from approved OPCR.
                    </p>
                </div>
                <button type="button" data-close-ipcr class="text-slate-400 hover:text-white">
                    ✕
                </button>
            </div>

            <div class="px-6 py-5 space-y-6 max-h-[70vh] overflow-y-auto">

                {{-- EMPLOYEE INFO --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs uppercase text-slate-400">Employee</p>
                        <p id="modal-employee" class="font-semibold text-white">—</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs uppercase text-slate-400">Position</p>
                        <p id="modal-position" class="font-semibold text-white">—</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs uppercase text-slate-400">Rating Period</p>
                        <p id="modal-period" class="font-semibold text-white">—</p>
                    </div>
                </div>

                {{-- CORE FUNCTIONS --}}
                <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                    <div class="border-b border-slate-800 px-4 py-3">
                        <h3 class="text-sm font-semibold text-white">
                            Core Functions <span class="text-slate-400">(80%)</span>
                        </h3>
                        <p class="text-xs text-slate-400">Read-only targets from approved OPCR.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-slate-800">
                            <thead class="bg-slate-950/80 text-slate-300">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Expected Output</th>
                                    <th class="px-4 py-3 text-left font-semibold">Success Indicator</th>
                                    <th class="px-4 py-3 text-left font-semibold">Timeline</th>
                                    <th class="px-4 py-3 text-center font-semibold">Weight</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 text-slate-100">
                                <tr>
                                    <td class="px-4 py-3">Records Digitization</td>
                                    <td class="px-4 py-3 text-slate-300">
                                        Scan and digitize 1,200 e-bank records
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">Jan – Dec 2026</td>
                                    <td class="px-4 py-3 text-center font-semibold">80%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SUPPORT FUNCTIONS --}}
                <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                    <div class="border-b border-slate-800 px-4 py-3">
                        <h3 class="text-sm font-semibold text-white">
                            Support Functions <span class="text-slate-400">(20%)</span>
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-slate-800">
                            <thead class="bg-slate-950/80 text-slate-300">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Expected Output</th>
                                    <th class="px-4 py-3 text-left font-semibold">Success Indicator</th>
                                    <th class="px-4 py-3 text-left font-semibold">Timeline</th>
                                    <th class="px-4 py-3 text-center font-semibold">Weight</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 text-slate-100">
                                <tr>
                                    <td class="px-4 py-3">Reference Support</td>
                                    <td class="px-4 py-3 text-slate-300">
                                        Provide document retrieval within 1 business day
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">Jan – Dec 2026</td>
                                    <td class="px-4 py-3 text-center font-semibold">20%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SUPERVISOR REMARKS --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-white">Supervisor Remarks</label>
                    <textarea
                        rows="3"
                        style="min-width:72px; background:#0f172a;color:#e5e7eb;"
                        class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Optional remarks if returning IPCR."
                    ></textarea>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-end gap-3 border-t border-slate-800 px-6 py-4">
                <button type="button"
                        data-employee-loading="true"
                        data-loading-text="Returning..."
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800">
                    <span data-button-label>Return to Employee</span>
                    <span data-button-spinner
                          class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>

                <button type="button"
                        data-employee-loading="true"
                        data-loading-text="Endorsing..."
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                    <span data-button-label>Endorse IPCR</span>
                    <span data-button-spinner
                          class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('ipcr-review-modal');

        document.querySelectorAll('[data-review-ipcr]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('modal-employee').textContent = btn.dataset.employee;
                document.getElementById('modal-position').textContent = btn.dataset.position;
                document.getElementById('modal-period').textContent = btn.dataset.period;

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        });

        document.querySelectorAll('[data-close-ipcr]').forEach(btn => {
            btn.addEventListener('click', () => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        });

        modal.addEventListener('click', e => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });

        document.querySelectorAll('[data-employee-loading="true"]').forEach(button => {
            button.addEventListener('click', () => {
                if (button.dataset.loadingActive === 'true') return;
                button.dataset.loadingActive = 'true';

                const label = button.querySelector('[data-button-label]');
                const spinner = button.querySelector('[data-button-spinner]');
                const original = label.textContent;

                label.textContent = button.dataset.loadingText || 'Processing...';
                spinner.classList.remove('hidden');
                button.disabled = true;

                setTimeout(() => {
                    label.textContent = original;
                    spinner.classList.add('hidden');
                    button.disabled = false;
                    button.dataset.loadingActive = 'false';
                }, 1200);
            });
        });
    });
    </script>
    @endpush
</x-layouts.supervisor>
