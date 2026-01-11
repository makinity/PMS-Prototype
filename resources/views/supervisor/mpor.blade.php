<x-layouts.supervisor>
    <section class="space-y-6">

        <!-- Header -->
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Monthly Performance Output Report
                </p>
                <h1 class="mt-1 text-2xl font-bold text-white">
                    MPOR Validation – December 2025
                </h1>
                <p class="text-sm text-slate-400 mt-1">
                    Review and validate employee MPOR entries before consolidation to SMPOR.
                </p>
            </div>
            <span class="rounded-full border border-amber-600/50 bg-amber-500/10 px-3 py-1
                text-xs font-semibold text-amber-200">
                Pending validation
            </span>
        </div>

        <!-- Employee Info -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Employee</p>
                <p class="mt-1 text-sm font-semibold text-white">Ramon Reyes</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Employee ID</p>
                <p class="mt-1 text-sm font-semibold text-white">EMP-0078</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Unit</p>
                <p class="mt-1 text-sm font-semibold text-white">Administrative Services</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">MPOR Status</p>
                <p class="mt-1 text-sm font-semibold text-amber-300">For review</p>
            </div>
        </div>

        <!-- Validation Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">ORS logs</p>
                <p class="mt-1 text-2xl font-semibold text-white">18</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Outputs linked</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-300">16</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Pending validation</p>
                <p class="mt-1 text-2xl font-semibold text-amber-300">2</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Missing links</p>
                <p class="mt-1 text-2xl font-semibold text-rose-300">1</p>
            </div>
        </div>

        <!-- MPOR Outputs -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <h2 class="text-lg font-semibold text-white mb-3">
                MPOR entries
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-800">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Output</th>
                            <th class="px-4 py-3 text-left">ORS Ref</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">Client Form Review</td>
                            <td class="px-4 py-3 text-slate-300">REQ-2025-021</td>
                            <td class="px-4 py-3 text-slate-300">Dec 20, 2025</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-blue-500/10 px-2 py-1 text-xs
                                    font-semibold text-blue-200 border border-blue-600/50">
                                    For review
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <button
                                    type="button"
                                    data-view-entry
                                    data-output="Client Form Review"
                                    data-ors="REQ-2025-021"
                                    data-date="Dec 20, 2025"
                                    data-status="For review"
                                    data-unit="Administrative Services"
                                    data-employee="Ramon Reyes"
                                    class="text-blue-400 hover:text-blue-300 text-xs font-semibold">
                                    View
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Final Action -->
        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 flex items-center justify-between">
            <p class="text-xs text-slate-400">
                MPOR can be locked only when all entries are validated and linked.
            </p>
            <button disabled
                class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-300 cursor-not-allowed">
                Lock MPOR (Ready for SMPOR)
            </button>
        </div>

    </section>

    <div id="view-mpor-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4">
        <div class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900/95 shadow-2xl shadow-black/40">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-4 py-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-emerald-300">MPOR Entry</p>
                    <h3 class="text-lg font-semibold text-white">View MPOR Entry</h3>
                    <p class="mt-1 text-sm text-slate-400">Review details and choose an action below.</p>
                </div>
                <button type="button" data-modal-hide="view-mpor-modal"
                        class="rounded-full p-1 text-slate-400 transition hover:bg-slate-800 hover:text-slate-200">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>
            <div class="space-y-3 px-4 py-4 text-sm text-slate-300">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Output</p>
                        <p id="modal-output" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">ORS Ref</p>
                        <p id="modal-ors" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Logged date</p>
                        <p id="modal-date" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Current status</p>
                        <p id="modal-status" class="mt-1 text-sm font-semibold text-amber-200">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Unit</p>
                        <p id="modal-unit" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Employee</p>
                        <p id="modal-employee" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-800 px-4 py-3">
                <button type="button" data-modal-hide="view-mpor-modal"
                        class="rounded-lg border border-slate-700 bg-slate-800/80 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Close
                </button>
                <button type="button"
                        data-modal-return="view-mpor-modal"
                        class="inline-flex items-center gap-2 rounded-lg border border-rose-500/40 bg-rose-600/10 px-3 py-2 text-sm font-semibold text-rose-200 transition hover:bg-rose-600/20">
                    Return
                </button>
                <button type="button"
                        data-modal-validate="view-mpor-modal"
                        data-auto-reset="true"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                    Validate
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const spinner = '<svg class="h-4 w-4 animate-spin text-emerald-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';

                const setLoadingState = (button, text) => {
                    if (!button || button.disabled) return;
                    button.dataset.originalContent = button.dataset.originalContent || button.innerHTML;
                    button.innerHTML = `<span class="flex items-center justify-center gap-2">${spinner}<span>${text}</span></span>`;
                    button.disabled = true;
                    button.classList.add('opacity-80', 'cursor-not-allowed');
                };

                const resetLoadingState = (button) => {
                    if (!button) return;
                    if (button.dataset.originalContent) {
                        button.innerHTML = button.dataset.originalContent;
                    }
                    button.disabled = false;
                    button.classList.remove('opacity-80', 'cursor-not-allowed');
                };

                const toggleModal = (modalId, shouldShow) => {
                    const modal = document.getElementById(modalId);
                    if (!modal) return;
                    if (shouldShow) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        modal.setAttribute('aria-hidden', 'false');
                    } else {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        modal.setAttribute('aria-hidden', 'true');
                    }
                };

                const modalOutput = document.getElementById('modal-output');
                const modalOrs = document.getElementById('modal-ors');
                const modalDate = document.getElementById('modal-date');
                const modalStatus = document.getElementById('modal-status');
                const modalUnit = document.getElementById('modal-unit');
                const modalEmployee = document.getElementById('modal-employee');
                const viewModalId = 'view-mpor-modal';

                document.querySelectorAll('[data-view-entry]').forEach((button) => {
                    button.addEventListener('click', () => {
                        if (modalOutput) modalOutput.textContent = button.dataset.output || '--';
                        if (modalOrs) modalOrs.textContent = button.dataset.ors || '--';
                        if (modalDate) modalDate.textContent = button.dataset.date || '--';
                        if (modalStatus) modalStatus.textContent = button.dataset.status || '--';
                        if (modalUnit) modalUnit.textContent = button.dataset.unit || '--';
                        if (modalEmployee) modalEmployee.textContent = button.dataset.employee || '--';
                        toggleModal(viewModalId, true);
                    });
                });

                document.getElementById(viewModalId)?.addEventListener('click', (event) => {
                    if (event.target.id === viewModalId) {
                        toggleModal(viewModalId, false);
                    }
                });

                document.querySelectorAll('[data-modal-hide="view-mpor-modal"]').forEach((button) => {
                    button.addEventListener('click', () => toggleModal(viewModalId, false));
                });

                document.querySelectorAll('[data-modal-return="view-mpor-modal"]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const modal = document.getElementById(viewModalId);
                        const actionButtons = modal?.querySelectorAll('[data-modal-return], [data-modal-validate], [data-modal-hide]');
                        setLoadingState(button, 'Returning...');
                        actionButtons?.forEach((btn) => btn.setAttribute('disabled', 'true'));
                        const delay = Number(button.dataset.resetDelay || 1400);
                        setTimeout(() => {
                            actionButtons?.forEach((btn) => btn.removeAttribute('disabled'));
                            resetLoadingState(button);
                            toggleModal(viewModalId, false);
                        }, delay);
                    });
                });

                document.querySelectorAll('[data-modal-validate="view-mpor-modal"]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const modal = document.getElementById(viewModalId);
                        const actionButtons = modal?.querySelectorAll('[data-modal-return], [data-modal-validate], [data-modal-hide]');
                        setLoadingState(button, 'Validating...');
                        actionButtons?.forEach((btn) => btn.setAttribute('disabled', 'true'));
                        const delay = Number(button.dataset.resetDelay || 1400);
                        setTimeout(() => {
                            actionButtons?.forEach((btn) => btn.removeAttribute('disabled'));
                            resetLoadingState(button);
                            toggleModal(viewModalId, false);
                        }, delay);
                    });
                });
            });
        </script>
    @endpush
</x-layouts.supervisor>
