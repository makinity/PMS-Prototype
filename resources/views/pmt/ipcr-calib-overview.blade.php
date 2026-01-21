<x-layouts.pmt>
    <section class="space-y-6">

        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage III – PMT Final Calibration
                </p>
                <h1 class="text-2xl font-bold text-white">IPCR Calibration Overview</h1>
                <p class="text-sm text-slate-400">
                    Office-level calibration overview. Review rating distribution by office/unit before drilling into individual IPCR calibration.
                </p>
                <p class="text-[11px] text-slate-500 mt-1">
                    Monitoring only — no individual calibration actions on this screen.
                </p>
            </div>
        </div>

        @php
            $offices = [
                [
                    'office' => 'Revenue Collection Unit',
                    'average' => '5.00',
                    'pct_five' => '100%',
                    'compression' => 'Within expected spread',
                    'status' => 'Within Norms',
                    'status_class' => 'bg-emerald-500/10 text-emerald-200 border-emerald-500/30',
                ],
                [
                    'office' => 'Records Management Unit',
                    'average' => '4.25',
                    'pct_five' => '40%',
                    'compression' => 'High concentration at 5.00',
                    'status' => 'Needs Review',
                    'status_class' => 'bg-amber-500/10 text-amber-200 border-amber-500/30',
                ],
            ];
        @endphp

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Office Calibration Overview</h2>
                    <p class="text-xs text-slate-400">Select an office to review and finalize IPCR calibration. Individual entries are accessed from the office drill-down screen.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-white">Office / Unit Comparison (Reference)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs text-slate-200">
                        <thead class="bg-slate-800/60">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Office / Unit</th>
                                <th class="px-3 py-2 text-left font-semibold">Average Rating</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr><td class="px-3 py-2">Revenue Collection Unit</td><td class="px-3 py-2">5.00</td></tr>
                            <tr><td class="px-3 py-2">Records Management Unit</td><td class="px-3 py-2">4.25</td></tr>
                            <tr class="bg-slate-800/40 font-semibold"><td class="px-3 py-2">Organization Average</td><td class="px-3 py-2">4.63</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-xs text-slate-400 space-y-1">
                    <p>✔ High concentration at 5.00</p>
                    <p>✔ Revenue Collection Unit at upper bound</p>
                    <p>✔ Potential rating compression</p>
                </div>
            </div>
        </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Average Rating</th>
                            <th class="px-4 py-3 text-left">% at 5.00</th>
                            <th class="px-4 py-3 text-left">Compression / Spread</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach ($offices as $office)
                            <tr class="hover:bg-slate-900">
                                <td class="px-4 py-3 text-slate-200">{{ $office['office'] }}</td>
                                <td class="px-4 py-3">{{ $office['average'] }}</td>
                                <td class="px-4 py-3">{{ $office['pct_five'] }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $office['compression'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $office['status_class'] }}">
                                        {{ $office['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('pmt.ipcr-calib') }}"
                                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500 transition">
                                        Review Office
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</x-layouts.pmt>
