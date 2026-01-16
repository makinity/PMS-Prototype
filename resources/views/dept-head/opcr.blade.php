<x-layouts.dept-head>
    <section class="space-y-6">

        <!-- PAGE HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Office Performance Commitment and Review (OPCR)
                </h1>
                <p class="text-sm text-slate-400">
                    Stage I – Performance Planning and Commitment
                </p>
                <p class="text-xs text-slate-500">
                    Review OPCRs submitted by Admin based on PMT-approved Unit Work Plans.
                </p>
            </div>
        </div>

        <!-- OPCR LIST -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Office / Unit</th>
                            <th class="px-4 py-2 text-left">Period</th>
                            <th class="px-4 py-2 text-left">Referenced UWP</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- FOR REVIEW -->
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Revenue Collection Unit</td>
                            <td class="px-4 py-3">January – June 2026</td>
                            <td class="px-4 py-3">Approved UWP</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300">
                                    For Review
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-open-review-opcr
                                        class="text-blue-400 hover:text-blue-300">
                                    Review
                                </button>
                            </td>
                        </tr>

                        <!-- APPROVED -->
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Human Resource Unit</td>
                            <td class="px-4 py-3">Jan–Dec 2026</td>
                            <td class="px-4 py-3">Approved UWP</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-300">
                                    Approved
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-open-view-opcr
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- REVIEW OPCR MODAL -->
        <div id="review-opcr-modal"
            data-modal-container
             class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-black/60 px-4 py-6">
            <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6">

                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">
                            Review OPCR – Revenue Collection Unit
                        </h2>
                        <p class="text-sm text-slate-400">
                            Derived from PMT-approved Unit Work Plan
                        </p>
                    </div>
                    <button data-close-modal class="text-slate-400 hover:text-white">✕</button>
                </div>

                <!-- CONTEXT -->
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3 text-sm">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs text-slate-400">Office</p>
                        <p class="text-white">Revenue Collection Unit</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs text-slate-400">Period</p>
                        <p class="text-white">January - June 2026</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs text-slate-400">Referenced UWP</p>
                        <p class="text-white">Approved</p>
                    </div>
                </div>

                <!-- OPCR TARGETS -->
                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-sm border border-slate-800">
                        <thead class="bg-slate-950 text-slate-300">
                            <tr>
                                <th class="px-4 py-3 text-left">Success Indicator</th>
                                <th class="px-4 py-3 text-left">Performance Measure</th>
                                <th class="px-4 py-3 text-left">Target</th>
                                <th class="px-4 py-3 text-left">Weight</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-100">
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3">
                                    E-Bank Scanning and Encoding of Revenue Transactions
                                </td>
                                <td class="px-4 py-3">
                                    Timeliness and accuracy
                                </td>
                                <td class="px-4 py-3">
                                    95% same-day processing
                                </td>
                                <td class="px-4 py-3">
                                    50%
                                </td>
                            </tr>

                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3">
                                    Processing of Over-the-Counter Revenue Transactions
                                </td>
                                <td class="px-4 py-3">
                                    Timeliness and accuracy
                                </td>
                                <td class="px-4 py-3">
                                    95% same-day processing
                                </td>
                                <td class="px-4 py-3">
                                    30%
                                </td>
                            </tr>

                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3">
                                    Maintenance of Revenue Records Filing System
                                </td>
                                <td class="px-4 py-3">
                                    Records completeness
                                </td>
                                <td class="px-4 py-3">
                                    Quarterly validation
                                </td>
                                <td class="px-4 py-3">
                                    20%
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- REMARKS -->
                <div class="mt-6">
                    <label class="block mb-1 text-sm text-slate-300">
                        Remarks (required if returning)
                    </label>
                    <textarea rows="3"
                            style="min-width:72px; background:#0f172a;color:#e5e7eb;"
                              class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"></textarea>
                </div>

                <!-- ACTIONS -->
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                            data-close-modal
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                        Close
                    </button>

                    <button type="button"
                            data-opcr-return
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-600 px-4 py-2 text-sm font-semibold text-amber-300 hover:bg-amber-600/10">
                        <span data-button-label>Return to Admin</span>
                        <span data-button-spinner
                              class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>

                    <button type="button"
                            data-opcr-approve
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                        <span data-button-label>Approve OPCR</span>
                        <span data-button-spinner
                              class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- VIEW OPCR MODAL -->
        <div id="view-opcr-modal"
            data-modal-container
             class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-black/60 px-4 py-6">
            <div class="w-full max-w-xl rounded-2xl border border-slate-800 bg-slate-900 p-6">

                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">
                            View OPCR – Human Resource Unit
                        </h2>
                        <p class="text-sm text-slate-400">Read-only OPCR details</p>
                    </div>
                    <button data-close-modal class="text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="mt-6 space-y-2 text-sm text-slate-300">
                    <p><strong>Office:</strong> Human Resource Unit</p>
                    <p><strong>Period:</strong> Jan–Dec 2026</p>
                    <p><strong>Referenced UWP:</strong> Approved</p>
                    <p><strong>Success Indicator:</strong> 1,200 records scanned and digitized</p>
                    <p><strong>Performance Measure:</strong> Timeliness and accuracy</p>
                    <p><strong>Weight:</strong> Core Function – 80%</p>
                    <p><strong>Status:</strong> Approved</p>
                </div>

                <div class="mt-6 flex justify-end">
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

    const modalIds = ['review-opcr-modal', 'view-opcr-modal'];

    function getModalEl(id) {
        return document.getElementById(id);
    }

    function openModal(id) {
        const modal = getModalEl(id);
        if (!modal) return;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(modal) {
        if (!modal) return;

        modal.classList.add('hidden');

        // If no other modal is open, restore scrolling
        const anyOpen = document.querySelector('[data-modal-container]:not(.hidden)');
        if (!anyOpen) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function closeAllModals() {
        document.querySelectorAll('[data-modal-container]').forEach(m => m.classList.add('hidden'));
        document.body.classList.remove('overflow-hidden');
    }

    function setButtonLoading(button, isLoading, loadingText) {
        if (!button) return;
        const label = button.querySelector('[data-button-label]');
        const spinner = button.querySelector('[data-button-spinner]');

        if (label && !button.dataset.originalLabel) {
            button.dataset.originalLabel = label.textContent.trim();
        }

        if (isLoading) {
            button.disabled = true;
            button.classList.add('opacity-70', 'cursor-not-allowed');
            spinner?.classList.remove('hidden');
            if (label && loadingText) label.textContent = loadingText;
        } else {
            button.disabled = false;
            button.classList.remove('opacity-70', 'cursor-not-allowed');
            spinner?.classList.add('hidden');
            if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
        }
    }

    // Open buttons
    document.querySelectorAll('[data-open-review-opcr]').forEach(btn => {
        btn.addEventListener('click', () => openModal('review-opcr-modal'));
    });

    document.querySelectorAll('[data-open-view-opcr]').forEach(btn => {
        btn.addEventListener('click', () => openModal('view-opcr-modal'));
    });

    // Close buttons (closes nearest modal only)
    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('[data-modal-container]');
            closeModal(modal);
        });
    });

    // Click outside to close
    document.querySelectorAll('[data-modal-container]').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
    });

    // Esc to close all
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAllModals();
    });

    // Approve/Return loading demo
    document.querySelectorAll('[data-opcr-approve],[data-opcr-return]').forEach(btn => {
        btn.addEventListener('click', () => {
            const isReturn = btn.hasAttribute('data-opcr-return');
            setButtonLoading(btn, true, isReturn ? 'Returning...' : 'Approving...');

            setTimeout(() => {
                setButtonLoading(btn, false);
                closeAllModals();
            }, 1000);
        });
    });

});
</script>
@endpush

</x-layouts.dept-head>
