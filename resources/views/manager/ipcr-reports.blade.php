<x-layouts.manager>
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">IPCR Reports</h1>
                <p class="text-sm text-slate-400">Objective reports generated from ORS logs and linked outputs.</p>
            </div>
            <div class="flex gap-2">
                <button type="button"
                        data-manager-action
                        data-action-title="Export IPCR reports"
                        data-action-message="Download all IPCR reports for the selected filters as a ZIP file."
                        data-action-confirm="Export reports"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    Export All
                </button>
                <button type="button"
                        data-manager-action
                        data-action-title="Generate IPCR report"
                        data-action-message="Create a new IPCR report using auto-logged ORS and output data."
                        data-action-confirm="Generate"
                        class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    Generate New
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">Rating Period</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>This Quarter</option>
                        <option selected>Current Year</option>
                        <option>Previous Year</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Status</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Status</option>
                        <option>Generated</option>
                        <option>Pending Review</option>
                        <option>Approved</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Team</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Teams</option>
                        <option>Operations</option>
                        <option>Finance</option>
                        <option>IT Support</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Reports Generated</p>
                <p class="mt-2 text-2xl font-semibold text-white">18</p>
                <p class="text-xs text-slate-500">Auto-generated</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Pending Review</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">4</p>
                <p class="text-xs text-slate-500">Awaiting supervisor</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Approved</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">12</p>
                <p class="text-xs text-slate-500">Locked ratings</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Missing Outputs</p>
                <p class="mt-2 text-2xl font-semibold text-rose-300">2</p>
                <p class="text-xs text-slate-500">Alerts sent</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Report List</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Employee</th>
                            <th class="px-4 py-2 text-left">Period</th>
                            <th class="px-4 py-2 text-left">Outputs</th>
                            <th class="px-4 py-2 text-left">Rating</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Juan Dela Cruz</td>
                            <td class="px-4 py-3">Q3 2025</td>
                            <td class="px-4 py-3">120</td>
                            <td class="px-4 py-3 text-emerald-300">4.7</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-400">Approved</span></td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="View report"
                                        data-action-message="Open the finalized IPCR report and audit trail."
                                        data-action-confirm="Open report"
                                        class="text-blue-400 hover:text-blue-300">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Maria Santos</td>
                            <td class="px-4 py-3">Q3 2025</td>
                            <td class="px-4 py-3">98</td>
                            <td class="px-4 py-3 text-sky-300">4.1</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-400">Pending</span></td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="Review report"
                                        data-action-message="Open the report draft to review auto-logged ratings and missing items."
                                        data-action-confirm="Open review"
                                        class="text-blue-400 hover:text-blue-300">
                                    Review
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Pedro Reyes</td>
                            <td class="px-4 py-3">Q3 2025</td>
                            <td class="px-4 py-3">82</td>
                            <td class="px-4 py-3 text-amber-300">3.6</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-rose-500/20 px-2 py-1 text-xs text-rose-300">Missing Output</span></td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="Notify employee"
                                        data-action-message="Send an automated reminder about missing outputs and incomplete fields."
                                        data-action-confirm="Send alert"
                                        class="text-blue-400 hover:text-blue-300">
                                    Notify
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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

