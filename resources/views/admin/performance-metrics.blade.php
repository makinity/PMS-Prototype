<x-layouts.admin>
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Performance Metrics</h1>
                <p class="text-sm text-slate-400">Configure scoring weights and alert thresholds.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        data-admin-action
                        data-action-title="Save metric set"
                        data-action-message="Save this configuration as the active scoring model."
                        data-action-confirm="Save metrics"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Metrics
                </button>
                <button type="button"
                        data-admin-action
                        data-action-title="Create template"
                        data-action-message="Create a reusable metrics template for other teams."
                        data-action-confirm="Create template"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    <i class="fa-solid fa-layer-group"></i>
                    New Template
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Active Profile</p>
                <p class="mt-2 text-2xl font-semibold text-white">Standard 2025</p>
                <p class="text-xs text-slate-500">Applies to all teams</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Timeliness Weight</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">40%</p>
                <p class="text-xs text-slate-500">Auto-logged time</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Quality Weight</p>
                <p class="mt-2 text-2xl font-semibold text-white">35%</p>
                <p class="text-xs text-slate-500">Validation results</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Alert Rules</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">6</p>
                <p class="text-xs text-slate-500">Active triggers</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Rating Formula</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-white">Timeliness</p>
                            <input type="number" value="40" class="w-20 rounded-md border border-slate-700 bg-slate-950 px-2 py-1 text-xs text-slate-200">
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-slate-800">
                            <div class="h-2 w-2/5 rounded-full bg-emerald-500"></div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Based on auto-logged start and end time.</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-white">Quality</p>
                            <input type="number" value="35" class="w-20 rounded-md border border-slate-700 bg-slate-950 px-2 py-1 text-xs text-slate-200">
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-slate-800">
                            <div class="h-2 w-1/3 rounded-full bg-blue-500"></div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Validation passes and rework rate.</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-white">Volume</p>
                            <input type="number" value="25" class="w-20 rounded-md border border-slate-700 bg-slate-950 px-2 py-1 text-xs text-slate-200">
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-slate-800">
                            <div class="h-2 w-1/4 rounded-full bg-amber-500"></div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Completed outputs vs assigned outputs.</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Metric Thresholds</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm text-slate-300">
                        <thead class="text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-2 text-left">Metric</th>
                                <th class="px-4 py-2 text-left">Target</th>
                                <th class="px-4 py-2 text-left">Warning</th>
                                <th class="px-4 py-2 text-left">Critical</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3 text-white">On-Time Rate</td>
                                <td class="px-4 py-3">95%</td>
                                <td class="px-4 py-3 text-amber-300">90%</td>
                                <td class="px-4 py-3 text-rose-300">85%</td>
                            </tr>
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3 text-white">Missing Outputs</td>
                                <td class="px-4 py-3">0-2</td>
                                <td class="px-4 py-3 text-amber-300">3-5</td>
                                <td class="px-4 py-3 text-rose-300">6+</td>
                            </tr>
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3 text-white">Quality Rework</td>
                                <td class="px-4 py-3">Below 5%</td>
                                <td class="px-4 py-3 text-amber-300">5-10%</td>
                                <td class="px-4 py-3 text-rose-300">Above 10%</td>
                            </tr>
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3 text-white">Avg Duration</td>
                                <td class="px-4 py-3">Baseline</td>
                                <td class="px-4 py-3 text-amber-300">+15%</td>
                                <td class="px-4 py-3 text-rose-300">+30%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Alert Rules</h2>
                <button type="button"
                        data-admin-action
                        data-action-title="Add alert rule"
                        data-action-message="Create a new rule for performance alerts."
                        data-action-confirm="Create rule"
                        class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-800">
                    Add rule
                </button>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3 text-sm text-slate-300">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="font-medium text-white">Incomplete Output</p>
                    <p class="text-xs text-slate-500">Alert after 4 hours idle</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="font-medium text-white">Baseline Overrun</p>
                    <p class="text-xs text-slate-500">Trigger at 120% baseline</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="font-medium text-white">Missing Client Link</p>
                    <p class="text-xs text-slate-500">Block rating until linked</p>
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
