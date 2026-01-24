@extends('layouts.admin')

@section('main-content')
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">System Logs</h1>
                <p class="text-sm text-slate-400">Monitor infrastructure events and service health.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        data-admin-loading="true"
                        data-loading-text="Downloading..."
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    <span data-button-label>Download</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
                <button type="button"
                        data-admin-loading="true"
                        data-loading-text="Clearing..."
                        class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-500">
                    <i class="fa-solid fa-trash"></i>
                    <span data-button-label>Clear</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">Severity</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All</option>
                        <option>Error</option>
                        <option>Warning</option>
                        <option>Info</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Service</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Services</option>
                        <option>Auth</option>
                        <option>Notifications</option>
                        <option>Analytics</option>
                        <option>Task Engine</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Date Range</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>Today</option>
                        <option selected>Last 7 Days</option>
                        <option>Last 30 Days</option>
                    </select>
                </div>
                <div class="min-w-[220px] flex-1">
                    <label class="text-xs uppercase text-slate-400">Search</label>
                    <input type="text"
                           placeholder="Search log message"
                           class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Errors</p>
                <p class="mt-2 text-2xl font-semibold text-rose-300">1</p>
                <p class="text-xs text-slate-500">Last 7 days</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Warnings</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">3</p>
                <p class="text-xs text-slate-500">Last 7 days</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Info Events</p>
                <p class="mt-2 text-2xl font-semibold text-white">22</p>
                <p class="text-xs text-slate-500">Last 7 days</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Queue Backlog</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">Low</p>
                <p class="text-xs text-slate-500">Current</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Timestamp</th>
                            <th class="px-4 py-2 text-left">Level</th>
                            <th class="px-4 py-2 text-left">Service</th>
                            <th class="px-4 py-2 text-left">Message</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Today, 10:02 AM</td>
                            <td class="px-4 py-3 text-rose-300">Error</td>
                            <td class="px-4 py-3">Notifications</td>
                            <td class="px-4 py-3">Failed to send email to 4 recipients</td>
                            <td class="px-4 py-3 text-amber-300">Retrying</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Log detail"
                                        data-action-message="Email service retry scheduled at 10:05 AM."
                                        data-action-confirm="Close"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Today, 09:44 AM</td>
                            <td class="px-4 py-3 text-amber-300">Warning</td>
                            <td class="px-4 py-3">Analytics</td>
                            <td class="px-4 py-3">Delayed processing for team metrics</td>
                            <td class="px-4 py-3 text-amber-300">Delayed</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Log detail"
                                        data-action-message="Analytics queue at 72% capacity."
                                        data-action-confirm="Close"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Today, 08:20 AM</td>
                            <td class="px-4 py-3 text-slate-300">Info</td>
                            <td class="px-4 py-3">Auth</td>
                            <td class="px-4 py-3">Scheduled token cleanup completed</td>
                            <td class="px-4 py-3 text-emerald-300">Success</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Log detail"
                                        data-action-message="Removed 120 expired tokens."
                                        data-action-confirm="Close"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="admin-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="admin-action-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="admin-action-body" class="mt-1 text-sm text-slate-400">Prototype action preview.</p>
                </div>
                <button type="button" data-admin-modal-close class="text-slate-400 hover:text-white">x</button>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-admin-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
                <button type="button" id="admin-action-confirm" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <span data-button-label>Proceed</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('admin-action-modal');
        const title = document.getElementById('admin-action-title');
        const body = document.getElementById('admin-action-body');
        const confirmBtn = document.getElementById('admin-action-confirm');

        if (!modal || !title || !body || !confirmBtn) {
            return;
        }

        function setButtonLoading(button, isLoading, loadingText) {
            if (!button) {
                return;
            }
            const label = button.querySelector('[data-button-label]');
            const spinner = button.querySelector('[data-button-spinner]');
            if (label && !button.dataset.originalLabel) {
                button.dataset.originalLabel = label.textContent.trim();
            }

            if (isLoading) {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-wait');
                if (spinner) {
                    spinner.classList.remove('hidden');
                }
                if (label && loadingText) {
                    label.textContent = loadingText;
                }
            } else {
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-wait');
                if (spinner) {
                    spinner.classList.add('hidden');
                }
                if (label && button.dataset.originalLabel) {
                    label.textContent = button.dataset.originalLabel;
                }
            }
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            setButtonLoading(confirmBtn, false);
        }

        function openModal(trigger) {
            const label = confirmBtn.querySelector('[data-button-label]');
            title.textContent = trigger.dataset.actionTitle || 'Action';
            body.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
            if (label) {
                label.textContent = trigger.dataset.actionConfirm || 'Proceed';
                confirmBtn.dataset.originalLabel = label.textContent.trim();
            }
            confirmBtn.dataset.loadingText = trigger.dataset.actionLoading || 'Working...';
            setButtonLoading(confirmBtn, false);
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        document.querySelectorAll('[data-admin-action]').forEach((button) => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openModal(button);
            });
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        modal.querySelectorAll('[data-admin-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        confirmBtn.addEventListener('click', function () {
            setButtonLoading(confirmBtn, true, confirmBtn.dataset.loadingText || 'Working...');
            setTimeout(() => {
                setButtonLoading(confirmBtn, false);
                closeModal();
            }, 1200);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    });
    </script>
    @endpush
@endsection
