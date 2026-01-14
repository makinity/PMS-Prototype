<x-layouts.employee>
    <section class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Summary MPOR — Ramon Reyes</p>
                <h1 class="mt-1 text-2xl font-bold text-white">SMPOR - Q4 2025</h1>
                <p class="text-sm text-slate-400 mt-1">
                    System-generated, locked view derived from supervisor-validated MPOR entries. This summary aggregates monthly MPOR submissions. Task-level breakdowns remain available in MPOR.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full border border-emerald-600/50 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                    System-generated
                </span>
                <span class="rounded-full border border-blue-600/50 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                    Locked view
                </span>
            </div>
        </div>

        <!-- Metrics -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Completion (locked)</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-300">100%</p>
                <p class="text-xs text-slate-500">All validated outputs included</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Total outputs (locked)</p>
                <p class="mt-1 text-2xl font-semibold text-white">100</p>
                <p class="text-xs text-slate-500">Q4 2025 consolidated snapshot</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Core outputs (locked)</p>
                <p class="mt-1 text-2xl font-semibold text-white">120</p>
                <p class="text-xs text-slate-500">Validated core functions</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Support outputs (locked)</p>
                <p class="mt-1 text-2xl font-semibold text-white">45</p>
                <p class="text-xs text-slate-500">Validated support functions</p>
            </div>
        </div>

        <!-- Consolidated Table -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <h2 class="text-lg font-semibold text-white">SMPOR rollup (locked)</h2>
                    <p class="text-xs text-slate-400">All supervisor-validated MPOR entries included.</p>
                </div>
                <span class="text-xs text-slate-400">Lock date: Jan 7, 2026</span>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-800">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Month</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Total outputs</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Core outputs</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Support outputs</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">October 2025</td>
                            <td class="px-4 py-3 text-slate-300">50</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">40</td>
                            <td class="px-4 py-3 text-blue-200 font-semibold">10</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-300 border border-emerald-600/50">Locked</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">November 2025</td>
                            <td class="px-4 py-3 text-slate-300">40</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">35</td>
                            <td class="px-4 py-3 text-blue-200 font-semibold">15</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-300 border border-emerald-600/50">Locked</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">December 2025</td>
                            <td class="px-4 py-3 text-slate-300">10</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">45</td>
                            <td class="px-4 py-3 text-blue-200 font-semibold">20</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-300 border border-emerald-600/50">Locked</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Integrity + Timeline -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Audit guarantees</h2>
                        <p class="text-xs text-slate-400">Locked SMPOR passed all integrity rules.</p>
                    </div>
                    <span class="text-xs text-slate-400">Final state</span>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Auto-timed logs only</p>
                            <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-200 border border-emerald-600/50">Passed</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">All durations used auto-timed ORS logs.</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Output linkage</p>
                            <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-200 border border-emerald-600/50">Verified</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">All outputs linked before lock.</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Supervisor validation</p>
                            <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-200 border border-emerald-600/50">Completed</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">All MPOR entries validated before SMPOR lock.</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Duplicate logs</p>
                            <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-200 border border-emerald-600/50">None</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">No overlapping ORS entries detected in locked set.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
                <h2 class="text-lg font-semibold text-white">Cutoffs and actions</h2>
                <div class="space-y-3 text-sm text-slate-200">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="font-semibold text-white">Lock</p>
                        <p class="text-xs text-slate-400">SMPOR locked on Jan 7, 2026.</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="font-semibold text-white">Next step</p>
                        <p class="text-xs text-slate-400">This SMPOR will be used as the basis for IPCR evaluation.</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <button type="button" disabled class="w-full rounded-lg bg-emerald-500 px-3 py-2 text-sm font-semibold text-slate-900 opacity-60 cursor-not-allowed">
                        SMPOR is system-generated
                    </button>
                    <button type="button" data-direct-action data-auto-reset="true" class="w-full rounded-lg border border-slate-700 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                        View MPOR sources
                    </button>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 text-xs text-slate-400">
            SMPOR is system-generated from locked MPOR and ORS logs. Manual edits, ratings, and time overrides remain disabled to keep the audit trail clean. This locked snapshot is final for IPCR evaluation.
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const spinner = '<svg class="h-4 w-4 animate-spin text-emerald-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';

                const setLoadingState = (button) => {
                    if (!button || button.disabled) return;
                    button.dataset.originalContent = button.dataset.originalContent || button.innerHTML;
                    button.innerHTML = `<span class="flex items-center justify-center gap-2">${spinner}<span>Processing...</span></span>`;
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

                const resetModalButtons = (modal) => {
                    if (!modal) return;
                    modal.querySelectorAll('[data-modal-confirm]').forEach((confirmButton) => {
                        resetLoadingState(confirmButton);
                    });
                    modal.querySelectorAll('[data-modal-close]').forEach((closeButton) => {
                        closeButton.disabled = false;
                    });
                };

                const toggleModal = (modalId, shouldShow) => {
                    const modal = document.getElementById(modalId);
                    if (!modal) return;
                    if (shouldShow) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        resetModalButtons(modal);
                        modal.setAttribute('aria-hidden', 'false');
                    } else {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        modal.setAttribute('aria-hidden', 'true');
                    }
                };

                const autoReset = (button) => {
                    const delay = Number(button.dataset.resetDelay || 1600);
                    if (Number.isNaN(delay) || delay <= 0) return;
                    setTimeout(() => {
                        const modalId = button.dataset.modalConfirm;
                        if (modalId) {
                            toggleModal(modalId, false);
                        }
                        resetLoadingState(button);
                    }, delay);
                };

                document.querySelectorAll('[data-direct-action]').forEach((button) => {
                    button.addEventListener('click', () => {
                        setLoadingState(button);
                        if (button.dataset.autoReset === 'true') {
                            autoReset(button);
                        }
                    });
                });

                document.querySelectorAll('[data-modal-trigger]').forEach((button) => {
                    button.addEventListener('click', () => toggleModal(button.dataset.modalTrigger, true));
                });

                document.querySelectorAll('[data-modal-close]').forEach((button) => {
                    button.addEventListener('click', () => toggleModal(button.dataset.modalClose, false));
                });

                document.querySelectorAll('[data-modal-confirm]').forEach((button) => {
                    button.addEventListener('click', () => {
                        setLoadingState(button);
                        const modalId = button.dataset.modalConfirm;
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            modal.querySelectorAll('[data-modal-close]').forEach((closeButton) => {
                                closeButton.disabled = true;
                            });
                        }
                        if (button.dataset.autoReset === 'true') {
                            autoReset(button);
                        }
                    });
                });

                document.querySelectorAll('[data-modal-trigger]').forEach((button) => {
                    const modalId = button.dataset.modalTrigger;
                    const modal = document.getElementById(modalId);
                    if (!modal || modal.dataset.boundClick) return;
                    modal.dataset.boundClick = 'true';
                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            toggleModal(modalId, false);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layouts.employee>
