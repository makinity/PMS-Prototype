<x-layouts.manager>
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">My Team</h1>
                <p class="text-sm text-slate-400">Live status, access, and performance setup for monitored employees.</p>
            </div>
            <div class="text-xs text-slate-400">Last sync: 2 minutes ago</div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Team Members</p>
                <p class="mt-2 text-2xl font-semibold text-white">12</p>
                <p class="text-xs text-slate-500">Active: 10</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Running Tasks</p>
                <p class="mt-2 text-2xl font-semibold text-white">7</p>
                <p class="text-xs text-slate-500">Auto-tracked</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Output Compliance</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">92%</p>
                <p class="text-xs text-slate-500">Missing outputs: 2</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Avg Rating</p>
                <p class="mt-2 text-2xl font-semibold text-white">4.3</p>
                <p class="text-xs text-slate-500">System calculated</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">Team</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Teams</option>
                        <option>Operations</option>
                        <option>Finance</option>
                        <option>IT Support</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Role</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Roles</option>
                        <option>Processor</option>
                        <option>Reviewer</option>
                        <option>Analyst</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Status</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Idle</option>
                        <option>Offline</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Employee</th>
                            <th class="px-4 py-2 text-left">Role</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Active Task</th>
                            <th class="px-4 py-2 text-left">Output Link</th>
                            <th class="px-4 py-2 text-left">Rating</th>
                            <th class="px-4 py-2 text-left">Last Activity</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                            <td class="px-4 py-3">Juan Dela Cruz</td>
                            <td class="px-4 py-3">Processor</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-400">Active</span></td>
                            <td class="px-4 py-3">E-Bank Scanning</td>
                            <td class="px-4 py-3 text-emerald-300">Linked</td>
                            <td class="px-4 py-3">4.5</td>
                            <td class="px-4 py-3 text-slate-400">5 mins ago</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="View employee"
                                        data-action-message="Open employee profile, task logs, and output link status."
                                        data-action-confirm="Open profile"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                            <td class="px-4 py-3">Maria Santos</td>
                            <td class="px-4 py-3">Reviewer</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-400">Idle</span></td>
                            <td class="px-4 py-3">Client Form Review</td>
                            <td class="px-4 py-3 text-amber-300">Pending</td>
                            <td class="px-4 py-3">4.1</td>
                            <td class="px-4 py-3 text-slate-400">12 mins ago</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="View employee"
                                        data-action-message="Open employee profile, task logs, and output link status."
                                        data-action-confirm="Open profile"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                            <td class="px-4 py-3">Pedro Reyes</td>
                            <td class="px-4 py-3">Analyst</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-slate-500/20 px-2 py-1 text-xs text-slate-300">Offline</span></td>
                            <td class="px-4 py-3">Report Submission</td>
                            <td class="px-4 py-3 text-rose-300">Missing</td>
                            <td class="px-4 py-3">3.6</td>
                            <td class="px-4 py-3 text-slate-400">Yesterday</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="View employee"
                                        data-action-message="Open employee profile, task logs, and output link status."
                                        data-action-confirm="Open profile"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
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
                    <h2 class="text-lg font-semibold text-white">Access and Metrics</h2>
                    <p class="text-sm text-slate-400">Prototype view of permissions and tracking parameters.</p>
                </div>
                <span class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-300">Manager View</span>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <h3 class="text-sm font-semibold text-white">Role Permissions</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-300">
                        <li>Processor: log tasks, submit outputs</li>
                        <li>Reviewer: validate outputs, request revisions</li>
                        <li>Analyst: generate performance reports</li>
                    </ul>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <h3 class="text-sm font-semibold text-white">Custom Metrics</h3>
                    <div class="mt-3 space-y-2 text-sm text-slate-300">
                        <div class="flex items-center justify-between">
                            <span>Output timeliness weight</span>
                            <span class="text-emerald-300">40%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Quality compliance weight</span>
                            <span class="text-emerald-300">35%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Volume completion weight</span>
                            <span class="text-emerald-300">25%</span>
                        </div>
                    </div>
                    <button type="button"
                            data-manager-action
                            data-action-title="Update metrics"
                            data-action-message="Adjust tracking weights and custom parameters for performance scoring."
                            data-action-confirm="Save changes"
                            class="mt-4 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Update parameters
                    </button>
                </div>
            </div>
        </div>
    </section>

    <div id="manager-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="manager-action-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="manager-action-body" class="mt-1 text-sm text-slate-400">Prototype action preview.</p>
                </div>
                <button type="button" data-manager-modal-close class="text-slate-400 hover:text-white">x</button>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-manager-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
                <button type="button" id="manager-action-confirm" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">Proceed</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('manager-action-modal');
            const title = document.getElementById('manager-action-title');
            const body = document.getElementById('manager-action-body');
            const confirmBtn = document.getElementById('manager-action-confirm');

            if (!modal || !title || !body || !confirmBtn) {
                return;
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function openModal(trigger) {
                title.textContent = trigger.dataset.actionTitle || 'Action';
                body.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
                confirmBtn.textContent = trigger.dataset.actionConfirm || 'Proceed';
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            document.querySelectorAll('[data-manager-action]').forEach((button) => {
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

            modal.querySelectorAll('[data-manager-modal-close]').forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            confirmBtn.addEventListener('click', closeModal);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });
        });
    </script>
    @endpush
</x-layouts.manager>

