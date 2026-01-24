@extends('layouts.admin')

@section('main-content')
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Data Export</h1>
                <p class="text-sm text-slate-400">Generate secure exports for analytics and compliance.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        data-admin-action
                        data-action-title="Create export"
                        data-action-message="Generate a new data export package."
                        data-action-confirm="Start export"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <i class="fa-solid fa-file-export"></i>
                    New Export
                </button>
                <button type="button"
                        data-admin-action
                        data-action-title="Schedule export"
                        data-action-message="Schedule recurring exports with automated delivery."
                        data-action-confirm="Schedule"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    <i class="fa-solid fa-clock"></i>
                    Schedule
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">Dataset</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Datasets</option>
                        <option>Task Logs</option>
                        <option>Performance Ratings</option>
                        <option>User Accounts</option>
                        <option>Audit Trail</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Format</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>CSV</option>
                        <option>Excel</option>
                        <option>JSON</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Frequency</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>One-time</option>
                        <option>Weekly</option>
                        <option>Monthly</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Delivery</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>Download</option>
                        <option>Email</option>
                        <option>SFTP</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Scheduled Exports</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-white">6</p>
                <p class="text-xs text-slate-500">Active schedules</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Last Export</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-emerald-400">Success</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="text-xs text-slate-500">Today, 07:30 AM</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Failed Jobs</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-rose-300">1</p>
                <p class="text-xs text-slate-500">Retry queued</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Retention</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-white">30 days</p>
                <p class="text-xs text-slate-500">Export archives</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Export Catalog</h2>
            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 text-sm text-slate-300">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <p class="font-medium text-white">Task Logs</p>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <p class="text-xs text-slate-500">Includes start/end timestamps and status</p>
                        </div>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-300">CSV</span>
                    </div>
                    <button type="button"
                            data-admin-action
                            data-action-title="Export task logs"
                            data-action-message="Generate CSV for task logs."
                            data-action-confirm="Generate"
                            class="mt-3 text-blue-400 hover:text-blue-300">
                        Generate export
                    </button>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <p class="font-medium text-white">Performance Ratings</p>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <p class="text-xs text-slate-500">Scores by employee and period</p>
                        </div>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-300">Excel</span>
                    </div>
                    <button type="button"
                            data-admin-action
                            data-action-title="Export ratings"
                            data-action-message="Generate Excel export for ratings."
                            data-action-confirm="Generate"
                            class="mt-3 text-blue-400 hover:text-blue-300">
                        Generate export
                    </button>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <p class="font-medium text-white">User Accounts</p>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <p class="text-xs text-slate-500">Account status and roles</p>
                        </div>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-300">CSV</span>
                    </div>
                    <button type="button"
                            data-admin-action
                            data-action-title="Export user accounts"
                            data-action-message="Generate CSV for user directory."
                            data-action-confirm="Generate"
                            class="mt-3 text-blue-400 hover:text-blue-300">
                        Generate export
                    </button>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <p class="font-medium text-white">Audit Trail</p>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <p class="text-xs text-slate-500">Compliance logs and access history</p>
                        </div>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-300">JSON</span>
                    </div>
                    <button type="button"
                            data-admin-action
                            data-action-title="Export audit trail"
                            data-action-message="Generate JSON export for audit trail."
                            data-action-confirm="Generate"
                            class="mt-3 text-blue-400 hover:text-blue-300">
                        Generate export
                    </button>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Export History</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Dataset</th>
                            <th class="px-4 py-2 text-left">Format</th>
                            <th class="px-4 py-2 text-left">Requested By</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">Today, 07:30 AM</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">Task Logs</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">CSV</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">System Admin</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3 text-emerald-300">Ready</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-loading="true"
                                        data-loading-text="Downloading..."
                                        class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300">
                                    <span data-button-label>Download</span>
                                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">Yesterday, 5:10 PM</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">Performance Ratings</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">Excel</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">Maria Santos</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3 text-amber-300">Processing</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="View job"
                                        data-action-message="Export job is running. Estimated 3 minutes."
                                        data-action-confirm="Close"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">Aug 12, 2025</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">Audit Trail</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">JSON</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3">System Admin</td>
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <td class="px-4 py-3 text-rose-300">Failed</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-loading="true"
                                        data-loading-text="Retrying..."
                                        class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300">
                                    <span data-button-label>Retry</span>
                                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
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
