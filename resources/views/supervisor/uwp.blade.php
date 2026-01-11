<x-layouts.supervisor>
    @php
        $status = $status ?? 'Draft';
        $isDraft = $status === 'Draft';
    @endphp

    <section class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Unit Work Plan</p>
                <h1 class="text-2xl font-bold text-white">Supervisor Unit Work Plan (UWP)</h1>
                <p class="text-sm text-slate-400">Plan the unit’s deliverables for the period. This sets commitments for OPCR/IPCR; no performance scoring occurs here.</p>
                <p class="text-xs text-slate-500">Outputs are planned deliverables. Actual ratings are calculated later from MPOR/IPCR.</p>
            </div>
            <div class="flex flex-col items-end gap-2 text-right">
                <span class="inline-flex items-center gap-2 rounded-full border border-blue-500/40 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                    Status: {{ $status }}
                </span>
                <p class="text-[11px] text-slate-500">Department Head approval is required before this plan is locked.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-white">Planning details</p>
                    <p class="text-xs text-slate-400">Define commitments for the period. Editing is allowed only while in Draft.</p>
                </div>
                <div class="flex items-center gap-2 text-[11px] text-slate-400">
                    <span class="inline-flex items-center gap-1 rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-1 font-semibold text-amber-200">Draft</span>
                    <span class="inline-flex items-center gap-1 rounded-full border border-blue-500/30 bg-blue-500/10 px-2.5 py-1 font-semibold text-blue-200">Submitted for Approval</span>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="space-y-1 text-sm text-slate-300">
                    <span class="text-xs uppercase tracking-wide text-slate-400">Office / Unit</span>
                    <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Administrative Services Unit" {{ $isDraft ? '' : 'disabled' }}>
                </label>
                <label class="space-y-1 text-sm text-slate-300">
                    <span class="text-xs uppercase tracking-wide text-slate-400">Performance Period</span>
                    <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Jan – Dec 2026" {{ $isDraft ? '' : 'disabled' }}>
                </label>
            </div>

            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-white">Core Functions (80%)</p>
                        <p class="text-xs text-slate-400">Each row is a measurable, loggable core output. No scoring here; capture targets only.</p>
                    </div>
                    <span class="text-[11px] text-slate-500">Actual ratings are calculated later from MPOR/IPCR.</span>
                </div>
                <div class="relative rounded-xl border border-slate-800 bg-slate-950/60">
                    <div class="{{ $isDraft ? '' : 'pointer-events-none opacity-60' }}">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-900/70 text-slate-300">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Major Output / Activity</th>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Planned Deliverable</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Target Level (5–1)</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Performance Standard Reference</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Timeline / Target</th>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 text-slate-100">
                                    <tr class="hover:bg-slate-900/50">
                                        <td class="px-4 py-3">
                                            <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Records management and archiving" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <textarea rows="2" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Describe the planned deliverable/output" {{ $isDraft ? '' : 'disabled' }}></textarea>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <select class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" {{ $isDraft ? '' : 'disabled' }}>
                                                <option>5 - Stretch target</option>
                                                <option>4 - Above target</option>
                                                <option>3 - Target</option>
                                                <option>2 - Needs support</option>
                                                <option>1 - Baseline</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <select class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" {{ $isDraft ? '' : 'disabled' }}>
                                                <option>Service standard</option>
                                                <option>Legal mandate</option>
                                                <option>Process SLA</option>
                                                <option>OPCR alignment</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Monthly; 1,200 files" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Context, dependencies, or scope" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-900/50">
                                        <td class="px-4 py-3">
                                            <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Client support and ticket resolution" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <textarea rows="2" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Describe the planned deliverable/output" {{ $isDraft ? '' : 'disabled' }}></textarea>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <select class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" {{ $isDraft ? '' : 'disabled' }}>
                                                <option>5 - Stretch target</option>
                                                <option>4 - Above target</option>
                                                <option>3 - Target</option>
                                                <option>2 - Needs support</option>
                                                <option>1 - Baseline</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <select class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" {{ $isDraft ? '' : 'disabled' }}>
                                                <option>Service standard</option>
                                                <option>Legal mandate</option>
                                                <option>Process SLA</option>
                                                <option>OPCR alignment</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Quarterly; 95% resolved" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Dependencies, service hours, tools" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @unless ($isDraft)
                        <div class="pointer-events-none absolute inset-0 rounded-xl border border-slate-700/60 bg-slate-950/50"></div>
                    @endunless
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-white">Support Functions (20%)</p>
                        <p class="text-xs text-slate-400">Log support outputs that enable the unit. Keep them measurable and planned.</p>
                    </div>
                    <span class="text-[11px] text-slate-500">No scoring fields here; only planned targets.</span>
                </div>
                <div class="relative rounded-xl border border-slate-800 bg-slate-950/60">
                    <div class="{{ $isDraft ? '' : 'pointer-events-none opacity-60' }}">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-900/70 text-slate-300">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Support Output</th>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Planned Deliverable</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Target Level (5–1)</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Performance Standard Reference</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Timeline / Target</th>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 text-slate-100">
                                    <tr class="hover:bg-slate-900/50">
                                        <td class="px-4 py-3">
                                            <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Staff training sessions" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <textarea rows="2" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Describe the planned deliverable/output" {{ $isDraft ? '' : 'disabled' }}></textarea>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <select class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" {{ $isDraft ? '' : 'disabled' }}>
                                                <option>5 - Stretch target</option>
                                                <option>4 - Above target</option>
                                                <option>3 - Target</option>
                                                <option>2 - Needs support</option>
                                                <option>1 - Baseline</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <select class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" {{ $isDraft ? '' : 'disabled' }}>
                                                <option>Service standard</option>
                                                <option>Legal mandate</option>
                                                <option>Process SLA</option>
                                                <option>OPCR alignment</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Quarterly; 4 sessions" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Stakeholders, coverage, notes" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @unless ($isDraft)
                        <div class="pointer-events-none absolute inset-0 rounded-xl border border-slate-700/60 bg-slate-950/50"></div>
                    @endunless
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-xs text-slate-400">Once submitted, this plan becomes read-only until reviewed.</p>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button"
                            data-employee-action
                            data-action-title="Save UWP Draft"
                            data-action-message="This will save the Unit Work Plan as a draft. You may continue editing until it is submitted for approval."
                            data-action-confirm="Save draft"
                            data-action-loading="Saving..."
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800/80 {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}" {{ $isDraft ? '' : 'disabled' }}>
                        <span data-button-label>Save as Draft</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                    </button>
                    <button type="button"
                            data-employee-loading="true"
                            data-loading-text="Submitting…"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-900/40 transition hover:bg-blue-500 {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}" {{ $isDraft ? '' : 'disabled' }}>
                        <span data-button-label>Submit for Approval</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                    <span class="text-[11px] text-slate-500">UWP remains editable only while in Draft.</span>
                </div>
            </div>
        </div>
    </section>

    <div id="employee-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="employee-action-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="employee-action-body" class="mt-1 text-sm text-slate-400">Prototype action preview.</p>
                </div>
                <button type="button" data-employee-modal-close class="text-slate-400 hover:text-white">x</button>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-employee-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
                <button type="button" id="employee-action-confirm" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <span data-button-label>Proceed</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('employee-action-modal');
                const title = document.getElementById('employee-action-title');
                const body = document.getElementById('employee-action-body');
                const confirmBtn = document.getElementById('employee-action-confirm');
                let activeTrigger = null;

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
                    activeTrigger = null;
                    setButtonLoading(confirmBtn, false);
                }

                function openModal(trigger) {
                    activeTrigger = trigger;
                    title.textContent = trigger.dataset.actionTitle || 'Action';
                    body.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
                    confirmBtn.dataset.actionLoading = trigger.dataset.actionLoading || 'Working...';
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }

                window.openEmployeeActionModal = openModal;

                document.querySelectorAll('[data-employee-action]').forEach((button) => {
                    if (button.dataset.actionRequiresValidation === 'true') {
                        return;
                    }
                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        openModal(button);
                    });
                });

                confirmBtn.addEventListener('click', function () {
                    setButtonLoading(confirmBtn, true, confirmBtn.dataset.actionLoading);
                    if (activeTrigger) {
                        setButtonLoading(activeTrigger, true, activeTrigger.dataset.actionLoading || confirmBtn.dataset.actionLoading);
                    }

                    setTimeout(() => {
                        setButtonLoading(confirmBtn, false);
                        if (activeTrigger) {
                            setButtonLoading(activeTrigger, false);
                        }
                        closeModal();
                    }, 1200);
                });

                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                modal.querySelectorAll('[data-employee-modal-close]').forEach((button) => {
                    button.addEventListener('click', closeModal);
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeModal();
                    }
                });

                document.querySelectorAll('[data-employee-loading="true"]').forEach((button) => {
                    button.addEventListener('click', function () {
                        if (button.dataset.loadingActive === 'true') {
                            return;
                        }
                        button.dataset.loadingActive = 'true';
                        setButtonLoading(button, true, button.dataset.loadingText || 'Loading...');

                        const duration = Number.parseInt(button.dataset.loadingDuration || '1200', 10);
                        if (!Number.isNaN(duration)) {
                            setTimeout(() => {
                                setButtonLoading(button, false);
                                button.dataset.loadingActive = 'false';
                            }, duration);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layouts.supervisor>
