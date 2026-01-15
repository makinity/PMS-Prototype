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
                            <th class="px-4 py-2 text-left">Period</th>
                            <th class="px-4 py-2 text-left">Referenced UWP</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Jan–Dec 2026</td>
                            <td class="px-4 py-3">Records Management Unit – UWP</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300">
                                    For Department Head Review
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-direct="true"
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
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Create OPCR</h2>
                        <p class="text-sm text-slate-400">Encode OPCR based on approved UWP.</p>
                    </div>
                    <button data-close-modal class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-1">Approved UWP</label>
                        <select class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white">
                            <option selected disabled>Select approved UWP</option>
                            <option>Records Management Unit – Jan–Dec 2026</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-1">Success Indicator</label>
                        <input type="text"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white"
                               placeholder="1,200 records scanned and digitized">
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-1">Performance Measure</label>
                        <input type="text"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white"
                               placeholder="Timeliness and accuracy">
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-1">Weight (%)</label>
                        <input type="number"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white"
                               placeholder="80">
                    </div>

                    <!-- ACTIONS -->
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button"
                                data-close-modal
                                class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                            Cancel
                        </button>

                        <!-- SUBMIT WITH LOADING -->
                        <button type="button"
                                data-submit-loading
                                data-loading-text="Submitting..."
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                            <span data-button-label>Submit OPCR</span>
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
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">OPCR Details</h2>
                        <p class="text-sm text-slate-400">Read-only view</p>
                    </div>
                    <button data-close-modal class="text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="mt-6 space-y-2 text-sm text-slate-300">
                    <p><strong>UWP:</strong> Records Management Unit – Jan–Dec 2026</p>
                    <p><strong>Success Indicator:</strong> 1,200 records scanned and digitized</p>
                    <p><strong>Measure:</strong> Timeliness and accuracy</p>
                    <p><strong>Weight:</strong> 80%</p>
                    <p><strong>Status:</strong> For Department Head Review</p>
                </div>

                <div class="flex justify-end mt-6">
                    <button data-close-modal
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
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

    });
    </script>
    @endpush
</x-layouts.admin>
