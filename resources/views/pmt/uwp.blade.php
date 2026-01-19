<x-layouts.pmt>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1>Unit Work Plan Approval</h1>
        <p>Final review and approval of Unit Work Plans for standards compliance and alignment.</p>
    </div>

    {{-- Performance Period --}}
    <div class="mb-6 rounded-2xl border border-slate-800 bg-slate-950 p-5">
        <p class="text-xs uppercase tracking-wide text-slate-400">Performance Period</p>
        <p class="font-medium text-slate-100">January – June 2026</p>
    </div>

    {{-- UWP List --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-950 overflow-hidden">
        <div class="border-b border-slate-800 p-5">
            <h2 class="text-lg font-medium text-white">Unit Work Plans</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">Office / Unit</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">Supervisor</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">Dept Head</th>
                        <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Status</th>
                        <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <tr class="hover:bg-slate-900/60 transition">
                        <td class="px-4 py-3 text-sm font-semibold text-slate-100">
                            Revenue Collection Unit
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            Carlo D. Beray
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            Dept-head
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                For PMT Approval
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <button
                                type="button"
                                data-modal-target="pmt-view-uwp-modal"
                                data-modal-toggle="pmt-view-uwp-modal"
                                class="rounded-lg border border-blue-500 px-3 py-2 text-blue-400
                                hover:bg-blue-500/10 transition">
                                View UWP
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- PMT VIEW UWP MODAL --}}
    {{-- ========================= --}}
    <div
        id="pmt-view-uwp-modal"
        data-modal-container
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur"
    >
        <div class="w-full max-w-5xl px-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 shadow-2xl">

                {{-- Modal Header --}}
                <div class="flex items-start justify-between border-b border-slate-800 px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-indigo-300">
                            Unit Work Plan
                        </p>
                        <h3 class="text-lg font-semibold text-white">
                            Revenue Collection Unit
                        </h3>
                        <p class="mt-1 text-sm text-slate-400">
                            Jan – June 2026 • Supervisor: Carlo D. Beray
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Export (enabled only if Approved Final) --}}
                        <a href="{{ route('stage1.uwp.export.pdf') }}"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                            Export PDF
                        </a>

                        <button class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                            Export Excel
                        </button>

                        <button
                            type="button"
                            data-modal-close
                            data-modal-hide="pmt-view-uwp-modal"
                            class="text-slate-400 hover:text-white"
                        >
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5 space-y-6">

                    {{-- Metadata --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-lg border border-slate-800 bg-slate-900 p-4">
                            <p class="text-xs uppercase text-slate-500">Office / Unit</p>
                            <p class="mt-1 text-sm font-semibold text-white">Revenue Collection Unit</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-900 p-4">
                            <p class="text-xs uppercase text-slate-500">Department Head</p>
                            <p class="mt-1 text-sm font-semibold text-white">Dept-head</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-900 p-4">
                            <p class="text-xs uppercase text-slate-500">Status</p>
                            <p class="mt-1 text-sm font-semibold text-emerald-300">For PMT Approval</p>
                        </div>
                    </div>

                    {{-- Planned Outputs --}}
                    <div class="rounded-xl border border-slate-800 bg-slate-900 overflow-hidden">
                        <div class="border-b border-slate-800 p-4">
                            <h4 class="text-sm font-semibold text-white">Planned Outputs</h4>
                        </div>

                        <table class="min-w-full">
                            <thead class="bg-slate-800/60">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">PPA / MFO</th>
                                    <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">Success Indicators</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Target / Timeline</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Function</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-white">E-Bank Scanning and Encoding of Revenue Transactions</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">
                                        <button type="button"
                                                class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-indicators
                                                data-title="E-Bank Scanning and Encoding of Revenue Transactions"
                                                data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>(3)</span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-white">Daily; all e-bank transactions processed within the same working day</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium
                                            text-emerald-400 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-4 py-3 text-sm text-slate-100">Processing of over-the-counter revenue transactions</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">
                                        <button type="button"
                                                class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-indicators
                                                data-title="Processing of over-the-counter revenue transactions"
                                                data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>(3)</span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-100">Daily; 95% processed within the same working day</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-300 border border-blue-400/30">
                                            Core
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-4 py-3 text-sm text-slate-100">Maintenance of Revenue Records Filing System</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">
                                        <button type="button"
                                                class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-indicators
                                                data-title="Maintenance of Revenue Records Filing System"
                                                data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>(3)</span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-100">Quarterly validation and update</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-300 border border-blue-400/30">
                                            Support
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-xl border border-slate-800 bg-slate-900 p-4 space-y-3">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            PMT Decision Remarks
                            <span class="text-slate-500">(required if returning)</span>
                        </label>

                        <textarea
                            rows="3"
                            style="background:#0f172a;color:#e5e7eb;"
                            placeholder="Provide justification or revision instructions if returning the UWP."
                            class="w-full rounded-lg border border-slate-700 bg-slate-900
                                px-3 py-2 text-sm text-slate-100 placeholder-slate-400">
                        </textarea>
                    </div>

                    <div class="rounded-xl border border-slate-800 bg-slate-900 p-4 space-y-3">
                        <p class="text-sm font-semibold text-white">
                            PMT Review Basis (Governance Reference)
                        </p>

                        <ul class="space-y-2 text-sm text-slate-300">
                            <li>✓ Aligned with approved OPCR and organizational targets</li>
                            <li>✓ Outputs are specific, measurable, and time-bound</li>
                            <li>✓ Core and support functions are properly classified</li>
                            <li>✓ Targets comply with SPMS performance standards</li>
                        </ul>

                        <p class="text-xs text-slate-400">
                            This checklist guides PMT decision-making. It does not modify targets or outputs.
                        </p>
                    </div>

                </div>

                <div class="flex flex-wrap items-center justify-between border-t border-slate-800 px-6 py-4">
                    <p class="text-xs text-slate-500">
                        PMT decision is final and will lock the Unit Work Plan.
                    </p>

                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            data-admin-loading="true"
                            data-loading-text="Returning..."
                            class="inline-flex items-center gap-2
                                rounded-lg border border-rose-500/30 bg-rose-600/10 px-4 py-2
                                text-sm font-semibold text-rose-300
                                hover:bg-rose-600/20 transition">
                            <span data-button-spinner
                                class="hidden h-4 w-4 animate-spin rounded-full
                                        border-2 border-rose-300/40 border-t-rose-300"></span>
                            <span data-button-label>Return to Dept Head</span>
                        </button>

                        <button
                            type="button"
                            data-admin-loading="true"
                            data-loading-text="Approving..."
                            class="inline-flex items-center gap-2
                                rounded-lg bg-emerald-600 px-4 py-2
                                text-sm font-semibold text-white
                                hover:bg-emerald-500 transition">
                            <span data-button-spinner
                                class="hidden h-4 w-4 animate-spin rounded-full
                                        border-2 border-white/40 border-t-white"></span>
                            <span data-button-label>Approve (Final)</span>
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div id="uwp-indicators-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="uwp-indicators-title" class="text-lg font-semibold text-white">--</h3>
                    <p class="text-xs text-slate-400 mt-1">Read-only list of indicators for this output.</p>
                </div>
                <button type="button" onclick="closePmtIndicatorsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 max-h-64 overflow-y-auto rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                <ol id="uwp-indicators-list" class="list-decimal space-y-2 pl-5 text-sm text-slate-100"></ol>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closePmtIndicatorsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function initPmtIndicatorsModal() {
                const modal = document.getElementById('uwp-indicators-modal');
                const titleEl = document.getElementById('uwp-indicators-title');
                const listEl = document.getElementById('uwp-indicators-list');

                window.closePmtIndicatorsModal = function () {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                };

                function openIndicatorsModal(title, indicators) {
                    if (!modal || !titleEl || !listEl) return;
                    titleEl.textContent = title || '--';
                    listEl.innerHTML = '';
                    (indicators || []).forEach((text) => {
                        const value = (text || '').trim();
                        if (!value) return;
                        const li = document.createElement('li');
                        li.textContent = value;
                        listEl.appendChild(li);
                    });
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                document.querySelectorAll('[data-uwp-view-indicators]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        let indicators = [];
                        try {
                            indicators = JSON.parse(btn.dataset.indicators || '[]');
                        } catch (e) {
                            indicators = [];
                        }
                        openIndicatorsModal(btn.dataset.title || '--', indicators);
                    });
                });
            })();
        </script>
    @endpush
</x-layouts.pmt>
