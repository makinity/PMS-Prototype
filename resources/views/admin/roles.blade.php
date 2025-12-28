<x-layouts.admin>
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Roles &amp; Permissions</h1>
                <p class="text-sm text-slate-400">Define access scopes and permission sets for every module.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        data-admin-action
                        data-action-title="Create new role"
                        data-action-message="Start a new custom role and assign permissions."
                        data-action-confirm="Create role"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <i class="fa-solid fa-user-shield"></i>
                    Create Role
                </button>
                <button type="button"
                        data-admin-loading="true"
                        data-loading-text="Exporting..."
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    <i class="fa-solid fa-file-export"></i>
                    <span data-button-label>Export</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Total Roles</p>
                <p class="mt-2 text-2xl font-semibold text-white">8</p>
                <p class="text-xs text-slate-500">3 custom roles</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Privileged Roles</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">2</p>
                <p class="text-xs text-slate-500">Admin level</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Permissions Sets</p>
                <p class="mt-2 text-2xl font-semibold text-white">14</p>
                <p class="text-xs text-slate-500">Across 9 modules</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Pending Updates</p>
                <p class="mt-2 text-2xl font-semibold text-rose-300">1</p>
                <p class="text-xs text-slate-500">Awaiting approval</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Role Directory</h2>
                <span class="text-xs text-slate-500">Last updated: Today 9:20 AM</span>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Role</th>
                            <th class="px-4 py-2 text-left">Users</th>
                            <th class="px-4 py-2 text-left">Scope</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">
                                <p class="font-medium text-white">System Admin</p>
                                <p class="text-xs text-slate-500">Full access, security override</p>
                            </td>
                            <td class="px-4 py-3">4</td>
                            <td class="px-4 py-3">All modules</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-400">Active</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="View admin role"
                                        data-action-message="Review permissions and assigned users."
                                        data-action-confirm="Open role"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">
                                <p class="font-medium text-white">Manager</p>
                                <p class="text-xs text-slate-500">Analytics, approvals, team oversight</p>
                            </td>
                            <td class="px-4 py-3">38</td>
                            <td class="px-4 py-3">Manager console</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-400">Active</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Edit manager role"
                                        data-action-message="Adjust permissions for manager access."
                                        data-action-confirm="Edit role"
                                        class="text-blue-400 hover:text-blue-300">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">
                                <p class="font-medium text-white">Employee</p>
                                <p class="text-xs text-slate-500">Task logging, output submissions</p>
                            </td>
                            <td class="px-4 py-3">1,102</td>
                            <td class="px-4 py-3">Employee portal</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-400">Active</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="View employee role"
                                        data-action-message="Review permissions for the employee role."
                                        data-action-confirm="Open role"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">
                                <p class="font-medium text-white">Audit Reviewer</p>
                                <p class="text-xs text-slate-500">Read-only audit and reports</p>
                            </td>
                            <td class="px-4 py-3">6</td>
                            <td class="px-4 py-3">Audit modules</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300">Pending Update</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Review pending update"
                                        data-action-message="Approve or reject the latest permission change."
                                        data-action-confirm="Review update"
                                        class="text-blue-400 hover:text-blue-300">
                                    Review
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Permission Matrix</h2>
                <span class="text-xs text-slate-500">Preview of module access</span>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Module</th>
                            <th class="px-4 py-2 text-left">Employee</th>
                            <th class="px-4 py-2 text-left">Manager</th>
                            <th class="px-4 py-2 text-left">Admin</th>
                            <th class="px-4 py-2 text-left">Audit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Task Logging</td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" disabled></td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Performance Analytics</td>
                            <td class="px-4 py-3"><input type="checkbox" disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">User Management</td>
                            <td class="px-4 py-3"><input type="checkbox" disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" disabled></td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Audit Trail</td>
                            <td class="px-4 py-3"><input type="checkbox" disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
                            <td class="px-4 py-3"><input type="checkbox" checked disabled></td>
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
