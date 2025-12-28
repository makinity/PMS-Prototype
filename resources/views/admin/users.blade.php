<x-layouts.admin>
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Users</h1>
                <p class="text-sm text-slate-400">Manage accounts, roles, and access across the system.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        data-admin-action
                        data-action-title="Invite user"
                        data-action-message="Send an invite link with the selected role and access scope."
                        data-action-confirm="Send invite"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <i class="fa-solid fa-user-plus"></i>
                    Invite User
                </button>
                <button type="button"
                        data-admin-action
                        data-action-title="Bulk import"
                        data-action-message="Upload a CSV file to provision multiple user accounts."
                        data-action-confirm="Upload CSV"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    <i class="fa-solid fa-file-arrow-up"></i>
                    Bulk Import
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="min-w-[220px] flex-1">
                    <label class="text-xs uppercase text-slate-400">Search</label>
                    <input type="text"
                           placeholder="Search name or email"
                           class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500">
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Role</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Roles</option>
                        <option>Employee</option>
                        <option>Manager</option>
                        <option>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Status</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Pending Invite</option>
                        <option>Suspended</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Department</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Departments</option>
                        <option>Operations</option>
                        <option>Finance</option>
                        <option>IT Support</option>
                        <option>HR</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Total Users</p>
                <p class="mt-2 text-2xl font-semibold text-white">1,247</p>
                <p class="text-xs text-slate-500">Across all units</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Active</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">1,096</p>
                <p class="text-xs text-slate-500">Online in last 7 days</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Pending Invites</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">38</p>
                <p class="text-xs text-slate-500">Awaiting activation</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Suspended</p>
                <p class="mt-2 text-2xl font-semibold text-rose-300">12</p>
                <p class="text-xs text-slate-500">Manual review required</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">User</th>
                            <th class="px-4 py-2 text-left">Role</th>
                            <th class="px-4 py-2 text-left">Department</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Last Login</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-white">Juan Dela Cruz</p>
                                <p class="text-xs text-slate-500">juan.delacruz@email.com</p>
                            </td>
                            <td class="px-4 py-3">Employee</td>
                            <td class="px-4 py-3">Operations</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-400">Active</span>
                            </td>
                            <td class="px-4 py-3 text-slate-400">Today, 9:12 AM</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="View user profile"
                                        data-action-message="Open user profile, access logs, and task history."
                                        data-action-confirm="Open profile"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-white">Maria Santos</p>
                                <p class="text-xs text-slate-500">maria.santos@email.com</p>
                            </td>
                            <td class="px-4 py-3">Manager</td>
                            <td class="px-4 py-3">Operations</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300">Pending Invite</span>
                            </td>
                            <td class="px-4 py-3 text-slate-400">Invite sent 2 days ago</td>
                            <td class="px-4 py-3 flex items-center gap-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Resend invite"
                                        data-action-message="Resend activation link to the user."
                                        data-action-confirm="Resend"
                                        class="text-blue-400 hover:text-blue-300">
                                    Resend
                                </button>
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Cancel invite"
                                        data-action-message="Revoke the pending invite and remove access."
                                        data-action-confirm="Cancel invite"
                                        class="text-rose-300 hover:text-rose-200">
                                    Cancel
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-white">Pedro Reyes</p>
                                <p class="text-xs text-slate-500">pedro.reyes@email.com</p>
                            </td>
                            <td class="px-4 py-3">Employee</td>
                            <td class="px-4 py-3">Finance</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-rose-500/20 px-2 py-1 text-xs text-rose-300">Suspended</span>
                            </td>
                            <td class="px-4 py-3 text-slate-400">Last login 5 days ago</td>
                            <td class="px-4 py-3 flex items-center gap-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Restore access"
                                        data-action-message="Reactivate this account and restore access."
                                        data-action-confirm="Restore"
                                        class="text-emerald-300 hover:text-emerald-200">
                                    Restore
                                </button>
                                <button type="button"
                                        data-admin-action
                                        data-action-title="Force password reset"
                                        data-action-message="Require the user to reset the password on next login."
                                        data-action-confirm="Force reset"
                                        class="text-blue-400 hover:text-blue-300">
                                    Reset
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Access Requests</h2>
                    <p class="text-sm text-slate-400">Pending approvals for elevated access and role changes.</p>
                </div>
                <span class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-300">3 pending</span>
            </div>
            <div class="mt-4 space-y-3 text-sm text-slate-300">
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <div>
                        <p class="font-medium text-white">Request: Manager role for Ana Lim</p>
                        <p class="text-xs text-slate-500">Requested by HR - 2 hours ago</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button"
                                data-admin-loading="true"
                                data-loading-text="Approving..."
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500">
                            <span data-button-label>Approve</span>
                            <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>
                        <button type="button"
                                data-admin-action
                                data-action-title="Reject access request"
                                data-action-message="Reject the request and notify the requester."
                                data-action-confirm="Reject"
                                class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-800">
                            Reject
                        </button>
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <div>
                        <p class="font-medium text-white">Request: Temporary admin for Miguel Castro</p>
                        <p class="text-xs text-slate-500">Requested by IT Support - Yesterday</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button"
                                data-admin-loading="true"
                                data-loading-text="Approving..."
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500">
                            <span data-button-label>Approve</span>
                            <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>
                        <button type="button"
                                data-admin-action
                                data-action-title="Reject access request"
                                data-action-message="Reject the request and provide a reason."
                                data-action-confirm="Reject"
                                class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-800">
                            Reject
                        </button>
                    </div>
                </div>
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
