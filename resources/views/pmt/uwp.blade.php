<x-layouts.pmt>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-white">
            Unit Work Plan Monitoring
        </h1>
        <p class="text-sm text-slate-400 mt-1">
            View and validate Unit Work Plans for standards compliance and alignment.
        </p>
    </div>

    {{-- Performance Period --}}
    <div class="mb-6 rounded-2xl border border-slate-800 bg-slate-950 p-5">
        <p class="text-xs uppercase tracking-wide text-slate-400">Performance Period</p>
        <p class="font-medium text-slate-100">January – December 2026</p>
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
                            Administrative Services Unit
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            Maria Santos
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            Engr. Roberto Reyes
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                Approved
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
                            Administrative Services Unit
                        </h3>
                        <p class="mt-1 text-sm text-slate-400">
                            Jan – Dec 2026 • Supervisor: Maria Santos
                        </p>
                    </div>
                    <button
                        type="button"
                        data-modal-close
                        data-modal-hide="pmt-view-uwp-modal"
                        class="text-slate-400 hover:text-white"
                    >
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5 space-y-6">

                    {{-- Metadata --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-lg border border-slate-800 bg-slate-900 p-4">
                            <p class="text-xs uppercase text-slate-500">Office / Unit</p>
                            <p class="mt-1 text-sm font-semibold text-white">Administrative Services Unit</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-900 p-4">
                            <p class="text-xs uppercase text-slate-500">Department Head</p>
                            <p class="mt-1 text-sm font-semibold text-white">Engr. Roberto Reyes</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-900 p-4">
                            <p class="text-xs uppercase text-slate-500">Status</p>
                            <p class="mt-1 text-sm font-semibold text-emerald-300">Approved (Locked)</p>
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
                                    <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">MFO</th>
                                    <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">Expected Output</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Target</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Timeframe</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Function</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-white">Records Management</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">Process and file incoming documents</td>
                                    <td class="px-4 py-3 text-sm text-center text-white">1,200</td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-300">Jan – Dec</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium
                                            text-emerald-400 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- PMT Comments --}}
                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            PMT Observations (Advisory)
                        </label>
                        <textarea
                            rows="3"
                            placeholder="Optional notes on alignment, clarity, or standardization"
                            class="w-full rounded-lg border border-slate-700 bg-slate-900
                            px-3 py-2 text-sm text-slate-100 placeholder-slate-400
                            focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                            style="background:#0f172a;color:#e5e7eb;"
                        ></textarea>
                        <p class="text-xs text-slate-400">
                            Notes here do not block approval and are for governance reference only.
                        </p>
                        <div class="flex justify-end">
                            <button
                                type="button"
                                data-admin-loading="true"
                                data-loading-text="Sending..."
                                class="mt-2 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 transition hover:bg-indigo-500">
                                <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                                <span data-button-label>Send</span>
                            </button>
                        </div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                    <button
                        type="button"
                        data-modal-hide="pmt-view-uwp-modal"
                        class="rounded-lg border border-slate-700 px-4 py-2
                        text-sm font-semibold text-slate-200 hover:bg-slate-800 transition"
                    >
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>
</x-layouts.pmt>
