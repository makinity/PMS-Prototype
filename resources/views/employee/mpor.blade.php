@extends('layouts.employee')

@section('main-content')
    <section class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Monthly Performance Output Report</p>
                <h1 class="mt-1 text-2xl font-bold text-white md:text-3xl">MONTHLY PERFORMANCE OUTPUT REPORT</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Locked Stage II data &mdash; supervisor-rated ORS entries mapped to MPOR rows.
                </p>

                <div class="mt-4 grid gap-3 text-xs uppercase tracking-[0.3em] text-white sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-3">
                        <p class="text-slate-400">NAME</p>
                        <p class="mt-1 font-semibold normal-case tracking-normal">Ramon Reyes</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-3">
                        <p class="text-slate-400">OFFICE / DIVISION</p>
                        <p class="mt-1 font-semibold normal-case tracking-normal">Revenue Collection Unit</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-3">
                        <p class="text-slate-400">MONTH</p>
                        <p class="mt-1 font-semibold normal-case tracking-normal">January 2026</p>
                    </div>
                </div>
            </div>

            <div class="flex w-full flex-col gap-2 md:w-auto md:items-end">
                <a href="{{ route('employee.mpor.export.pdf') }}"
                    class="w-full rounded-lg border border-slate-700 px-4 py-2 text-center text-xs text-slate-300 hover:bg-slate-800 md:w-auto">
                    Export PDF
                </a>
            </div>
        </div>

        {{-- MOBILE VIEW (cards) --}}
        <div class="space-y-4 md:hidden">
            {{-- Core --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-400">Core Functions (80%)</p>

                {{-- Row 1 --}}
                <div class="mt-3 rounded-xl border border-slate-800 bg-slate-900/50 p-3">
                    <p class="text-sm font-semibold text-white">Processing of Over-the-Counter Revenue Transactions</p>

                    <div class="mt-3 grid gap-3">
                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Efficiency / Quantity</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div>
                                    <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p>
                                    <p class="text-sm font-semibold text-white">12</p>
                                </div>
                                <div>
                                    <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p>
                                    <p class="text-sm font-semibold text-white">0</p>
                                </div>
                                <div>
                                    <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p>
                                    <p class="text-sm font-semibold text-white">0</p>
                                </div>
                                <div>
                                    <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p>
                                    <p class="text-sm font-semibold text-white">0</p>
                                </div>
                                <div>
                                    <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p>
                                    <p class="text-sm font-semibold text-white">12</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Quality / Effectiveness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">60</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">60</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Timeliness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">60</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">60</p></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Row 2 --}}
                <div class="mt-3 rounded-xl border border-slate-800 bg-slate-900/50 p-3">
                    <p class="text-sm font-semibold text-white">E-Bank Scanning and Encoding of Revenue Transactions</p>

                    <div class="mt-3 grid gap-3">
                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Efficiency / Quantity</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">1</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">1</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Quality / Effectiveness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">5</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">5</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Timeliness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">5</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">5</p></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOTAL CORE --}}
                <div class="mt-4 rounded-xl border border-slate-700 bg-slate-900/60 p-3">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-semibold text-white">TOTAL — CORE</p>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Totals</p>
                    </div>
                    <div class="mt-3 grid gap-3">
                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Efficiency / Quantity</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">13</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">13</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Quality / Effectiveness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">65</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">65</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Timeliness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">65</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">65</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Support --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-400">Support Functions (20%)</p>

                <div class="mt-3 rounded-xl border border-slate-800 bg-slate-900/50 p-3">
                    <p class="text-sm font-semibold text-white">Maintenance of Revenue Records Filing System</p>

                    <div class="mt-3 grid gap-3">
                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Efficiency / Quantity</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">0</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Quality / Effectiveness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">0</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Timeliness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">0</p></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOTAL SUPPORT --}}
                <div class="mt-4 rounded-xl border border-slate-700 bg-slate-900/60 p-3">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-semibold text-white">TOTAL — SUPPORT</p>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Totals</p>
                    </div>

                    <div class="mt-3 grid gap-3">
                        @php($zero = '0')
                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Efficiency / Quantity</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Quality / Effectiveness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Timeliness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">{{ $zero }}</p></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GRAND TOTAL --}}
                <div class="mt-4 rounded-xl border border-dashed border-slate-700 bg-slate-900/40 p-3">
                    <p class="text-sm font-semibold text-white">GRAND TOTAL</p>
                    <div class="mt-3 grid gap-3">
                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Efficiency / Quantity</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">13</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">13</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Quality / Effectiveness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">65</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">65</p></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                            <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">Timeliness</p>
                            <div class="mt-2 grid grid-cols-5 gap-2 text-center">
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W1</p><p class="text-sm font-semibold text-white">65</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W2</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W3</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W4</p><p class="text-sm font-semibold text-white">0</p></div>
                                <div><p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p><p class="text-sm font-semibold text-white">65</p></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- DESKTOP/TABLET VIEW (table) --}}
        <div class="hidden md:block">
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
                            <td class="px-2 py-1">12</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">12</td>
                            <td class="px-2 py-1 border-l border-slate-800">60</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">60</td>
                            <td class="px-2 py-1 border-l border-slate-800">60</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">60</td>
                        </tr>

                        <tr class="border-t border-slate-800 text-slate-200">
                            <td class="px-3 py-2 font-medium">E-Bank Scanning and Encoding of Revenue Transactions</td>
                            <td class="px-2 py-1">1</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">1</td>
                            <td class="px-2 py-1 border-l border-slate-800">5</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">5</td>
                            <td class="px-2 py-1 border-l border-slate-800">5</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">5</td>
                        </tr>

                        <tr class="border-t border-slate-700 bg-slate-900/60 font-semibold text-slate-100">
                            <td class="px-3 py-2">TOTAL — CORE</td>
                            <td class="px-2 py-1">13</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">13</td>
                            <td class="px-2 py-1 border-l border-slate-800">65</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">65</td>
                            <td class="px-2 py-1 border-l border-slate-800">65</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">65</td>
                        </tr>

                        <tr class="bg-slate-800/40 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                            <td class="px-3 py-2 font-semibold text-slate-200" colspan="16">Support Functions (20%)</td>
                        </tr>

                        <tr class="border-t border-slate-800 text-slate-200">
                            <td class="px-3 py-2 font-medium">Maintenance of Revenue Records Filing System</td>
                            <td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td>
                            <td class="px-2 py-1 border-l border-slate-800">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td>
                            <td class="px-2 py-1 border-l border-slate-800">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td>
                        </tr>

                        <tr class="border-t border-slate-700 bg-slate-900/60 font-semibold text-slate-100">
                            <td class="px-3 py-2">TOTAL — SUPPORT</td>
                            <td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td>
                            <td class="px-2 py-1 border-l border-slate-800">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td>
                            <td class="px-2 py-1 border-l border-slate-800">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td>
                        </tr>

                        <tr class="border-t border-dashed border-slate-700 text-slate-300">
                            <td class="px-3 py-2 font-semibold">GRAND TOTAL</td>
                            <td class="px-2 py-1">13</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">13</td>
                            <td class="px-2 py-1 border-l border-slate-800">65</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">65</td>
                            <td class="px-2 py-1 border-l border-slate-800">65</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">0</td><td class="px-2 py-1">65</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-400">
            Stage II demo: MPOR points = Quantity &times; Supervisor Rating (Q/T). Batch quantities are treated as single units.
        </p>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                    <span>Week 1</span><span>Week 2</span><span>Week 3</span><span>Week 4</span><span>Total</span>
                </div>
                <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                    <span>13</span><span>0</span><span>0</span><span>0</span><span>13</span>
                </div>
                <div class="mt-3 space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Man day(s) lost thru absence</span>
                        <span class="shrink-0 font-semibold text-white">0</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Man hrs./minutes lost thru tardiness/undertime</span>
                        <span class="shrink-0 font-semibold text-white">0</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-sm font-semibold text-white">
                    <span>Confirmed:</span>
                    <span class="text-slate-500">Stage II</span>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Supervisor</p>
                        <p class="text-sm font-semibold text-white normal-case tracking-normal">Carlo D. Beray</p>
                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                    </div>
                    <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Employee</p>
                        <p class="text-sm font-semibold text-white normal-case tracking-normal">Ramon Reyes</p>
                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
