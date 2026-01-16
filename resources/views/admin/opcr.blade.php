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
                            <th class="px-4 py-2 text-left">Source UWP</th>
                            <th class="px-4 py-2 text-left">Outputs</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Revenue Collection Unit</td>
                            <td class="px-4 py-3">January - June 2026</td>
                            <td class="px-4 py-3">Approved UWP</td>
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
                            Generate Office Performance Commitment based on PMT-approved UWP.
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
                            <option value="uwp-rcu-2025">
                                Revenue Collection Unit – January–December 2025
                            </option>
                        </select>
                    </div>

                    <!-- AUTO-DERIVED OPCR PREVIEW -->
                    <div id="derivedPreview"
                            class="hidden rounded-lg border border-slate-800 bg-slate-900/50 p-4">

                        <p class="text-xs text-slate-400 mb-3">
                            Derived Office Outputs (read-only)
                        </p>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-slate-300">
                                <thead class="text-slate-500 uppercase">
                                    <tr>
                                        <th class="py-2 text-left">Office Output</th>
                                        <th class="py-2 text-left">Target</th>
                                        <th class="py-2 text-left">Weight</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t border-slate-800">
                                        <td class="py-2">
                                            E-Bank Scanning and Encoding of Revenue Transactions
                                        </td>
                                        <td class="py-2">95% same-day processing</td>
                                        <td class="py-2">50%</td>
                                    </tr>
                                    <tr class="border-t border-slate-800">
                                        <td class="py-2">
                                            Processing of Over-the-Counter Revenue Transactions
                                        </td>
                                        <td class="py-2">95% same-day processing</td>
                                        <td class="py-2">30%</td>
                                    </tr>
                                    <tr class="border-t border-slate-800">
                                        <td class="py-2">
                                            Maintenance of Revenue Records Filing System
                                        </td>
                                        <td class="py-2">Quarterly validation</td>
                                        <td class="py-2">20%</td>
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
                Derived outputs from PMT-approved Unit Work Plan
            </p>
        </div>

        <!-- Outputs Table -->
        <table class="w-full text-sm text-slate-300">
            <thead class="text-xs uppercase text-slate-500">
            <tr>
                <th class="py-2 text-left">Output</th>
                <th class="py-2 text-left">Target</th>
                <th class="py-2 text-left">Weight</th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-t border-slate-800">
                <td class="py-2">E-Bank Scanning and Encoding</td>
                <td class="py-2">95% same-day</td>
                <td class="py-2">50%</td>
            </tr>
            <tr class="border-t border-slate-800">
                <td class="py-2">OTC Revenue Transactions</td>
                <td class="py-2">95% same-day</td>
                <td class="py-2">30%</td>
            </tr>
            <tr class="border-t border-slate-800">
                <td class="py-2">Records Filing Maintenance</td>
                <td class="py-2">Quarterly</td>
                <td class="py-2">20%</td>
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

    });

        document.getElementById('uwpSelect')?.addEventListener('change', () => {
        document.getElementById('derivedPreview')?.classList.remove('hidden');
    });

    </script>
    @endpush
</x-layouts.admin>
