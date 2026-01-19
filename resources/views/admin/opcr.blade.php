<x-layouts.admin>
    <section class="space-y-6 admin-page">

        <!-- HEADER -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">
                    Office Performance Commitment and Review (OPCR)
                </h1>
                <p class="text-sm text-slate-400">
                    Stage I – Performance Planning and Commitment
                </p>
                <p class="text-xs text-slate-500">
                    Admin encodes OPCR based on PMT-approved UWP and submits it for Department Head review.
                </p>
            </div>

            <!-- CREATE OPCR (DIRECT) -->
            <button type="button"
                    data-direct="true"
                    data-opens-modal="create-opcr-modal"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                Create OPCR
            </button>
        </div>

        <!-- OPCR LIST -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
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
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Revenue Collection Unit</td>
                            <td class="px-4 py-3">January – June 2026</td>
                            <td class="px-4 py-3">PMT Approved UWP</td>
                            <td class="px-4 py-3">3 outputs</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300">
                                    For Department Head Review
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button data-direct="true"
                                        data-opens-modal="view-opcr-modal"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CREATE OPCR MODAL -->
        <div id="create-opcr-modal"
            class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-black/60 px-4 py-6">
            <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6">

                <!-- MODAL HEADER -->
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Generate OPCR</h2>
                        <p class="text-sm text-slate-400">
                            Generate Office Performance Commitment derived from PMT-approved UWP (Stage 1).
                        </p>
                    </div>
                    <button data-close-modal class="text-slate-400 hover:text-white">✕</button>
                </div>

                <!-- MODAL BODY -->
                <form class="mt-6 space-y-4">

                    <!-- APPROVED UWP SELECT -->
                    <div>
                        <label class="block text-sm text-slate-300 mb-1">
                            Approved Unit Work Plan (UWP)
                        </label>
                        <select id="uwpSelect"
                                class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white">
                            <option disabled selected>Select approved UWP</option>
                            <option value="uwp-rcu-2026">
                                Revenue Collection Unit – January–June 2026 (PMT Approved)
                            </option>
                        </select>
                    </div>

                    <!-- AUTO-DERIVED OPCR PREVIEW -->
                    <div id="derivedPreview"
                            class="hidden rounded-lg border border-slate-800 bg-slate-900/50 p-4">

                        <p class="text-xs text-slate-400 mb-1">
                            Derived Office Performance Commitments (from approved UWP)
                        </p>
                        <p class="text-[11px] text-slate-500 mt-2">
                            Targets shown are aggregated from approved UWP success indicators.
                        </p>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-slate-300">
                                <thead class="text-slate-500 uppercase">
                                    <tr>
                            <th class="py-2 text-left">Output</th>
                            <th class="py-2 text-left">Success Indicators</th>
                            <th class="py-2 text-left">Target Summary</th>
                            <th class="py-2 text-left">Weight</th>
                            <th class="py-2 text-left">Function</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-slate-800">
                                <td class="py-2">
                                    E-Bank Scanning and Encoding of Revenue Transactions
                                </td>
                                <td class="py-2">
                                    <button type="button"
                                            class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                            data-uwp-view-indicators
                                            data-title="E-Bank Scanning and Encoding of Revenue Transactions"
                                            data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'>
                                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span>(3)</span>
                                    </button>
                                </td>
                                <td class="py-2">Daily; all e-bank transactions processed within the same working day</td>
                                <td class="py-2">50%</td>
                                <td class="py-2">
                                    <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                                        Core
                                    </span>
                                </td>
                            </tr>
                            <tr class="border-t border-slate-800">
                                <td class="py-2">
                                    Processing of Over-the-Counter Revenue Transactions
                                </td>
                                <td class="py-2">
                                    <button type="button"
                                            class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                            data-uwp-view-indicators
                                            data-title="Processing of Over-the-Counter Revenue Transactions"
                                            data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'>
                                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span>(3)</span>
                                    </button>
                                </td>
                                <td class="py-2">Daily; 95% processed within the same working day</td>
                                <td class="py-2">30%</td>
                                <td class="py-2">
                                    <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                                        Core
                                    </span>
                                </td>
                            </tr>
                            <tr class="border-t border-slate-800">
                                <td class="py-2">
                                    Maintenance of Revenue Records Filing System
                                </td>
                                <td class="py-2">
                                    <button type="button"
                                            class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                            data-uwp-view-indicators
                                            data-title="Maintenance of Revenue Records Filing System"
                                            data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'>
                                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span>(3)</span>
                                    </button>
                                </td>
                                <td class="py-2">Quarterly validation and update</td>
                                <td class="py-2">20%</td>
                                <td class="py-2">
                                    <span class="rounded-md bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-300 border border-blue-400/30">
                                        Support
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

                    <!-- ACTIONS -->
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button"
                                data-close-modal
                                class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                            Cancel
                        </button>

                        <button type="button"
                                data-submit-loading
                                data-loading-text="Generating OPCR..."
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                            <span data-button-label>Generate OPCR</span>
                            <span data-button-spinner
                                    class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>
                    </div>

                </form>
            </div>
        </div>


       <!-- VIEW OPCR MODAL -->
<div id="view-opcr-modal"
     class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-black/60 px-4 py-6">

    <div class="w-full max-w-xl rounded-2xl border border-slate-800 bg-slate-900 p-6">

        <!-- Header -->
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-white">
                Office Performance Commitment and Review
            </h3>
            <p class="text-sm text-slate-400">
                Derived from PMT-approved Unit Work Plan (Stage 1)
            </p>
            <span class="inline-flex rounded-full bg-amber-500/10 px-2 py-1 text-xs text-amber-300 mt-2">
                For Department Head Review
            </span>
        </div>

        <!-- Outputs Table -->
        <table class="w-full text-sm text-slate-300">
            <thead class="text-xs uppercase text-slate-500">
            <tr>
                <th class="py-2 text-left">Output</th>
                <th class="py-2 text-left">Success Indicators</th>
                <th class="py-2 text-left">Target Summary</th>
                <th class="py-2 text-left">Weight</th>
                <th class="py-2 text-left">Function</th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-t border-slate-800">
                <td class="py-2">E-Bank Scanning and Encoding of Revenue Transactions</td>
                <td class="py-2">
                    <button type="button"
                            class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                            data-uwp-view-indicators
                            data-title="E-Bank Scanning and Encoding of Revenue Transactions"
                            data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'>
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>(3)</span>
                    </button>
                </td>
                <td class="py-2">Daily; all e-bank transactions processed within the same working day</td>
                <td class="py-2">50%</td>
                <td class="py-2">
                    <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                        Core
                    </span>
                </td>
            </tr>
            <tr class="border-t border-slate-800">
                <td class="py-2">Processing of Over-the-Counter Revenue Transactions</td>
                <td class="py-2">
                    <button type="button"
                            class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                            data-uwp-view-indicators
                            data-title="Processing of Over-the-Counter Revenue Transactions"
                            data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'>
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>(3)</span>
                    </button>
                </td>
                <td class="py-2">Daily; 95% processed within the same working day</td>
                <td class="py-2">30%</td>
                <td class="py-2">
                    <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                        Core
                    </span>
                </td>
            </tr>
            <tr class="border-t border-slate-800">
                <td class="py-2">Maintenance of Revenue Records Filing System</td>
                <td class="py-2">
                    <button type="button"
                            class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                            data-uwp-view-indicators
                            data-title="Maintenance of Revenue Records Filing System"
                            data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'>
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>(3)</span>
                    </button>
                </td>
                <td class="py-2">Quarterly validation and update</td>
                <td class="py-2">20%</td>
                <td class="py-2">
                    <span class="rounded-md bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-300 border border-blue-400/30">
                        Support
                    </span>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between mt-6">

            <!-- Status hint -->
            <p class="text-xs text-slate-500">
                Export becomes available after Department Head approval.
            </p>

            <div class="flex gap-2">

                <!-- Close -->
                <button data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                    Close
                </button>

                <!-- EXPORT OPCR (Admin + Approved only) -->
                <a href="{{ route('stage1.opcr.export.pdf') }}"
                    data-export-opcr
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                    Export OPCR (PDF)
                </a>

            </div>
        </div>

    </div>
</div>

<div id="uwp-indicators-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 px-4 py-6">
    <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
        <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                <h3 id="uwp-indicators-title" class="text-lg font-semibold text-white">--</h3>
                <p class="text-xs text-slate-400 mt-1">Read-only list derived from the approved UWP.</p>
            </div>
            <button type="button" onclick="closeUwpIndicatorsModal()" class="text-slate-400 hover:text-white">
                <span class="sr-only">Close</span>
                &times;
            </button>
        </div>

        <div class="mt-4 max-h-64 overflow-y-auto rounded-lg border border-slate-800 bg-slate-950/70 p-3">
            <ol id="uwp-indicators-list" class="list-decimal space-y-2 pl-5 text-sm text-slate-100"></ol>
        </div>

        <div class="mt-5 flex justify-end">
            <button type="button"
                    class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                    onclick="closeUwpIndicatorsModal()">
                Close
            </button>
        </div>
    </div>
</div>


    </section>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // DIRECT MODAL OPEN (View / Create)
        document.querySelectorAll('[data-direct="true"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.opensModal;
                const modal = document.getElementById(target);
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
            });
        });

        // CLOSE MODALS
        document.querySelectorAll('[data-close-modal]').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.fixed')?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        });

        // SUBMIT BUTTON LOADING
        document.querySelectorAll('[data-submit-loading]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.dataset.loading === 'true') return;

                btn.dataset.loading = 'true';

                const label = btn.querySelector('[data-button-label]');
                const spinner = btn.querySelector('[data-button-spinner]');
                const originalText = label.textContent;
                const loadingText = btn.dataset.loadingText || 'Loading...';

                label.textContent = loadingText;
                spinner.classList.remove('hidden');
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-wait');

                // DEMO DELAY
                setTimeout(() => {
                    label.textContent = originalText;
                    spinner.classList.add('hidden');
                    btn.disabled = false;
                    btn.classList.remove('opacity-70', 'cursor-wait');
                    btn.dataset.loading = 'false';

                    // close modal after submit (demo behavior)
                    btn.closest('.fixed')?.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }, 1200);
            });
        });

        // EXPORT OPCR (PDF) - demo-friendly loading then open in new tab
        const exportBtn = document.querySelector('[data-export-opcr]');
        if (exportBtn) {
            exportBtn.addEventListener('click', (e) => {
                const href = exportBtn.getAttribute('href');
                if (!href) return;
                e.preventDefault();

                exportBtn.classList.add('opacity-70', 'cursor-wait');
                setTimeout(() => {
                    exportBtn.classList.remove('opacity-70', 'cursor-wait');
                    window.open(href, '_blank');
                }, 300);
            });
        }

    });

        document.getElementById('uwpSelect')?.addEventListener('change', () => {
        document.getElementById('derivedPreview')?.classList.remove('hidden');
    });

    (function initIndicatorsModal() {
        const modal = document.getElementById('uwp-indicators-modal');
        const titleEl = document.getElementById('uwp-indicators-title');
        const listEl = document.getElementById('uwp-indicators-list');

        window.closeUwpIndicatorsModal = function () {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        };

        function openIndicatorsModal(title, indicators) {
            if (!modal || !titleEl || !listEl) return;
            titleEl.textContent = title || '--';
            listEl.innerHTML = '';
            (indicators || []).forEach((text) => {
                const value = (text || '').trim();
                if (!value) return;
                const li = document.createElement('li');
                li.textContent = value;
                listEl.appendChild(li);
            });
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        document.querySelectorAll('[data-uwp-view-indicators]').forEach((btn) => {
            btn.addEventListener('click', () => {
                let indicators = [];
                try {
                    indicators = JSON.parse(btn.dataset.indicators || '[]');
                } catch (e) {
                    indicators = [];
                }
                openIndicatorsModal(btn.dataset.title || '--', indicators);
            });
        });
    })();

    </script>
    @endpush
</x-layouts.admin>
