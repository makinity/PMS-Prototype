@extends('layouts.admin')

@section('main-content')
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Task Configuration</h1>
                <p class="text-sm text-slate-400">Define task categories, SLA targets, and auto-logging rules.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        data-admin-action
                        data-action-title="Create task"
                        data-action-message="Add a new task definition and SLA baseline."
                        data-action-confirm="Create task"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <i class="fa-solid fa-plus"></i>
                    New Task
                </button>
                <button type="button"
                        data-admin-action
                        data-action-title="Import task catalog"
                        data-action-message="Upload a CSV file to bulk add tasks."
                        data-action-confirm="Import"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    <i class="fa-solid fa-file-arrow-up"></i>
                    Import
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="space-y-2">
                <label class="text-xs uppercase text-slate-400">Employee</label>
                <div class="relative w-full md:w-1/2">
                    {{-- DUMMY_DATA: replace --}}
                    <input
                        type="text"
                        placeholder="Search employee by name or ID"
                        value="Juan Delacruz"
                        class="w-full rounded-full border border-slate-700 bg-slate-900 px-4 py-2.5 pr-12 text-sm text-slate-100 placeholder-slate-400 shadow-inner shadow-black/30 focus:border-blue-500 focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                        style="background:#0f172a;color:#e5e7eb;border-color:#334155;"
                    >
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"></path>
                        </svg>
                    </span>
                    {{-- DUMMY_DATA: replace --}}
                    {{-- <div class="absolute left-0 right-0 z-20 mt-2 rounded-xl border border-slate-700 bg-slate-900/90 shadow-2xl shadow-black/50">
                        <ul class="max-h-64 overflow-y-auto text-sm text-slate-200">
                            <li class="border-b border-slate-800 px-4 py-2 hover:bg-slate-800/80">
                                Juan Dela Cruz · Finance
                            </li>
                            <li class="border-b border-slate-800 px-4 py-2 hover:bg-slate-800/80">
                                Maria Santos · IT Services
                            </li>
                            <li class="px-4 py-2 hover:bg-slate-800/80">
                                Ramon Reyes · HR Management
                            </li>
                        </ul>
                    </div> --}}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Total Tasks</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-white">42</p>
                <p class="text-xs text-slate-500">Active catalog</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Critical Tasks</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-amber-300">6</p>
                <p class="text-xs text-slate-500">Auto tracked</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">SLA Coverage</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-emerald-400">94%</p>
                <p class="text-xs text-slate-500">38 tasks with SLA</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Missing Outputs</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-rose-300">2</p>
                <p class="text-xs text-slate-500">Requires setup</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Task</th>
                            <th class="px-4 py-2 text-left">Category</th>
                            <th class="px-4 py-2 text-left">Baseline</th>
                            <th class="px-4 py-2 text-left">SLA</th>
                            <th class="px-4 py-2 text-left">Output Form</th>
                            <th class="px-4 py-2 text-left">Auto Log</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">E-Bank Scanning</td>
                            <td class="px-4 py-3">Scanning</td>
                            <td class="px-4 py-3">1.8h</td>
                            <td class="px-4 py-3">2.0h</td>
                            <td class="px-4 py-3">Bank Statement Form</td>
                            <td class="px-4 py-3 text-emerald-300">Enabled</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-400">Active</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Edit task"
                                        data-action-message="Update SLA target or output requirements."
                                        data-action-confirm="Save changes"
                                        class="text-blue-400 hover:text-blue-300">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Client Form Review</td>
                            <td class="px-4 py-3">Validation</td>
                            <td class="px-4 py-3">2.2h</td>
                            <td class="px-4 py-3">2.5h</td>
                            <td class="px-4 py-3">Client Summary Report</td>
                            <td class="px-4 py-3 text-emerald-300">Enabled</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-400">Active</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Edit task"
                                        data-action-message="Update SLA target or output requirements."
                                        data-action-confirm="Save changes"
                                        class="text-blue-400 hover:text-blue-300">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Report Generation</td>
                            <td class="px-4 py-3">Reporting</td>
                            <td class="px-4 py-3">3.0h</td>
                            <td class="px-4 py-3">3.5h</td>
                            <td class="px-4 py-3">Financial Statement</td>
                            <td class="px-4 py-3 text-amber-300">Partial</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300">Draft</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Publish task"
                                        data-action-message="Activate task for employees and start tracking."
                                        data-action-confirm="Publish"
                                        class="text-blue-400 hover:text-blue-300">
                                    Publish
                                </button>
                            </td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Document Upload</td>
                            <td class="px-4 py-3">Client Follow-up</td>
                            <td class="px-4 py-3">1.2h</td>
                            <td class="px-4 py-3">1.5h</td>
                            <td class="px-4 py-3">Document Upload Form</td>
                            <td class="px-4 py-3 text-rose-300">Disabled</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-rose-500/20 px-2 py-1 text-xs text-rose-300">Paused</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Resume task"
                                        data-action-message="Re-enable auto logging and task assignment."
                                        data-action-confirm="Resume"
                                        class="text-blue-400 hover:text-blue-300">
                                    Resume
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Auto-Logging Rules</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">Start timers on task open</p>
                            <p class="text-xs text-slate-500">Auto capture when task is opened from My Tasks.</p>
                        </div>
                        <input type="checkbox" checked>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">Require output link</p>
                            <p class="text-xs text-slate-500">Block completion if output is missing.</p>
                        </div>
                        <input type="checkbox" checked>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">Pause timers on inactivity</p>
                            <p class="text-xs text-slate-500">Pause after 10 minutes of inactivity.</p>
                        </div>
                        <input type="checkbox">
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Critical Task Tracking</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <p class="font-medium text-white">E-Bank Scanning</p>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <p class="text-xs text-slate-500">Baseline 1.8h, alerts at 2.2h</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <p class="font-medium text-white">Client Form Review</p>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <p class="text-xs text-slate-500">Baseline 2.2h, alerts at 2.7h</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <p class="font-medium text-white">Report Generation</p>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <p class="text-xs text-slate-500">Baseline 3.0h, alerts at 3.8h</p>
                    </div>
                </div>
                <button type="button"
                        data-admin-action
                        data-action-title="Update critical tasks"
                        data-action-message="Adjust baseline targets and alert thresholds."
                        data-action-confirm="Save changes"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    Update thresholds
                </button>
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
