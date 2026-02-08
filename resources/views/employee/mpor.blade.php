@extends('layouts.employee')

@section('main-content')
    <section class="space-y-6">

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Monthly Performance Output Report</p>
                <h1 class="mt-1 text-2xl font-bold text-white">MONTHLY PERFORMANCE OUTPUT REPORT</h1>
                <p class="text-sm text-slate-400 mt-1">
                    Locked Stage II data � supervisor-rated ORS entries mapped to MPOR rows.
                </p>
                <div class="mt-3 flex gap-4 text-xs uppercase tracking-[0.3em] text-white">
                    <div>
                        <p class="text-slate-400">NAME</p>
                        <p class="font-semibold">Ramon Reyes</p>
                    </div>
                    <div>
                        <p class="text-slate-400">OFFICE / DIVISION</p>
                        <p class="font-semibold">Revenue Collection Unit</p>
                    </div>
                    <div>
                        <p class="text-slate-400">MONTH</p>
                        <p class="font-semibold">January 2026</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-end gap-2">

                <a href="{{ route('employee.mpor.export.pdf') }}"
                    class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    Export PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <table class="min-w-full text-[0.75rem] text-slate-200">
                <thead>
                    <tr class="text-left text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                        <th class="whitespace-nowrap px-3 py-2 align-bottom" rowspan="2">Output / Task</th>
                        <th class="px-3 py-2 border-l border-slate-800" colspan="5">Efficiency / Quantity</th>
                        <th class="px-3 py-2 border-l border-slate-800" colspan="5">Quality / Effectiveness</th>
                        <th class="px-3 py-2 border-l border-slate-800" colspan="5">Timeliness</th>
                    </tr>
                    <tr class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">
                        <th class="px-2 py-1 border-l border-slate-800">W1</th>
                        <th class="px-2 py-1">W2</th>
                        <th class="px-2 py-1">W3</th>
                        <th class="px-2 py-1">W4</th>
                        <th class="px-2 py-1">Total</th>
                        <th class="px-2 py-1 border-l border-slate-800">W1</th>
                        <th class="px-2 py-1">W2</th>
                        <th class="px-2 py-1">W3</th>
                        <th class="px-2 py-1">W4</th>
                        <th class="px-2 py-1">Total</th>
                        <th class="px-2 py-1 border-l border-slate-800">W1</th>
                        <th class="px-2 py-1">W2</th>
                        <th class="px-2 py-1">W3</th>
                        <th class="px-2 py-1">W4</th>
                        <th class="px-2 py-1">Total</th>
                    </tr>
                </thead>
                <tbody class="text-[0.75rem] font-light">
                    <tr class="bg-slate-800/40 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                        <td class="px-3 py-2 font-semibold text-slate-200" colspan="16">Core Functions (80%)</td>
                    </tr>
                    <tr class="border-t border-slate-800 text-slate-200">
                        <td class="px-3 py-2 font-medium">Processing of Over-the-Counter Revenue Transactions</td>
                        <td class="px-2 py-1">12</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">12</td>
                        <td class="px-2 py-1 border-l border-slate-800">60</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">60</td>
                        <td class="px-2 py-1 border-l border-slate-800">60</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">60</td>
                    </tr>
                    <tr class="border-t border-slate-800 text-slate-200">
                        <td class="px-3 py-2 font-medium">E-Bank Scanning and Encoding of Revenue Transactions</td>
                        <td class="px-2 py-1">1</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">1</td>
                        <td class="px-2 py-1 border-l border-slate-800">5</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">5</td>
                        <td class="px-2 py-1 border-l border-slate-800">5</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">5</td>
                    </tr>
                    <tr class="border-t border-slate-700 bg-slate-900/60 font-semibold text-slate-100">
                        <td class="px-3 py-2">TOTAL � CORE</td>
                        <td class="px-2 py-1">13</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">13</td>
                        <td class="px-2 py-1 border-l border-slate-800">65</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">65</td>
                        <td class="px-2 py-1 border-l border-slate-800">65</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">65</td>
                    </tr>
                    <tr class="bg-slate-800/40 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                        <td class="px-3 py-2 font-semibold text-slate-200" colspan="16">Support Functions (20%)</td>
                    </tr>
                    <tr class="border-t border-slate-800 text-slate-200">
                        <td class="px-3 py-2 font-medium">Maintenance of Revenue Records Filing System</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1 border-l border-slate-800">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1 border-l border-slate-800">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                    </tr>
                    <tr class="border-t border-slate-700 bg-slate-900/60 font-semibold text-slate-100">
                        <td class="px-3 py-2">TOTAL � SUPPORT</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1 border-l border-slate-800">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1 border-l border-slate-800">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                    </tr>
                    <tr class="border-t border-dashed border-slate-700 text-slate-300">
                        <td class="px-3 py-2 font-semibold">GRAND TOTAL</td>
                        <td class="px-2 py-1">13</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">13</td>
                        <td class="px-2 py-1 border-l border-slate-800">65</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">65</td>
                        <td class="px-2 py-1 border-l border-slate-800">65</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">0</td>
                        <td class="px-2 py-1">65</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="mt-3 text-xs text-slate-400">Stage II demo: MPOR points = Quantity � Supervisor Rating (Q/T). Batch quantities are treated as single units.</p>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                    <span>Week 1</span>
                    <span>Week 2</span>
                    <span>Week 3</span>
                    <span>Week 4</span>
                    <span>Total</span>
                </div>
                <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                    <span>13</span>
                    <span>0</span>
                    <span>0</span>
                    <span>0</span>
                    <span>13</span>
                </div>
                <div class="mt-3 space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                    <div class="flex items-center justify-between">
                        <span>Man day(s) lost thru absence</span>
                        <span class="text-white font-semibold">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Man hrs./minutes lost thru tardiness/undertime</span>
                        <span class="text-white font-semibold">0</span>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-sm font-semibold text-white">
                    <span>Confirmed:</span>
                    <span class="text-slate-500">Stage II</span>
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div class="space-y-1 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Supervisor</p>
                        <p class="text-sm font-semibold text-white">Carlo D. Beray</p>
                        <p class="text-[0.6rem] text-slate-500">Signature over printed name</p>
                    </div>
                    <div class="space-y-1 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Employee</p>
                        <p class="text-sm font-semibold text-white">Ramon Reyes</p>
                        <p class="text-[0.6rem] text-slate-500">Signature over printed name</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
