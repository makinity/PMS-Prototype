<x-layouts.pmt>
    <section class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage III – OPCR Approval
                </p>
                <h1 class="text-2xl font-semibold text-white">OPCR Approval List</h1>
                <p class="text-sm text-slate-400">
                    OPCR Accomplishments awaiting PMT approval.
                </p>
            </div>
        </div>

        <!-- OPCR APPROVAL LIST -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Office / Unit</th>
                            <th class="px-4 py-2 text-left">Period</th>
                            <th class="px-4 py-2 text-left">IPCRs Included</th>
                            <th class="px-4 py-2 text-left">Overall OPCR Rating</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800 hover:bg-slate-900/60">
                            <td class="px-4 py-3 text-white">Revenue Collection Unit</td>
                            <td class="px-4 py-3">Jan–Jun 2026</td>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3">5.00 – Outstanding</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300 border border-amber-500/30">
                                    For PMT Approval
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('pmt.opcr-app-view') }}"
                                   class="inline-flex items-center gap-2 rounded-lg border border-blue-500 text-blue-400 px-3 py-2 text-xs font-semibold hover:bg-blue-500/10 transition">
                                    Review OPCR
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-[11px] text-slate-500">
            Select an OPCR to review and approve. Approval is performed on the next screen.
        </p>

    </section>
</x-layouts.pmt>
