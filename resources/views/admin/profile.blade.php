<x-layouts.admin>
    <section class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-white">Profile &amp; Security</h1>
            <p class="text-sm text-slate-400">Manage administrator account details and security settings.</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-600 via-blue-600 to-emerald-500 text-white text-lg font-semibold">
                        SA
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-white">PMS Administrator</p>
                        <p class="text-sm text-slate-400">System Admin</p>
                    </div>
                </div>
                <button type="button"
                        data-admin-action
                        data-action-title="Update profile photo"
                        data-action-message="Upload a new profile photo (JPG or PNG)."
                        data-action-confirm="Upload"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    <i class="fa-solid fa-camera"></i>
                    Update Photo
                </button>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-xs uppercase text-slate-400">Full Name</label>
                    <input type="text"
                           value="PMS Administrator"
                           class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Email</label>
                    <input type="email"
                           value="admin@pms.gov"
                           class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Department</label>
                    <input type="text"
                           value="Information Systems"
                           class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Contact</label>
                    <input type="text"
                           value="+63 917 000 1234"
                           class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button"
                        data-admin-action
                        data-action-title="Save profile"
                        data-action-message="Save profile changes for this administrator."
                        data-action-confirm="Save"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <i class="fa-solid fa-check"></i>
                    Save Profile
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Security</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div>
                        <label class="text-xs uppercase text-slate-400">Current Password</label>
                        <input type="password"
                               placeholder="Enter current password"
                               class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    </div>
                    <div>
                        <label class="text-xs uppercase text-slate-400">New Password</label>
                        <input type="password"
                               placeholder="Enter new password"
                               class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    </div>
                    <div>
                        <label class="text-xs uppercase text-slate-400">Confirm Password</label>
                        <input type="password"
                               placeholder="Confirm new password"
                               class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    </div>
                    <button type="button"
                            data-admin-action
                            data-action-title="Update password"
                            data-action-message="Change administrator password and update active sessions."
                            data-action-confirm="Update"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                        <i class="fa-solid fa-key"></i>
                        Update Password
                    </button>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Access Controls</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">Multi-factor authentication</p>
                            <p class="text-xs text-slate-500">Required for admin roles.</p>
                        </div>
                        <input type="checkbox" checked>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">Session timeout</p>
                            <p class="text-xs text-slate-500">Auto sign-out after 30 minutes.</p>
                        </div>
                        <input type="checkbox" checked>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="font-medium text-white">API Access Tokens</p>
                        <p class="text-xs text-slate-500">3 active tokens</p>
                        <button type="button"
                                data-admin-action
                                data-action-title="Manage access tokens"
                                data-action-message="Review and rotate API access tokens."
                                data-action-confirm="Open tokens"
                                class="mt-3 text-blue-400 hover:text-blue-300">
                            Manage tokens
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Recent Sessions</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Device</th>
                            <th class="px-4 py-2 text-left">Location</th>
                            <th class="px-4 py-2 text-left">Last Active</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Chrome - Windows</td>
                            <td class="px-4 py-3">Manila, PH</td>
                            <td class="px-4 py-3">Active now</td>
                            <td class="px-4 py-3 text-emerald-300">Current</td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-slate-500">Primary</span>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Safari - iOS</td>
                            <td class="px-4 py-3">Quezon City, PH</td>
                            <td class="px-4 py-3">3 hours ago</td>
                            <td class="px-4 py-3 text-amber-300">Active</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-admin-action
                                        data-action-title="End session"
                                        data-action-message="Sign out this device from the system."
                                        data-action-confirm="Sign out"
                                        class="text-rose-300 hover:text-rose-200">
                                    End session
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
