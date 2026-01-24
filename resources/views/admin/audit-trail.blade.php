@extends('layouts.admin')

@section('main-content')
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Audit Trail</h1>
                <p class="text-sm text-slate-400">Track privileged actions and system access history.</p>
            </div>
            <button type="button"
                    data-admin-loading="true"
                    data-loading-text="Exporting..."
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                <i class="fa-solid fa-file-export"></i>
                <span data-button-label>Export</span>
                <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">User</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Users</option>
                        <option>PMS Administrator</option>
                        <option>Maria Santos</option>
                        <option>Juan Dela Cruz</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Module</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Modules</option>
                        <option>User Management</option>
                        <option>Performance Metrics</option>
                        <option>Task Configuration</option>
                        <option>Audit</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Action</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Actions</option>
                        <option>Create</option>
                        <option>Update</option>
                        <option>Delete</option>
                        <option>Login</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Date Range</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>Last 7 Days</option>
                        <option selected>Last 30 Days</option>
                        <option>Last 90 Days</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Events Logged</p>
                <p class="mt-2 text-2xl font-semibold text-white">128</p>
                <p class="text-xs text-slate-500">Last 30 days</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Privileged Actions</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">6</p>
                <p class="text-xs text-slate-500">Admin level</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Failed Logins</p>
                <p class="mt-2 text-2xl font-semibold text-rose-300">1</p>
                <p class="text-xs text-slate-500">Auto locked: 0</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Policy Changes</p>
                <p class="mt-2 text-2xl font-semibold text-white">2</p>
                <p class="text-xs text-slate-500">Pending review: 0</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Timestamp</th>
                            <th class="px-4 py-2 text-left">User</th>
                            <th class="px-4 py-2 text-left">Action</th>
                            <th class="px-4 py-2 text-left">Module</th>
                            <th class="px-4 py-2 text-left">IP Address</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Today, 09:45 AM</td>
                            <td class="px-4 py-3">PMS Administrator</td>
                            <td class="px-4 py-3">Updated roles</td>
                            <td class="px-4 py-3">User Management</td>
                            <td class="px-4 py-3">192.168.1.45</td>
                            <td class="px-4 py-3 text-emerald-300">Success</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Audit detail"
                                        data-action-message="Role permissions updated for Manager."
                                        data-action-confirm="Close"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Today, 09:15 AM</td>
                            <td class="px-4 py-3">Maria Santos</td>
                            <td class="px-4 py-3">Queued validation for Report Generation</td>
                            <td class="px-4 py-3">Task Management</td>
                            <td class="px-4 py-3">192.168.1.20</td>
                            <td class="px-4 py-3 text-emerald-300">Success</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Audit detail"
                                        data-action-message="Validation queued for REQ-2025-012."
                                        data-action-confirm="Close"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Today, 08:10 AM</td>
                            <td class="px-4 py-3">Pedro Reyes</td>
                            <td class="px-4 py-3">Login attempt</td>
                            <td class="px-4 py-3">Authentication</td>
                            <td class="px-4 py-3">192.168.1.77</td>
                            <td class="px-4 py-3 text-rose-300">Failed</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Audit detail"
                                        data-action-message="Incorrect password. Account flagged for review."
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
