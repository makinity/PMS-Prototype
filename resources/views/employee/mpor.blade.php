@extends('layouts.employee')

@section('main-content')
    <section class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Monthly Performance Output Report</p>
                <h1 class="mt-1 text-2xl font-bold text-white">MPOR - December 2025</h1>
                <p class="text-sm text-slate-400 mt-1">
                    Generated from ORS logs and linked outputs. Ratings stay system-calculated to keep scores objective.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full border border-emerald-600/50 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                    Submission open
                </span>
                <span class="rounded-full border border-blue-700/60 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                    Prototype data
                </span>
            </div>
        </div>

        <!-- Metrics -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">ORS logs (system-tracked)</p>
                <p class="mt-1 text-2xl font-semibold text-white">18</p>
                <p class="text-xs text-slate-500">Auto-imported this month</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Outputs linked (system-tracked)</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-300">15</p>
                <p class="text-xs text-slate-500">Ready for MPOR</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Pending validation (validation-based)</p>
                <p class="mt-1 text-2xl font-semibold text-amber-300">2</p>
                <p class="text-xs text-slate-500">Need supervisor check</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Outputs validated (validation-based)</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-300">13</p>
                <p class="text-xs text-slate-500">Supervisor-reviewed</p>
            </div>
        </div>

        <!-- Outputs Table -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <h2 class="text-lg font-semibold text-white">Outputs ready for MPOR</h2>
                    <p class="text-xs text-slate-400">Only ORS entries with linked outputs are included.</p>
                </div>
                <span class="text-xs text-slate-400">Cutoff: Dec 29, 2025</span>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-800">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Output</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Request ID</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Logged in ORS</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Validation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">E-Bank Scanning</td>
                            <td class="px-4 py-3 text-slate-300">REQ-2025-018</td>
                            <td class="px-4 py-3 text-slate-300">Dec 18, 2025</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-300 border border-emerald-600/50">Validated</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">Client Form Review</td>
                            <td class="px-4 py-3 text-slate-300">REQ-2025-021</td>
                            <td class="px-4 py-3 text-slate-300">Dec 20, 2025</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-blue-500/10 px-2.5 py-1 text-xs font-semibold text-blue-200 border border-blue-600/50">For review</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">Report Generation</td>
                            <td class="px-4 py-3 text-slate-300">REQ-2025-027</td>
                            <td class="px-4 py-3 text-slate-300">Dec 21, 2025</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-200 border border-amber-600/50">Missing link</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">Client Follow-up</td>
                            <td class="px-4 py-3 text-slate-300">REQ-2025-031</td>
                            <td class="px-4 py-3 text-slate-300">Dec 22, 2025</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-300 border border-emerald-600/50">Validated</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Compliance + Actions -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Submission health check</h2>
                        <p class="text-xs text-slate-400">MPOR only accepts auto-timed ORS tasks with linked outputs.</p>
                    </div>
                    <span class="text-xs text-slate-400">Live checks</span>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Auto-timed tasks</p>
                            <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-200 border border-emerald-600/50">Pass</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Start/end times captured from ORS.</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Output links</p>
                            <span class="rounded-full bg-amber-500/10 px-2 py-0.5 text-[11px] font-semibold text-amber-200 border border-amber-600/50">2 missing</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Resolve before submitting for SMPOR consolidation.</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Duplicate entries</p>
                            <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-200 border border-emerald-600/50">None</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">No overlapping logs detected.</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Supervisor validation</p>
                            <span class="rounded-full bg-blue-500/10 px-2 py-0.5 text-[11px] font-semibold text-blue-200 border border-blue-600/50">In review</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Pending checks for 2 outputs.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
                <h2 class="text-lg font-semibold text-white">Actions</h2>
                <p class="text-xs text-slate-400">Exports lock in the captured timestamps and validations. Final consolidation is system-generated after supervisor validation.</p>
                <button type="button" data-direct-action data-auto-reset="true" class="w-full rounded-lg bg-emerald-500 px-3 py-2 text-sm font-semibold text-slate-900 transition hover:bg-emerald-600">
                    Export MPOR
                </button>
                <button type="button" data-modal-trigger="send-to-smpor-modal" class="w-full rounded-lg border border-slate-700 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Mark MPOR Complete (Supervisor Validation)
                </button>
                <button type="button" data-direct-action data-auto-reset="true" class="w-full rounded-lg border border-slate-800 bg-slate-950/70 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-900">
                    View ORS log source
                </button>
            </div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 text-xs text-slate-400">
            Note: Manual edits are disabled. Ratings, time stamps, and task durations remain system-generated to keep the MPOR objective.
        </div>
    </section>

    <div id="send-to-smpor-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900/95 shadow-2xl shadow-black/40">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-4 py-3">
                <div>
                    <h3 class="text-lg font-semibold text-white">Mark MPOR Complete</h3>
                    <p class="mt-1 text-sm text-slate-400">Flag this MPOR as ready for supervisor validation. SMPOR consolidation is system-generated after validation.</p>
                </div>
                <button type="button" data-modal-close="send-to-smpor-modal" class="rounded-full p-1 text-slate-400 transition hover:bg-slate-800 hover:text-slate-200">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>
            <div class="space-y-3 px-4 py-4 text-sm text-slate-300">
                <p>Submitting will share current entries for supervisor validation. Final SMPOR consolidation occurs automatically after validation and lock.</p>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-800 px-4 py-3">
                <button type="button" data-modal-close="send-to-smpor-modal" class="rounded-lg border border-slate-700 bg-slate-800/80 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Close
                </button>
                <button type="button" data-modal-confirm="send-to-smpor-modal" data-auto-reset="true" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Submit Request
                </button>
            </div>
        </div>
    </div>

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
@endsection
