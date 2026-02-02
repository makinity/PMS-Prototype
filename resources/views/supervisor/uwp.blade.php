@extends('layouts.supervisor')
    @php
        $status = $status ?? 'Draft';
        $isDraft = $status === 'Draft';
    @endphp
@section('main-content')
    <section class="space-y-6">
        <div>
            <a href="{{ route('supervisor.uwp-page') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-300">
                ← Back to Unit Work Plans
            </a>
        </div>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Unit Work Plan</p>
                <h1 class="text-2xl font-bold text-white">Unit Work Plan (UWP) – Planning & Commitment</h1>
                <p class="text-sm text-slate-400">Plan the unit's deliverables for the period. This is the commitment basis for OPCR/IPCR; no performance scoring occurs here.</p>
                <p class="text-xs text-slate-500">One output may have multiple success indicators (measurement criteria). Actual ratings are system-generated later from MPOR/IPCR.</p>
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

                    <select
                        class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2
                            text-sm text-slate-100 focus:border-blue-500
                            focus:ring-2 focus:ring-blue-500/40 focus:outline-none
                            {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}"
                        style="background:#0f172a;color:#e5e7eb;"
                        {{ $isDraft ? '' : 'disabled' }}
                    >
                        <option selected>Revenue Collection Unit</option>
                        <option>Records Management Unit</option>
                        <option>Administrative Services Unit</option>
                        <option>Human Resource Management Unit</option>
                        <option>General Services Unit</option>
                        <option>Planning and Development Unit</option>
                    </select>
                </label>

                <label class="space-y-1 text-sm text-slate-300">
                    <span class="text-xs uppercase tracking-wide text-slate-400">Performance Period</span>

                    <select
                        class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none
                               {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}"
                        style="background:#0f172a;color:#e5e7eb;"
                        {{ $isDraft ? '' : 'disabled' }}
                    >
                        <option value="January - June 2026" selected>January – June 2026</option>
                        <option value="July - December 2026">July – December 2026</option>
                    </select>
                </label>
            </div>

            {{-- CORE FUNCTIONS --}}
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-white">Core Functions (80%)</p>
                        <p class="text-xs text-slate-400">Each row is a measurable, loggable core output. No scoring here; capture targets only.</p>
                    </div>
                    <span class="text-[11px] text-slate-500">Actual ratings are calculated later from MPOR/IPCR.</span>
                </div>

                <div class="relative rounded-xl border border-slate-800 bg-slate-950/60">
                    <div class="{{ $isDraft ? '' : 'opacity-60' }}">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-900/70 text-slate-300">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">PPA / MFO</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Success Indicators</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Target / Timeline</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 text-slate-100">
                                    <tr class="hover:bg-slate-900/50">
                                        <td class="px-4 py-3">
                                            <input type="text"
                                                value="E-Bank Scanning and Encoding of Revenue Transactions"
                                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                                                style="background:#0f172a;color:#e5e7eb;"
                                                placeholder="e.g., Records management and archiving"
                                                {{ $isDraft ? '' : 'disabled' }}>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 text-blue-400 hover:text-blue-300 transition justify-center"
                                                data-uwp-indicators
                                                data-title="E-Bank Scanning and Encoding of Revenue Transactions"
                                                data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'
                                                aria-label="View success indicators"
                                            >
                                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 14">
                                                    <g stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                                                        <path d="M10 3c-3.5 0-6.5 2.3-8 5 1.5 2.7 4.5 5 8 5s6.5-2.3 8-5c-1.5-2.7-4.5-5-8-5Z"/>
                                                        <path d="M10 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                    </g>
                                                </svg>
                                                <span class="text-xs">View (3)</span>
                                            </button>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <input type="text"
                                                value="Daily; all e-bank transactions processed within the same working day"
                                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                                                style="background:#0f172a;color:#e5e7eb;"
                                                placeholder="e.g., Monthly; 1,200 files"
                                                {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                    </tr>

                                    <tr class="hover:bg-slate-900/50">
                                        <td class="px-4 py-3">
                                            <input type="text"
                                                value="Processing of Over-the-Counter Revenue Transactions"
                                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                                                style="background:#0f172a;color:#e5e7eb;"
                                                placeholder="e.g., Records management and archiving"
                                                {{ $isDraft ? '' : 'disabled' }}>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 text-blue-400 hover:text-blue-300 transition justify-center"
                                                data-uwp-indicators
                                                data-title="Processing of Over-the-Counter Revenue Transactions"
                                                data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'
                                                aria-label="View success indicators"
                                            >
                                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 14">
                                                    <g stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                                                        <path d="M10 3c-3.5 0-6.5 2.3-8 5 1.5 2.7 4.5 5 8 5s6.5-2.3 8-5c-1.5-2.7-4.5-5-8-5Z"/>
                                                        <path d="M10 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                    </g>
                                                </svg>

                                                <span class="text-xs">View (3)</span>
                                            </button>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <input type="text"
                                                value="Daily; 95% processed within the same working day"
                                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                                                style="background:#0f172a;color:#e5e7eb;"
                                                placeholder="e.g., Monthly; 1,200 files"
                                                {{ $isDraft ? '' : 'disabled' }}>
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

            {{-- SUPPORT FUNCTIONS --}}
            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-white">Support Functions (20%)</p>
                        <p class="text-xs text-slate-400">Log support outputs that enable the unit. Keep them measurable and planned.</p>
                    </div>
                    <span class="text-[11px] text-slate-500">No scoring fields here; only planned targets.</span>
                </div>

                <div class="relative rounded-xl border border-slate-800 bg-slate-950/60">
                    <div class="{{ $isDraft ? '' : 'opacity-60' }}">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-900/70 text-slate-300">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">PPA / MFO</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Success Indicators</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Target / Timeline</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 text-slate-100">
                                    <tr class="hover:bg-slate-900/50">
                                        <td class="px-4 py-3">
                                            <input type="text"
                                                value="Maintenance of revenue records and filing system"
                                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                                                style="background:#0f172a;color:#e5e7eb;"
                                                placeholder="e.g., Staff training sessions"
                                                {{ $isDraft ? '' : 'disabled' }}>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 text-blue-400 hover:text-blue-300 transition justify-center"
                                                data-uwp-indicators
                                                data-title="Maintenance of revenue records and filing system"
                                                data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'
                                                aria-label="View success indicators"
                                            >
                                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 14">
                                                    <g stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                                                        <path d="M10 3c-3.5 0-6.5 2.3-8 5 1.5 2.7 4.5 5 8 5s6.5-2.3 8-5c-1.5-2.7-4.5-5-8-5Z"/>
                                                        <path d="M10 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                    </g>
                                                </svg>

                                                <span class="text-xs">View (3)</span>
                                            </button>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <input value="Quarterly; records validated and properly filed"
                                                type="text"
                                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                                                style="background:#0f172a;color:#e5e7eb;"
                                                placeholder="e.g., Quarterly; 4 sessions"
                                                {{ $isDraft ? '' : 'disabled' }}>
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
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800/80 {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}"
                            {{ $isDraft ? '' : 'disabled' }}>
                        <span data-button-label>Save as Draft</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                    </button>

                    <button type="button"
                            data-employee-loading="true"
                            data-loading-text="Submitting..."
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-900/40 transition hover:bg-blue-500 {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}"
                            {{ $isDraft ? '' : 'disabled' }}>
                        <span data-button-label>Submit for Approval</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>

                    <span class="text-[11px] text-slate-500">UWP remains editable only while in Draft.</span>
                </div>
            </div>
        </div>
    </section>

    {{-- SUCCESS INDICATORS MODAL (now includes Assigned Employees per indicator) --}}
    <div id="uwp-indicators-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="uwp-indicators-title" class="text-lg font-semibold text-white">--</h3>
                    <p class="text-xs text-slate-400 mt-1">One output may have multiple success indicators. Each indicator can be assigned to a specific employee.</p>
                </div>
                <button type="button" onclick="closeUwpIndicatorsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-3">
                @if ($isDraft)
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-xs text-slate-400">Manage success indicators (one per line, scalable list).</span>
                        <button type="button" id="uwp-add-indicator"
                                class="inline-flex items-center gap-1 rounded-lg border border-blue-500/50 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200 hover:bg-blue-500/20">
                            <span class="fa-solid fa-plus text-[10px]"></span>
                            <span>Add Indicator</span>
                        </button>
                    </div>
                @endif

                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                    <div class="max-h-[340px] overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-900/70 text-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[11px] uppercase tracking-[0.2em] text-slate-400">Success Indicator</th>
                                    <th class="px-4 py-3 text-center text-[11px] uppercase tracking-[0.2em] text-slate-400">Standards</th>
                                    <th class="px-4 py-3 text-center text-[11px] uppercase tracking-[0.2em] text-slate-400">Assign Employee</th>
                                    @if ($isDraft)
                                        <th class="px-4 py-3 text-center text-[11px] uppercase tracking-[0.2em] text-slate-400">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="uwp-indicators-list" class="divide-y divide-slate-800"></tbody>
                        </table>
                    </div>
                </div>

                @unless ($isDraft)
                    <p class="text-[11px] text-slate-500">Read-only view. Indicators were finalized at submission.</p>
                @endunless
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closeUwpIndicatorsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- STANDARDS SUB-MODAL --}}
    <div id="uwp-standards-modal" class="fixed inset-0 z-[86] hidden items-center justify-center bg-black/60 px-4 py-8">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards</p>
                    <h3 class="text-lg font-semibold text-white">Target Difficulty / Standards</h3>
                    <p id="uwp-standards-indicator" class="text-[11px] text-slate-400 mt-1"></p>
                </div>
                <button type="button" onclick="closeStandardsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-4 text-sm text-slate-200 max-h-[70vh] overflow-y-auto">
                <div id="uwp-standards-list" class="w-full"></div>

                @if ($isDraft)
                    <div class="space-y-3 rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs text-slate-400">Add a standard to a specific Rating × Dimension cell.</p>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-4">
                            <select id="uwp-standard-rating"
                                    style="background:#0f172a;color:#e5e7eb;"
                                    class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                                <option value="5" selected>Rating: 5</option>
                                <option value="4">Rating: 4</option>
                                <option value="3">Rating: 3</option>
                                <option value="2">Rating: 2</option>
                                <option value="1">Rating: 1</option>
                            </select>

                            <select id="uwp-standard-dimension"
                                    style="background:#0f172a;color:#e5e7eb;"
                                    class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                                <option value="q" selected>Dimension: Q (Quality)</option>
                                <option value="e">Dimension: E (Efficiency)</option>
                                <option value="t">Dimension: T (Timeliness)</option>
                            </select>

                            <div class="sm:col-span-2">
                                <textarea id="uwp-standards-input"
                                          style="background:#0f172a;color:#e5e7eb;"
                                          rows="2"
                                          placeholder="Enter standard text"
                                          class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"></textarea>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button type="button"
                                    id="uwp-add-standard"
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                                Save to Table
                            </button>
                            <button type="button"
                                    id="uwp-reset-standard"
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                Reset to Seeded Dummy
                            </button>
                        </div>
                    </div>
                @else
                    <p class="text-[11px] text-slate-500">Standards are read-only in this stage.</p>
                @endif
            </div>

            <div class="mt-5 flex justify-end border-t border-slate-800 pt-3">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closeStandardsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- ASSIGN EMPLOYEE SUB-MODAL (scoped to a specific success indicator) --}}
    <div id="uwp-assigned-employees-modal" class="fixed inset-0 z-[85] hidden items-center justify-center bg-black/60 px-4 py-8">
        <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assign Employee</p>
                    <h3 class="text-lg font-semibold text-white">Employees under the selected Office/Unit</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Office / Unit: <span id="uwp-assigned-unit">--</span></p>
                    <p class="text-[11px] text-slate-400 mt-1">Success Indicator: <span id="uwp-assigned-indicator" class="font-semibold text-slate-200">--</span></p>
                </div>
                <button type="button" onclick="closeAssignedModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-3 text-sm text-slate-200 max-h-[60vh] overflow-y-auto">
                <div>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-500 text-xs">🔍</span>
                        <input type="text"
                               style="background:#0f172a;color:#e5e7eb;"
                               placeholder="Search employee name…"
                               class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 pl-8 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                    </div>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950/60 overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-900/70 text-slate-200">
                            <tr>
                                <th class="px-4 py-2 text-left">Employee Name</th>
                                <th class="px-4 py-2 text-left">Office / Unit</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Success Indicator</th>
                                @if ($isDraft)
                                    <th class="px-4 py-2 text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="uwp-assigned-list" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>

                <p id="uwp-assigned-empty" class="text-[12px] text-slate-500 hidden">No employees available (demo).</p>
            </div>

            <div class="mt-4 flex items-center justify-end gap-3 border-t border-slate-800 pt-3">
                @if ($isDraft)
                    <button type="button"
                            id="uwp-save-assignments"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                        <span data-button-label>Save Assignment</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                @endif

                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closeAssignedModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- GENERIC ACTION MODAL (unchanged) --}}
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
                // ===== Generic Action Modal (existing) =====
                const modal = document.getElementById('employee-action-modal');
                const title = document.getElementById('employee-action-title');
                const body = document.getElementById('employee-action-body');
                const confirmBtn = document.getElementById('employee-action-confirm');
                let activeTrigger = null;

                if (!modal || !title || !body || !confirmBtn) {
                    return;
                }

                function setButtonLoading(button, isLoading, loadingText) {
                    if (!button) return;
                    const label = button.querySelector('[data-button-label]');
                    const spinner = button.querySelector('[data-button-spinner]');
                    if (label && !button.dataset.originalLabel) {
                        button.dataset.originalLabel = label.textContent.trim();
                    }

                    if (isLoading) {
                        button.disabled = true;
                        button.classList.add('opacity-70', 'cursor-wait');
                        if (spinner) spinner.classList.remove('hidden');
                        if (label && loadingText) label.textContent = loadingText;
                    } else {
                        button.disabled = false;
                        button.classList.remove('opacity-70', 'cursor-wait');
                        if (spinner) spinner.classList.add('hidden');
                        if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
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
                    if (button.dataset.actionRequiresValidation === 'true') return;
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
                        if (activeTrigger) setButtonLoading(activeTrigger, false);
                        closeModal();
                    }, 1200);
                });

                modal.addEventListener('click', function (event) {
                    if (event.target === modal) closeModal();
                });

                modal.querySelectorAll('[data-employee-modal-close]').forEach((button) => {
                    button.addEventListener('click', closeModal);
                });

                // ===== UWP Modals =====
                const indicatorsModal = document.getElementById('uwp-indicators-modal');
                const indicatorsTitle = document.getElementById('uwp-indicators-title');
                const indicatorsList = document.getElementById('uwp-indicators-list');
                const addIndicatorBtn = document.getElementById('uwp-add-indicator');

                const standardsModal = document.getElementById('uwp-standards-modal');
                const standardsList = document.getElementById('uwp-standards-list');
                const standardsIndicatorLabel = document.getElementById('uwp-standards-indicator');
                const standardsInput = document.getElementById('uwp-standards-input');
                const addStandardBtn = document.getElementById('uwp-add-standard');

                const assignedModal = document.getElementById('uwp-assigned-employees-modal');
                const assignedList = document.getElementById('uwp-assigned-list');
                const assignedEmpty = document.getElementById('uwp-assigned-empty');
                const assignedUnit = document.getElementById('uwp-assigned-unit');
                const assignedIndicator = document.getElementById('uwp-assigned-indicator');
                const saveAssignmentsBtn = document.getElementById('uwp-save-assignments');

                const unitSelect = document.querySelector('select[aria-label="Office / Unit"]') || document.querySelector('select');

                let activeIndicators = [];
                let standardsData = [];

                // NEW: assignment per success indicator
                let indicatorAssignments = {}; // { [indicatorText]: string[] }
                let activeAssignIndicatorIndex = null;

                const assignedData = {
                    'Revenue Collection Unit': [
                        { name: 'Ramon Reyes', unit: 'Revenue Collection Unit' },
                    ],
                    'Records Management Unit': [],
                    'Administrative Services Unit': [],
                    'Human Resource Management Unit': [],
                    'General Services Unit': [],
                    'Planning and Development Unit': [],
                };

                const isDraft = {{ $isDraft ? 'true' : 'false' }};

                const standardsSeedMap = {
                    'All e-bank transactions scanned and encoded daily': {
                        5: { q: ['No errors; accurate encoding'], e: ['100% processed'], t: ['Same working day'] },
                        4: { q: ['Minor errors'], e: ['100% processed'], t: ['Same working day'] },
                        3: { q: ['Few minor errors'], e: ['95–99% processed'], t: ['End of working day'] },
                        2: { q: ['Multiple errors'], e: ['<95% processed'], t: ['Beyond working day'] },
                        1: { q: ['Major errors/missing'], e: ['Majority unprocessed'], t: ['Not within acceptable time'] },
                    },
                    'Indexing complete with no missing pages': {
                        5: { q: ['Indexing fully verified, zero gaps'], e: ['100% pages indexed'], t: ['Same day'] },
                        4: { q: ['Indexing minor rechecks'], e: ['100% pages indexed'], t: ['Same day'] },
                        3: { q: ['Occasional missing indexes fixed'], e: ['95–99% indexed'], t: ['Within 24 hours'] },
                        2: { q: ['Frequent missing pages'], e: ['<95% indexed'], t: ['Beyond 24 hours'] },
                        1: { q: ['Indexing largely incomplete'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Audit trail maintained within 24 hours': {
                        5: { q: ['Complete trail, no errors'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                        4: { q: ['Minor corrections only'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                        3: { q: ['Some gaps corrected'], e: ['95–99% entries captured'], t: ['Within 48 hours'] },
                        2: { q: ['Multiple missing logs'], e: ['<95% captured'], t: ['Beyond 48 hours'] },
                        1: { q: ['Trail missing'], e: ['Majority uncaptured'], t: ['Unacceptable'] },
                    },
                    'Same-day verification of OTC transactions': {
                        5: { q: ['Verified without discrepancies'], e: ['100% OTC verified'], t: ['Same working day'] },
                        4: { q: ['Minor verifications pending'], e: ['100% OTC verified'], t: ['Same working day'] },
                        3: { q: ['Few pending verifications'], e: ['95–99% verified'], t: ['End of working day'] },
                        2: { q: ['Several unverified'], e: ['<95% verified'], t: ['Beyond working day'] },
                        1: { q: ['Verification not done'], e: ['Majority unverified'], t: ['Unacceptable'] },
                    },
                    '95% encoded within the business day': {
                        5: { q: ['Encodings error-free'], e: ['100% encoded'], t: ['Same business day'] },
                        4: { q: ['Minor corrections'], e: ['100% encoded'], t: ['Same business day'] },
                        3: { q: ['Few delays'], e: ['95–99% encoded'], t: ['By end of day'] },
                        2: { q: ['Multiple delays'], e: ['<95% encoded'], t: ['Next day'] },
                        1: { q: ['Encoding largely incomplete'], e: ['Major backlog'], t: ['Unacceptable'] },
                    },
                    'OR validation completed daily': {
                        5: { q: ['All ORs validated error-free'], e: ['100% validated'], t: ['Daily'] },
                        4: { q: ['Minor issues corrected same day'], e: ['100% validated'], t: ['Daily'] },
                        3: { q: ['Some validations late'], e: ['95–99% validated'], t: ['Within 48 hours'] },
                        2: { q: ['Frequent late validations'], e: ['<95% validated'], t: ['Beyond 48 hours'] },
                        1: { q: ['Validations mostly missing'], e: ['Majority unvalidated'], t: ['Unacceptable'] },
                    },
                    'Weekly filing updated and retrievable': {
                        5: { q: ['Zero retrieval issues'], e: ['100% weekly updates'], t: ['Within week'] },
                        4: { q: ['Minor retrieval fixes'], e: ['100% weekly updates'], t: ['Within week'] },
                        3: { q: ['Some items late'], e: ['95–99% updates'], t: ['Within next week'] },
                        2: { q: ['Many late updates'], e: ['<95% updates'], t: ['Beyond next week'] },
                        1: { q: ['Updates not done'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Digital backups synced monthly': {
                        5: { q: ['Backups verified'], e: ['100% synced'], t: ['Within month'] },
                        4: { q: ['Minor sync corrections'], e: ['100% synced'], t: ['Within month'] },
                        3: { q: ['Some delays'], e: ['95–99% synced'], t: ['Within following week'] },
                        2: { q: ['Frequent delays'], e: ['<95% synced'], t: ['Beyond following week'] },
                        1: { q: ['Backups largely missing'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Retrieval logs maintained for audits': {
                        5: { q: ['Logs complete and audit-ready'], e: ['100% requests logged'], t: ['Same day'] },
                        4: { q: ['Minor log gaps corrected'], e: ['100% requests logged'], t: ['Same day'] },
                        3: { q: ['Some gaps'], e: ['95–99% logged'], t: ['Within 48 hours'] },
                        2: { q: ['Many gaps'], e: ['<95% logged'], t: ['Beyond 48 hours'] },
                        1: { q: ['Logs largely missing'], e: ['Majority unlogged'], t: ['Unacceptable'] },
                    },
                };

                function createEmptyStandards() {
                    return {
                        5: { q: [], e: [], t: [] },
                        4: { q: [], e: [], t: [] },
                        3: { q: [], e: [], t: [] },
                        2: { q: [], e: [], t: [] },
                        1: { q: [], e: [], t: [] },
                    };
                }

                function seedStandardsForIndicator(text) {
                    const seed = standardsSeedMap[text];
                    if (!seed) return createEmptyStandards();
                    const base = createEmptyStandards();
                    [5,4,3,2,1].forEach((lvl) => {
                        if (seed[lvl]) {
                            base[lvl] = {
                                q: Array.isArray(seed[lvl].q) ? [...seed[lvl].q] : [seed[lvl].q],
                                e: Array.isArray(seed[lvl].e) ? [...seed[lvl].e] : [seed[lvl].e],
                                t: Array.isArray(seed[lvl].t) ? [...seed[lvl].t] : [seed[lvl].t],
                            };
                        }
                    });
                    return base;
                }

                // Seed assignments (demo): first indicator gets Ramon Reyes if present
                function seedAssignmentsForIndicators(list) {
                    const next = {};
                    list.forEach((text) => {
                        const key = (text || '').trim();
                        if (!key) return;
                        const existing = indicatorAssignments[key];
                        next[key] = Array.isArray(existing) ? [...existing] : [];
                    });
                    indicatorAssignments = next;
                }

                function getAssignedEmployees(indicatorText) {
                    const key = (indicatorText || '').trim();
                    if (!key) return [];
                    return Array.isArray(indicatorAssignments[key]) ? [...indicatorAssignments[key]] : [];
                }

                function isEmployeeAssignedToIndicator(indicatorText, employeeName) {
                    if (!employeeName) return false;
                    const list = getAssignedEmployees(indicatorText);
                    return list.includes(employeeName);
                }

                function assignEmployeeToIndicator(indicatorText, employeeName) {
                    const key = (indicatorText || '').trim();
                    if (!key || !employeeName) return;
                    indicatorAssignments[key] = Array.isArray(indicatorAssignments[key]) ? indicatorAssignments[key] : [];
                    if (!indicatorAssignments[key].includes(employeeName)) {
                        indicatorAssignments[key].push(employeeName);
                    }
                }

                function unassignEmployeeFromIndicator(indicatorText, employeeName) {
                    const key = (indicatorText || '').trim();
                    if (!key || !employeeName) return;
                    indicatorAssignments[key] = Array.isArray(indicatorAssignments[key]) ? indicatorAssignments[key] : [];
                    const index = indicatorAssignments[key].indexOf(employeeName);
                    if (index !== -1) {
                        indicatorAssignments[key].splice(index, 1);
                    }
                }

                function renderIndicators(list) {
                    if (!indicatorsList) return;
                    indicatorsList.innerHTML = '';

                    list.forEach((item, idx) => {
                        const value = (item || '').trim();
                        if (!value) return;

                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/40';

                        // Indicator text
                        const indicatorTd = document.createElement('td');
                        indicatorTd.className = 'px-4 py-3 align-top';
                        const indicatorWrap = document.createElement('div');
                        indicatorWrap.className = 'space-y-1';

                        const textSpan = document.createElement('div');
                        textSpan.className = 'text-slate-100';
                        textSpan.textContent = value;

                        const hint = document.createElement('div');
                        hint.className = 'text-[11px] text-slate-500';
                        hint.textContent = 'Assigned per indicator (task-level).';

                        indicatorWrap.append(textSpan);
                        indicatorWrap.append(hint);
                        indicatorTd.appendChild(indicatorWrap);

                        // Standards column
                        const standardsTd = document.createElement('td');
                        standardsTd.className = 'px-4 py-3 text-center align-top';

                        const standardBtn = document.createElement('button');
                        standardBtn.type = 'button';
                        standardBtn.className = 'inline-flex items-center justify-center rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[120px]';
                        standardBtn.innerHTML = `
                            <span class="inline-flex items-center gap-2">
                                <span class="fa-regular fa-eye text-[12px]"></span>
                                <span>Standards</span>
                            </span>
                        `;
                        standardBtn.addEventListener('click', () => openStandardsModal(idx));
                        standardsTd.appendChild(standardBtn);

                        // Assigned employee column
                        const assignedTd = document.createElement('td');
                        assignedTd.className = 'px-4 py-3 text-center align-top';

                        const assignedCount = getAssignedEmployees(value).length;
                        const assignBtn = document.createElement('button');
                        assignBtn.type = 'button';
                        assignBtn.className = 'inline-flex items-center gap-2 text-xs font-semibold text-blue-400 transition-colors hover:text-blue-300 focus:outline-none';
                        assignBtn.style.cursor = 'pointer';
                        assignBtn.innerHTML = `
                            <span class="fa-regular fa-user text-[12px]"></span>
                            <span>+ Assign</span>
                            <span class="text-[11px] text-slate-400">( ${assignedCount} )</span>
                        `;
                        assignBtn.addEventListener('click', () => openAssignedModalForIndicator(idx));
                        assignedTd.appendChild(assignBtn);

                        tr.appendChild(indicatorTd);
                        tr.appendChild(standardsTd);
                        tr.appendChild(assignedTd);

                        // Draft actions (Edit/Delete only — assignment is handled by Assign Employee button)
                        if (isDraft) {
                            const actionsTd = document.createElement('td');
                            actionsTd.className = 'px-4 py-3 text-center align-top';

                            const actionsWrap = document.createElement('div');
                            actionsWrap.className = 'inline-flex items-center gap-3 text-[11px] text-blue-200';

                            const editBtn = document.createElement('button');
                            editBtn.type = 'button';
                            editBtn.textContent = 'Edit';
                            editBtn.className = 'hover:text-blue-100 underline';
                            editBtn.addEventListener('click', () => startEditIndicator(idx, value));

                            const delBtn = document.createElement('button');
                            delBtn.type = 'button';
                            delBtn.textContent = 'Delete';
                            delBtn.className = 'hover:text-blue-100 underline';
                            delBtn.addEventListener('click', () => deleteIndicator(idx));

                            actionsWrap.appendChild(editBtn);
                            actionsWrap.appendChild(delBtn);
                            actionsTd.appendChild(actionsWrap);
                            tr.appendChild(actionsTd);
                        }

                        indicatorsList.appendChild(tr);
                    });
                }

                // ===== Assigned Modal (scoped to active indicator) =====
                function renderAssigned(unit) {
                    if (!assignedList || !assignedEmpty || !assignedUnit || !assignedIndicator) return;

                    const indicatorText = activeIndicators[activeAssignIndicatorIndex] || '';
                    assignedUnit.textContent = unit || '—';
                    assignedIndicator.textContent = indicatorText || '—';

                    assignedList.innerHTML = '';
                    const rows = assignedData[unit] || [];
                    if (!rows.length) {
                        assignedEmpty.classList.remove('hidden');
                        return;
                    }
                    assignedEmpty.classList.add('hidden');

                    rows.forEach((emp) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/40';

                        const nameTd = document.createElement('td');
                        nameTd.className = 'px-4 py-2';
                        nameTd.textContent = emp.name;

                        const unitTd = document.createElement('td');
                        unitTd.className = 'px-4 py-2';
                        unitTd.textContent = emp.unit;

                        const statusTd = document.createElement('td');
                        statusTd.className = 'px-4 py-2';

                        const isAssigned = isEmployeeAssignedToIndicator(indicatorText, emp.name);
                        const badge = document.createElement('span');
                        badge.className = `inline-flex items-center px-2 py-1 text-[11px] font-semibold rounded-full border ${
                            isAssigned ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200' : 'border-slate-600 bg-slate-800 text-slate-300'
                        }`;
                        badge.textContent = isAssigned ? 'Assigned' : 'Available';
                        statusTd.appendChild(badge);

                        const indicatorTd = document.createElement('td');
                        indicatorTd.className = 'px-4 py-2 text-slate-300';
                        indicatorTd.textContent = indicatorText || '—';

                        tr.appendChild(nameTd);
                        tr.appendChild(unitTd);
                        tr.appendChild(statusTd);
                        tr.appendChild(indicatorTd);

                        if (isDraft) {
                            const actionTd = document.createElement('td');
                            actionTd.className = 'px-4 py-2 text-center';

                            const toggle = document.createElement('button');
                            toggle.type = 'button';
                            toggle.className = 'text-blue-300 hover:text-blue-200 text-xs underline';
                            toggle.textContent = isAssigned ? 'Unassign' : 'Assign';

                            toggle.addEventListener('click', () => {
                                const currentIndicator = activeIndicators[activeAssignIndicatorIndex] || '';
                                if (!currentIndicator) return;
                                const alreadyAssigned = isEmployeeAssignedToIndicator(currentIndicator, emp.name);
                                if (alreadyAssigned) {
                                    unassignEmployeeFromIndicator(currentIndicator, emp.name);
                                } else {
                                    assignEmployeeToIndicator(currentIndicator, emp.name);
                                }

                                // refresh both modals
                                renderAssigned(unit);
                                renderIndicators(activeIndicators);
                            });

                            actionTd.appendChild(toggle);
                            tr.appendChild(actionTd);
                        }

                        assignedList.appendChild(tr);
                    });
                }

                function openAssignedModalForIndicator(indicatorIdx) {
                    if (!assignedModal) return;
                    activeAssignIndicatorIndex = indicatorIdx;

                    const unit = (unitSelect && unitSelect.value) || 'Revenue Collection Unit';
                    renderAssigned(unit);

                    assignedModal.classList.remove('hidden');
                    assignedModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function closeAssignedModal() {
                    if (assignedModal) {
                        assignedModal.classList.add('hidden');
                        assignedModal.classList.remove('flex');
                    }
                    document.body.classList.remove('overflow-hidden');
                }

                // ===== Standards Modal (existing, unchanged behavior) =====
                function openStandardsModal(idx) {
                    if (!standardsModal || !standardsList) return;
                    const data = standardsData[idx] || seedStandardsForIndicator(activeIndicators[idx] || '');
                    standardsData[idx] = data;

                    standardsList.innerHTML = '';
                    if (standardsIndicatorLabel) standardsIndicatorLabel.textContent = activeIndicators[idx] || '';

                    const table = document.createElement('table');
                    table.className = 'w-full text-sm border border-slate-800';
                    table.innerHTML = `
                        <thead class="bg-slate-900/70 text-slate-200">
                            <tr>
                                <th class="px-3 py-2 text-left">Rating</th>
                                <th class="px-3 py-2 text-left">Quality (Q)</th>
                                <th class="px-3 py-2 text-left">Efficiency (E)</th>
                                <th class="px-3 py-2 text-left">Timeliness (T)</th>
                            </tr>
                        </thead>
                    `;

                    const tbody = document.createElement('tbody');
                    tbody.className = 'divide-y divide-slate-800 text-slate-100';

                    [5,4,3,2,1].forEach((lvl) => {
                        const row = data[lvl] || { q:[], e:[], t:[] };
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/40';

                        const ratingTd = document.createElement('td');
                        ratingTd.className = 'px-3 py-2 text-left';
                        ratingTd.textContent = lvl;

                        const makeCell = (arr, dim) => {
                            const td = document.createElement('td');
                            td.className = 'px-3 py-2 text-left align-top';

                            if (!arr || arr.length === 0) {
                                td.textContent = '—';
                            } else {
                                const list = document.createElement('div');
                                list.className = 'space-y-1';
                                arr.forEach((txt, itemIdx) => {
                                    const line = document.createElement('div');
                                    line.className = 'flex items-start gap-2';
                                    const bullet = document.createElement('span');
                                    bullet.textContent = '•';
                                    bullet.className = 'text-slate-400';
                                    const text = document.createElement('span');
                                    text.className = 'flex-1';
                                    text.textContent = txt;
                                    line.append(bullet, text);

                                    if (isDraft) {
                                        const del = document.createElement('button');
                                        del.type = 'button';
                                        del.className = 'text-rose-400 hover:text-rose-300 text-[11px]';
                                        del.textContent = '×';
                                        del.addEventListener('click', () => removeStandard(idx, lvl, dim, itemIdx));
                                        line.appendChild(del);
                                    }
                                    list.appendChild(line);
                                });
                                td.appendChild(list);
                            }

                            if (isDraft) {
                                const addLink = document.createElement('button');
                                addLink.type = 'button';
                                addLink.className = 'mt-1 text-[11px]';
                                addLink.style.color = '#60a5fa';
                                addLink.textContent = 'Add +';
                                addLink.addEventListener('click', () => {
                                    const ratingSelect = document.getElementById('uwp-standard-rating');
                                    const dimSelect = document.getElementById('uwp-standard-dimension');
                                    if (ratingSelect && dimSelect && standardsInput) {
                                        ratingSelect.value = String(lvl);
                                        dimSelect.value = dim;
                                        standardsInput.focus();
                                    }
                                });
                                td.appendChild(addLink);
                            }

                            return td;
                        };

                        tr.append(
                            ratingTd,
                            makeCell(row.q, 'q'),
                            makeCell(row.e, 'e'),
                            makeCell(row.t, 't')
                        );
                        tbody.appendChild(tr);
                    });

                    table.appendChild(tbody);
                    standardsList.appendChild(table);

                    standardsModal.dataset.currentIndex = idx;
                    standardsModal.classList.remove('hidden');
                    standardsModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function handleAddStandard() {
                    if (!standardsModal || !standardsInput) return;
                    const idx = Number(standardsModal.dataset.currentIndex);
                    const ratingSelect = document.getElementById('uwp-standard-rating');
                    const dimensionSelect = document.getElementById('uwp-standard-dimension');
                    if (!ratingSelect || !dimensionSelect) return;

                    const rating = ratingSelect.value;
                    const dim = dimensionSelect.value; // q | e | t
                    const raw = standardsInput.value.trim();
                    if (!raw) return;

                    standardsData[idx] = standardsData[idx] || createEmptyStandards();
                    if (!Array.isArray(standardsData[idx][rating][dim])) {
                        standardsData[idx][rating][dim] = [];
                    }
                    standardsData[idx][rating][dim].push(raw);
                    standardsInput.value = '';
                    openStandardsModal(idx);
                }

                function removeStandard(itemIdx, ratingLevel, dim, itemIdxInDim) {
                    if (!standardsData[itemIdx] || !standardsData[itemIdx][ratingLevel]) return;
                    const arr = standardsData[itemIdx][ratingLevel][dim];
                    if (!Array.isArray(arr) || itemIdxInDim === undefined) return;
                    arr.splice(itemIdxInDim, 1);
                    openStandardsModal(itemIdx);
                }

                function closeStandardsModal() {
                    if (standardsModal) {
                        standardsModal.classList.add('hidden');
                        standardsModal.classList.remove('flex');
                    }
                    document.body.classList.remove('overflow-hidden');
                }

                // ===== Indicator CRUD =====
                function startEditIndicator(idx, currentValue) {
                    if (!indicatorsList) return;
                    const rows = Array.from(indicatorsList.children);
                    const targetRow = rows[idx];
                    if (!targetRow) return;

                    const firstTd = targetRow.querySelector('td');
                    if (!firstTd) return;

                    const input = document.createElement('input');
                    input.type = 'text';
                    input.placeholder = 'Enter Success Indicator...';
                    input.className = 'w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm ' +
                                      'text-slate-100 placeholder:text-slate-500 focus:border-blue-500 ' +
                                      'focus:ring-2 focus:ring-blue-500/40 focus:outline-none';
                    input.style.background = '#0f172a';
                    input.style.color = '#e5e7eb';
                    input.value = currentValue || '';

                    // Keep assignment mapping when renaming:
                    const prevKey = (activeIndicators[idx] || '').trim();
                    const prevAssigned = Array.isArray(indicatorAssignments[prevKey]) ? [...indicatorAssignments[prevKey]] : [];

                    firstTd.innerHTML = '';
                    firstTd.appendChild(input);
                    input.focus();
                    input.select();

                    const commit = () => {
                        const next = input.value.trim() || 'New success indicator';
                        activeIndicators[idx] = next;

                        // move assignment to new key
                        delete indicatorAssignments[prevKey];
                        indicatorAssignments[next] = prevAssigned;

                        renderIndicators(activeIndicators);
                    };

                    input.addEventListener('blur', commit);
                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            commit();
                        }
                    });
                }

                function deleteIndicator(idx) {
                    const key = (activeIndicators[idx] || '').trim();
                    activeIndicators.splice(idx, 1);
                    if (key) delete indicatorAssignments[key];
                    renderIndicators(activeIndicators);
                }

                function addIndicator() {
                    activeIndicators.push('New success indicator');
                    seedAssignmentsForIndicators(activeIndicators);
                    renderIndicators(activeIndicators);
                    startEditIndicator(activeIndicators.length - 1, 'New success indicator');
                }

                function openUwpIndicatorsModal(titleText, indicators) {
                    if (indicatorsTitle) indicatorsTitle.textContent = titleText || '--';
                    activeIndicators = Array.isArray(indicators) ? [...indicators] : [];

                    standardsData = activeIndicators.map((text) => seedStandardsForIndicator(text));

                    seedAssignmentsForIndicators(activeIndicators);
                    renderIndicators(activeIndicators);

                    if (indicatorsModal) {
                        indicatorsModal.classList.remove('hidden');
                        indicatorsModal.classList.add('flex');
                        document.body.classList.add('overflow-hidden');
                    }
                }

                window.closeUwpIndicatorsModal = function () {
                    if (indicatorsModal) {
                        indicatorsModal.classList.add('hidden');
                        indicatorsModal.classList.remove('flex');
                    }
                    document.body.classList.remove('overflow-hidden');
                };

                // ===== Wire events =====
                document.querySelectorAll('[data-uwp-indicators]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        let indicators = [];
                        try {
                            indicators = JSON.parse(btn.dataset.indicators || '[]');
                        } catch (e) {
                            indicators = [];
                        }
                        openUwpIndicatorsModal(btn.dataset.title || '--', indicators);
                    });
                });

                if (addIndicatorBtn && isDraft) addIndicatorBtn.addEventListener('click', addIndicator);
                if (addStandardBtn && isDraft) addStandardBtn.addEventListener('click', handleAddStandard);

                const resetStandardBtn = document.getElementById('uwp-reset-standard');
                if (resetStandardBtn && isDraft) {
                    resetStandardBtn.addEventListener('click', () => {
                        const idx = Number(standardsModal?.dataset.currentIndex || 0);
                        standardsData[idx] = seedStandardsForIndicator(activeIndicators[idx] || '');
                        openStandardsModal(idx);
                    });
                }

                if (saveAssignmentsBtn && isDraft) {
                    saveAssignmentsBtn.addEventListener('click', () => {
                        setButtonLoading(saveAssignmentsBtn, true, 'Saving...');
                        setTimeout(() => {
                            setButtonLoading(saveAssignmentsBtn, false);
                            closeAssignedModal();
                        }, 800);
                    });
                }

                // ===== Close on backdrop + Escape =====
                standardsModal?.addEventListener('click', (e) => {
                    if (e.target === standardsModal) closeStandardsModal();
                });

                assignedModal?.addEventListener('click', (e) => {
                    if (e.target === assignedModal) closeAssignedModal();
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        if (standardsModal && !standardsModal.classList.contains('hidden')) {
                            closeStandardsModal();
                        } else if (assignedModal && !assignedModal.classList.contains('hidden')) {
                            closeAssignedModal();
                        } else if (indicatorsModal && !indicatorsModal.classList.contains('hidden')) {
                            closeUwpIndicatorsModal();
                        } else {
                            closeModal();
                        }
                    }
                });

                // expose closers
                window.closeStandardsModal = closeStandardsModal;
                window.closeAssignedModal = closeAssignedModal;
                window.removeStandard = removeStandard;
            });
        </script>
    @endpush
@endsection
