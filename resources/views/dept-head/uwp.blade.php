<x-layouts.dept-head>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-100">Unit Work Plan Review</h1>
        <p class="text-sm text-slate-400 mt-1">
            Select an office/unit to review its submitted Unit Work Plan. Approve or return with remarks.
        </p>
    </div>

    {{-- View Endorsed UWP Modal --}}
    <div id="view-uwp-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur" data-modal-hide="view-uwp-modal"></div>
        <div class="relative z-10 w-full max-w-5xl px-4">
            <div class="w-full overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-4">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-emerald-300">Endorsed UWP</p>
                        <h3 class="text-lg font-semibold text-white">View UWP</h3>
                        <div class="grid grid-cols-1 gap-3 text-sm text-slate-300 sm:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Office / Unit</p>
                                <p class="font-medium text-slate-100">Records Management Unit</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Supervisor</p>
                                <p class="font-medium text-slate-100">Carlo D. Beray</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Performance Period</p>
                                <p class="font-medium text-slate-100">January - June 2026</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">UWP Type</p>
                                <p class="font-medium text-slate-100">Unit-Level Plan</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                            Endorsed by Dept Head
                        </span>
                    </div>
                    <button type="button" data-modal-hide="view-uwp-modal"
                            class="text-slate-400 transition hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                        <div class="border-b border-slate-800 px-4 py-3">
                            <h4 class="text-sm font-medium text-slate-100">Planned Outputs</h4>
                            <p class="text-xs text-slate-400 mt-1">Read-only. Endorsed UWPs are locked.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-slate-800/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-400">Major Final Output</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-400">Expected Output</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Target</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Timeframe</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Function</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800">
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="px-4 py-3 text-sm text-slate-100">Records Management</td>
                                        <td class="px-4 py-3 text-sm text-slate-300">Process and file incoming documents</td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-100">1,200 documents</td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-300">Jan ƒ?\" Dec</td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span class="px-2 py-1 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                Core
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="px-4 py-3 text-sm text-slate-100">Client Support</td>
                                        <td class="px-4 py-3 text-sm text-slate-300">Coordinate and resolve service tickets</td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-100">95% resolved on time</td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-300">Quarterly</td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span class="px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-300 border border-blue-400/30">
                                                Support
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-800 px-6 py-4">
                    <button type="button" data-modal-hide="view-uwp-modal"
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-slate-800/80">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters / Meta (Optional but useful) --}}
    <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Performance Period</p>
            <p class="font-medium text-slate-100">January – June 2026</p>
        </div>

        <div class="flex items-center gap-3">
            <input
                type="text"
                placeholder="Search office/unit..."
                class="w-full md:w-72
                    bg-slate-900 text-slate-100 placeholder-slate-300
                    border border-slate-700
                    rounded-lg px-3 py-2 text-sm
                    focus:bg-slate-900 focus:border-blue-500
                    focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                style="background:#0f172a;color:#e5e7eb;"
            />
            <select
                style="background:#0f172a;color:#e5e7eb;"
                class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-2
                text-sm text-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option>All Status</option>
                <option>Submitted</option>
                <option>Draft</option>
                <option>Endorsed</option>
                <option>Returned</option>
            </select>
        </div>
    </div>

    {{-- Office/Unit List --}}
    <div class="bg-slate-900/80 border border-slate-800 rounded-xl overflow-hidden">
        <div class="p-5 border-b border-slate-800">
            <h2 class="text-lg font-medium text-slate-100">Offices / Units</h2>
            <p class="text-sm text-slate-400 mt-1">
                Click a unit to open its UWP planned outputs.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-800/60">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Office / Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Supervisor</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">UWP Type</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-800">
                    {{-- SAMPLE ROW: Submitted --}}
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3 text-sm text-slate-100 font-medium">
                            Revenue Collection Unit
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            Carlo D. Beray
                        </td>
                        <td class="px-4 py-3 text-sm text-center text-slate-300">
                            Unit-Level Plan
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full
                                bg-blue-500/10 text-blue-300 border border-blue-500/20">
                                Submitted
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            {{-- Clicking opens modal --}}
                            <a href="#uwp-unit-1"
                               data-modal-target="uwp-review-modal"
                               data-modal-toggle="uwp-review-modal"
                               class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg
                               border border-blue-500 text-blue-400 hover:bg-blue-500/10 transition">
                                Review UWP
                            </a>
                        </td>
                    </tr>

                    {{-- SAMPLE ROW: Endorsed --}}
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3 text-sm text-slate-100 font-medium">
                            Records Management Unit
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            Carlo D. Beray
                        </td>
                        <td class="px-4 py-3 text-sm text-center text-slate-300">
                            Unit-Level Plan
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full
                                bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                Endorsed
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <a href="#uwp-unit-2"
                               data-modal-target="view-uwp-modal"
                               data-modal-toggle="view-uwp-modal"
                               class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg
                               border border-slate-700 text-slate-300 hover:bg-slate-800/60 transition">
                                View
                            </a>
                        </td>
                    </tr>

                    {{-- Add more rows here --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- MODAL 1: Review UWP --}}
    {{-- ========================= --}}
    <div id="uwp-unit-1" class="fixed inset-0 z-50 hidden target:block">
        {{-- Backdrop --}}
        <a href="#" class="absolute inset-0 bg-black/60"></a>

        {{-- Modal Card --}}
        <div class="relative mx-auto mt-10 w-[92%] md:w-[900px] bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            {{-- Modal Header --}}
            <div class="p-5 border-b border-slate-800 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-100">Unit Work Plan (UWP)</h3>
                    <p class="text-sm text-slate-400 mt-1">
                        Revenue Collection Unit • January – June 2026
                    </p>

                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full
                            bg-blue-500/10 text-blue-300 border border-blue-500/20">
                            Submitted for Approval
                        </span>
                        <span class="text-xs text-slate-500">
                            Supervisor: <span class="text-slate-300">Carlo D. Beray</span> •
                            Dept Head: <span class="text-slate-300">Engr. Roberto Reyes</span>
                        </span>
                    </div>
                </div>

                <a href="#"
                   class="px-3 py-2 text-sm rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800/60 transition">
                    Close
                </a>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 space-y-6 max-h-[70vh] overflow-y-auto">
                {{-- Planned Outputs --}}
                <div class="bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="p-4 border-b border-slate-800">
                        <h4 class="text-sm font-medium text-slate-100">Planned Outputs</h4>
                        <p class="text-xs text-slate-400 mt-1">Read-only. Review targets and classification.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Major Final Output</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Expected Output</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Target</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Timeframe</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Function</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-slate-100">Records Management</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">Process and file incoming documents</td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-100">1,200 documents</td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-300">Jan – Dec</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="px-2 py-1 rounded-md text-xs font-medium
                                            bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>
                                {{-- Add more planned outputs rows --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Review Remarks --}}
                <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-4">
                    <h4 class="text-sm font-medium text-slate-100 mb-2">Review Remarks</h4>
                    <textarea
                        rows="4"
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3
                        text-sm text-slate-200 placeholder-slate-500
                        focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Add remarks or justification (required if returning the plan)..."></textarea>
                    <p class="text-xs text-slate-500 mt-2">
                        Tip: If returning, include specific revision instructions (e.g., adjust targets, clarify indicators).
                    </p>
                </div>
            </div>

            {{-- Modal Footer Actions --}}
            <div class="p-5 border-t border-slate-800 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="text-xs text-slate-500">
                    This action will be recorded in the audit log.
                </div>

                <div class="flex items-center gap-3 justify-end">
                    <button
                        class="px-4 py-2 text-sm font-medium rounded-lg
                        bg-emerald-600 hover:bg-emerald-500 text-white transition">
                        Endorse & Forward to PMT
                    </button>

                    <button
                        class="px-4 py-2 text-sm font-medium rounded-lg
                        bg-rose-600/10 text-rose-400 border border-rose-500/30
                        hover:bg-rose-600/20 transition">
                        Return for Revision
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- MODAL 2: View Endorsed UWP --}}
    {{-- ========================= --}}
    <div id="uwp-unit-2" class="fixed inset-0 z-50 hidden target:block">
        <a href="#" class="absolute inset-0 bg-black/60"></a>

        <div class="relative mx-auto mt-10 w-[92%] md:w-[900px] bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-800 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-100">Unit Work Plan (UWP)</h3>
                    <p class="text-sm text-slate-400 mt-1">Records Management Unit • January – June 2026</p>

                    <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full
                            bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                            Endorsed
                        </span>
                    </div>
                </div>

                <a href="#"
                   class="px-3 py-2 text-sm rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800/60 transition">
                    Close
                </a>
            </div>

            <div class="p-5 max-h-[70vh] overflow-y-auto">
                <div class="bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="p-4 border-b border-slate-800">
                        <h4 class="text-sm font-medium text-slate-100">Planned Outputs</h4>
                        <p class="text-xs text-slate-400 mt-1">Read-only. Endorsed UWPs cannot be modified.</p>
                    </div>
                    <div class="p-4 text-sm text-slate-300">
                        (Put the same planned outputs table here.)
                    </div>
                </div>
            </div>

            <div class="p-5 border-t border-slate-800 flex justify-end">
                <a href="#"
                   class="px-4 py-2 text-sm font-medium rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800/60 transition">
                    Close
                </a>
            </div>
        </div>
    </div>

    {{-- Review UWP Modal (Flowbite-style) --}}
    <div id="uwp-review-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur" data-modal-hide="uwp-review-modal"></div>
        <div class="relative z-10 w-full max-w-5xl px-4">
            <div class="w-full overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-4">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Unit Work Plan</p>
                        <h3 class="text-lg font-semibold text-white">Review UWP</h3>
                        <div class="grid grid-cols-1 gap-3 text-sm text-slate-300 sm:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Office / Unit</p>
                                <p class="font-medium text-slate-100">Revenue Collection Unit</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Supervisor</p>
                                <p class="font-medium text-slate-100">Carlo D. Beray                                </p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Performance Period</p>
                                <p class="font-medium text-slate-100">January - June 2026</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">UWP Type</p>
                                <p class="font-medium text-slate-100">Unit-Level Plan</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" data-modal-hide="uwp-review-modal"
                            class="text-slate-400 transition hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                        <div class="border-b border-slate-800 px-4 py-3">
                            <h4 class="text-sm font-medium text-slate-100">Planned Outputs</h4>
                            <p class="text-xs text-slate-400 mt-1">Read-only reference for review.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-slate-800/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-400">Major Final Output</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-400">Expected Output</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Target</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Timeframe</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Function</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800">
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="px-4 py-3 text-sm text-slate-100">E-Bank Scanning and Encoding of Revenue Transactions</td>
                                        <td class="px-4 py-3 text-sm text-slate-300">All e-bank transaction documents are scanned, encoded, and uploaded to the system with complete details and proper indexing.</td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-100">Verified and accurately recorded over-the-counter revenue transactions.</td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-300">Daily (January – June 2026)</td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span class="px-2 py-1 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                Core
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="px-4 py-3 text-sm text-slate-100">Processing of over-the-counter revenue transactions</td>
                                        <td class="px-4 py-3 text-sm text-slate-300">All over-the-counter revenue transactions are verified, recorded, and encoded accurately within the same working day.</td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-100">Daily; 95% processed within the same working day   </td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-300">Daily (January – June 2026)</td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span class="px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-300 border border-blue-400/30">
                                                Core
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="px-4 py-3 text-sm text-slate-100">Maintenance of Revenue Records Filing System</td>
                                        <td class="px-4 py-3 text-sm text-slate-300">Organized and updated physical and digital filing of revenue transaction records.</td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-100">Quarterly validation and update</td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-300">Quarterly</td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span class="px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-300 border border-blue-400/30">
                                                Support
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 border-t border-slate-800 px-6 py-4">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Review Remarks <span class="text-slate-500">(required if returning)</span>
                        </label>
                        <textarea
                            rows="3"
                            required
                            class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            style="background:#0f172a;color:#e5e7eb;"
                            placeholder="Add clear instructions or justification for your decision..."></textarea>
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-3">
                        <button type="button" data-modal-hide="uwp-review-modal"
                                class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-slate-800/80">
                            Cancel
                        </button>
                        <button type="button"
                                data-admin-loading="true"
                                data-loading-text="Processing..."
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 transition hover:bg-emerald-500">
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                            <span data-button-label>Endorse & Forward to PMT</span>
                        </button>
                        <button type="button"
                                data-admin-loading="true"
                                data-loading-text="Returning..."
                                class="inline-flex items-center gap-2 rounded-lg border border-rose-500/30 bg-rose-600/10 px-4 py-2 text-sm font-semibold text-rose-200 transition hover:bg-rose-600/20">
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-rose-200/40 border-t-rose-200"></span>
                            <span data-button-label>Return for Revision</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function initViewUwpModal() {
                const run = () => {
                    const viewModal = document.getElementById('view-uwp-modal');
                    const viewTriggers = document.querySelectorAll('[data-modal-target="view-uwp-modal"]');

                    if (!viewModal || !viewTriggers.length) {
                        return;
                    }

                    const viewModalInstance = typeof Modal !== 'undefined' && viewModal ? new Modal(viewModal) : null;

                    const showViewModal = () => {
                        if (viewModalInstance) {
                            viewModalInstance.show();
                        } else {
                            viewModal.classList.remove('hidden');
                        }
                    };

                    const hideViewModal = () => {
                        if (viewModalInstance) {
                            viewModalInstance.hide();
                        } else {
                            viewModal.classList.add('hidden');
                        }
                    };

                    viewTriggers.forEach((trigger) => {
                        trigger.addEventListener('click', (event) => {
                            event.preventDefault();
                            showViewModal();
                        });
                    });

                    viewModal.querySelectorAll('[data-modal-hide="view-uwp-modal"]').forEach((btn) => {
                        btn.addEventListener('click', (event) => {
                            event.preventDefault();
                            hideViewModal();
                        });
                    });

                    viewModal.addEventListener('click', (event) => {
                        if (event.target === viewModal) {
                            hideViewModal();
                        }
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', run);
                } else {
                    run();
                }
            })();
        </script>
    @endpush

</x-layouts.dept-head>
