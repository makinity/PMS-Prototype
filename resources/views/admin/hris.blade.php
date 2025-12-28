<x-layouts.admin>
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">HRIS Integration</h1>
                <p class="text-sm text-slate-400">Manage HR data sync and field mappings.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        data-admin-loading="true"
                        data-loading-text="Testing..."
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    <i class="fa-solid fa-plug"></i>
                    <span data-button-label>Test Connection</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
                <button type="button"
                        data-admin-loading="true"
                        data-loading-text="Syncing..."
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <i class="fa-solid fa-rotate"></i>
                    <span data-button-label>Run Sync</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Connection</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">Connected</p>
                <p class="text-xs text-slate-500">HRIS API v2</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Last Sync</p>
                <p class="mt-2 text-2xl font-semibold text-white">Today, 6:30 AM</p>
                <p class="text-xs text-slate-500">1,210 records</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Next Sync</p>
                <p class="mt-2 text-2xl font-semibold text-white">Tomorrow, 6:30 AM</p>
                <p class="text-xs text-slate-500">Daily schedule</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Sync Issues</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">3</p>
                <p class="text-xs text-slate-500">Pending resolution</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Connection Settings</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div>
                        <label class="text-xs uppercase text-slate-400">API Endpoint</label>
                        <input type="text"
                               value="https://hris.gov/api/v2"
                               class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    </div>
                    <div>
                        <label class="text-xs uppercase text-slate-400">Authentication</label>
                        <select class="manager-filter-select mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                            <option>OAuth 2.0</option>
                            <option>API Key</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs uppercase text-slate-400">Sync Schedule</label>
                        <select class="manager-filter-select mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                            <option>Daily - 6:30 AM</option>
                            <option>Weekly - Monday</option>
                            <option>Manual</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Sync Health</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="font-medium text-white">Employee Directory</p>
                        <p class="text-xs text-slate-500">Matched 1,185 of 1,210 records</p>
                        <div class="mt-2 h-2 rounded-full bg-slate-800">
                            <div class="h-2 w-11/12 rounded-full bg-emerald-500"></div>
                        </div>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="font-medium text-white">Department Mapping</p>
                        <p class="text-xs text-slate-500">Missing 3 department codes</p>
                        <div class="mt-2 h-2 rounded-full bg-slate-800">
                            <div class="h-2 w-3/4 rounded-full bg-amber-500"></div>
                        </div>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="font-medium text-white">Position Mapping</p>
                        <p class="text-xs text-slate-500">All positions mapped</p>
                        <div class="mt-2 h-2 rounded-full bg-slate-800">
                            <div class="h-2 w-full rounded-full bg-emerald-500"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Field Mapping</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">HRIS Field</th>
                            <th class="px-4 py-2 text-left">PMS Field</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">employee_id</td>
                            <td class="px-4 py-3">employee_code</td>
                            <td class="px-4 py-3 text-emerald-300">Mapped</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Edit mapping"
                                        data-action-message="Update the PMS field mapping."
                                        data-action-confirm="Save"
                                        class="text-blue-400 hover:text-blue-300">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">department_code</td>
                            <td class="px-4 py-3">department</td>
                            <td class="px-4 py-3 text-amber-300">Needs Review</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-loading="true"
                                        data-loading-text="Resolving..."
                                        class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300">
                                    <span data-button-label>Resolve</span>
                                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">position_title</td>
                            <td class="px-4 py-3">position</td>
                            <td class="px-4 py-3 text-emerald-300">Mapped</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Edit mapping"
                                        data-action-message="Update the PMS field mapping."
                                        data-action-confirm="Save"
                                        class="text-blue-400 hover:text-blue-300">
                                    Edit
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
</x-layouts.admin>
