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
                        {{-- Selected office --}}
                        <option selected>
                            Revenue Collection Unit
                        </option>

                        {{-- Other dummy offices --}}
                        <option>Records Management Unit</option>
                        <option>Administrative Services Unit</option>
                        <option>Human Resource Management Unit</option>
                        <option>General Services Unit</option>
                        <option>Planning and Development Unit</option>
                    </select>
                </label>

                <label class="space-y-1 text-sm text-slate-300">
                    <span class="text-xs uppercase tracking-wide text-slate-400">
                        Performance Period
                    </span>

                    <select
                        class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none
                               {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}"
                        style="background:#0f172a;color:#e5e7eb;"
                        {{ $isDraft ? '' : 'disabled' }}
                    >
                        <option value="January - June 2026" selected>
                            January – June 2026
                        </option>
                        <option value="July - December 2026">
                            July – December 2026
                        </option>
                    </select>
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
                    <div class="{{ $isDraft ? '' : 'opacity-60' }}">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-900/70 text-slate-300">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">PPA/MFO</th>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Success Indicators</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Target Difficulty (Planning Only)</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Performance Standard Reference</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Timeline / Target</th>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Notes / Assumptions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 text-slate-100">
                                    <tr class="hover:bg-slate-900/50">
                                        <td class="px-4 py-3">
                                            <input type="text" value="E-Bank Scanning and Encoding of Revenue Transactions" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Records management and archiving" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 text-blue-400 hover:text-blue-300 transition"
                                                data-uwp-indicators
                                                data-title="E-Bank Scanning and Encoding of Revenue Transactions"
                                                data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'
                                                aria-label="View success indicators"
                                            >
                                                <!-- Flowbite / Heroicons eye -->
                                                <svg class="w-4 h-4"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 20 14">
                                                    <g stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5">
                                                        <path d="M10 3c-3.5 0-6.5 2.3-8 5 1.5 2.7 4.5 5 8 5s6.5-2.3 8-5c-1.5-2.7-4.5-5-8-5Z"/>
                                                        <path d="M10 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                    </g>
                                                </svg>

                                                <span class="text-xs">(3)</span>
                                            </button>
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
                                            <input type="text" value="Daily; all e-bank transactions processed within the same working day" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Monthly; 1,200 files" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" value="Subject to transaction volume and system availability" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Context, dependencies, or scope" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                    </tr>

                                    <tr class="hover:bg-slate-900/50">
                                        <td class="px-4 py-3">
                                            <input type="text" value="Processing of Over-the-Counter Revenue Transactions" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Records management and archiving" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 text-blue-400 hover:text-blue-300 transition"
                                                data-uwp-indicators
                                                data-title="Processing of Over-the-Counter Revenue Transactions"
                                                data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'
                                                aria-label="View success indicators"
                                            >
                                                <!-- Flowbite / Heroicons eye -->
                                                <svg class="w-4 h-4"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 20 14">
                                                    <g stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5">
                                                        <path d="M10 3c-3.5 0-6.5 2.3-8 5 1.5 2.7 4.5 5 8 5s6.5-2.3 8-5c-1.5-2.7-4.5-5-8-5Z"/>
                                                        <path d="M10 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                    </g>
                                                </svg>

                                                <span class="text-xs">(3)</span>
                                            </button>
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
                                            <input type="text" value="Daily; 95% processed within the same working day" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Monthly; 1,200 files" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" value="Subject to transaction volume and system availability" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Context, dependencies, or scope" {{ $isDraft ? '' : 'disabled' }}>
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
                    <div class="{{ $isDraft ? '' : 'opacity-60' }}">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-900/70 text-slate-300">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Support Output</th>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Success Indicators</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Target Difficulty (Planning Only)</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Performance Standard Reference</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Target / Timeline</th>
                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">Notes / Assumptions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 text-slate-100">
                                    <tr class="hover:bg-slate-900/50">
                                        <td class="px-4 py-3">
                                            <input type="text" value="Maintenance of revenue records and filing system" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Staff training sessions" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 text-blue-400 hover:text-blue-300 transition"
                                                data-uwp-indicators
                                                data-title="Maintenance of revenue records and filing system"
                                                data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'
                                                aria-label="View success indicators"
                                            >
                                                <!-- Flowbite / Heroicons eye -->
                                                <svg class="w-4 h-4"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 20 14">
                                                    <g stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5">
                                                        <path d="M10 3c-3.5 0-6.5 2.3-8 5 1.5 2.7 4.5 5 8 5s6.5-2.3 8-5c-1.5-2.7-4.5-5-8-5Z"/>
                                                        <path d="M10 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                    </g>
                                                </svg>

                                                <span class="text-xs">(3)</span>
                                            </button>

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
                                            <input value="Quarterly; records validated and properly filed" type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="e.g., Quarterly; 4 sessions" {{ $isDraft ? '' : 'disabled' }}>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input value="Supports audit, verification, and reporting requirements" type="text" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none" style="background:#0f172a;color:#e5e7eb;" placeholder="Stakeholders, coverage, notes" {{ $isDraft ? '' : 'disabled' }}>
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
                            data-loading-text="Submitting..."
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-900/40 transition hover:bg-blue-500 {{ $isDraft ? '' : 'opacity-60 pointer-events-none' }}" {{ $isDraft ? '' : 'disabled' }}>
                        <span data-button-label>Submit for Approval</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                    <span class="text-[11px] text-slate-500">UWP remains editable only while in Draft.</span>
                </div>
            </div>
        </div>
    </section>

    <div id="uwp-indicators-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="uwp-indicators-title" class="text-lg font-semibold text-white">--</h3>
                    <p class="text-xs text-slate-400 mt-1">One output may have multiple success indicators. Indicators describe measurement criteria.</p>
                </div>
                <button type="button" onclick="closeUwpIndicatorsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-3">
                @if ($isDraft)
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400">Manage success indicators (one per line, scalable list).</span>
                        <button type="button" id="uwp-add-indicator" class="inline-flex items-center gap-1 rounded-lg border border-blue-500/50 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200 hover:bg-blue-500/20">
                            <span class="fa-solid fa-plus text-[10px]"></span>
                            <span>Add Indicator</span>
                        </button>
                    </div>
                @endif
                <div class="max-h-64 space-y-2 overflow-y-auto rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <ol id="uwp-indicators-list" class="list-decimal space-y-2 pl-5 text-sm text-slate-100"></ol>
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

                const indicatorsModal = document.getElementById('uwp-indicators-modal');
                const indicatorsTitle = document.getElementById('uwp-indicators-title');
                const indicatorsList = document.getElementById('uwp-indicators-list');
                const addIndicatorBtn = document.getElementById('uwp-add-indicator');
                let activeIndicators = [];
                const isDraft = {{ $isDraft ? 'true' : 'false' }};

                function renderIndicators(list) {
                    if (!indicatorsList) return;
                    indicatorsList.innerHTML = '';
                    list.forEach((item, idx) => {
                        const value = (item || '').trim();
                        if (!value) return;
                        const li = document.createElement('li');
                        li.className = 'flex items-start gap-2';

                        const textWrap = document.createElement('div');
                        textWrap.className = 'flex-1 space-y-1';
                        const textSpan = document.createElement('span');
                        textSpan.className = 'text-slate-100';
                        textSpan.textContent = value;
                        textWrap.appendChild(textSpan);

                        if (isDraft) {
                            const actions = document.createElement('div');
                            actions.className = 'flex items-center gap-2 text-[11px] text-blue-200';

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

                            actions.appendChild(editBtn);
                            actions.appendChild(delBtn);
                            li.appendChild(actions);
                        }

                        li.prepend(textWrap);
                        indicatorsList.appendChild(li);
                    });
                }

                function startEditIndicator(idx, currentValue) {
                    if (!indicatorsList) return;
                    const items = Array.from(indicatorsList.children);
                    const target = items[idx];
                    if (!target) return;
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.placeholder = 'Enter Success Indicator...'
                    input.className ='w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm ' +
                                    'text-slate-100 placeholder:text-slate-500 focus:border-blue-500 ' +
                                    'focus:ring-2 focus:ring-blue-500/40 focus:outline-none';
                    input.style.background = '#0f172a';
                    input.style.color = '#e5e7eb';
                    const textWrap = target.querySelector('div.flex-1');
                    if (!textWrap) return;
                    textWrap.innerHTML = '';
                    textWrap.appendChild(input);
                    input.focus();
                    input.select();
                    const commit = () => {
                        activeIndicators[idx] = input.value.trim();
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
                    activeIndicators.splice(idx, 1);
                    renderIndicators(activeIndicators);
                }

                function addIndicator() {
                    activeIndicators.push('New success indicator');
                    renderIndicators(activeIndicators);
                    startEditIndicator(activeIndicators.length - 1, 'New success indicator');
                }

                function openUwpIndicatorsModal(title, indicators) {
                    if (indicatorsTitle) indicatorsTitle.textContent = title || '--';
                    activeIndicators = Array.isArray(indicators) ? [...indicators] : [];
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

                if (addIndicatorBtn && isDraft) {
                    addIndicatorBtn.addEventListener('click', addIndicator);
                }
            });
        </script>
    @endpush
@endsection
