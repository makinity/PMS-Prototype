@extends('layouts.employee')

@section('main-content')
    @php
        $mporMonthYear = 'January 2026';
        $mporStatusKey = 'mpor_status_' . (auth()->id() ?? 'guest') . '_' . \Illuminate\Support\Str::slug($mporMonthYear, '_');
        $mporStatus = (string) data_get(session($mporStatusKey, []), 'status', 'draft');
        $isMporLocked = in_array($mporStatus, ['submitted', 'endorsed'], true);
    @endphp

    <section class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-200">
                {{ session('info') }}
            </div>
        @endif

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

            <div class="flex w-full flex-row gap-2 md:w-auto md:items-center">
                @if (! $isMporLocked)
                    <button type="button" data-modal-target="mporSubmitConfirmModal" data-modal-toggle="mporSubmitConfirmModal"
                        class="flex-1 rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-center text-xs font-semibold text-white transition hover:bg-slate-700 md:flex-none">
                        Submit MPOR
                    </button>
                @else
                    <button type="button" disabled
                        class="flex-1 cursor-not-allowed rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-center text-xs font-semibold text-emerald-200 opacity-80 md:flex-none">
                        Submitted
                    </button>
                @endif
                <a href="{{ route('employee.mpor.export.excel') }}"
                    class="flex-1 rounded-lg border border-slate-700 px-4 py-2 text-center text-xs text-slate-300 hover:bg-slate-800 md:flex-none">
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

        <div id="mporSubmitConfirmModal" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                    <div class="flex items-start justify-between border-b border-slate-800 p-5">
                        <h3 class="text-lg font-semibold text-white">Submit MPOR</h3>
                        <button type="button" data-modal-hide="mporSubmitConfirmModal"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                            <span class="sr-only">Close modal</span>
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div class="space-y-3 p-5 text-sm text-slate-300">
                        <p>
                            You are about to submit your Monthly Performance Output Report (MPOR) for January 2026.
                        </p>
                        <p>
                            Once submitted, this report will be locked and forwarded for supervisor/department review.
                        </p>
                        <p class="font-medium text-white">Proceed?</p>
                    </div>

                    <form id="mporSubmitForm" method="POST" action="{{ route('employee.mpor.submit') }}"
                        class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                        @csrf
                        <input type="hidden" name="month_year" value="January 2026">

                        <button type="button" data-modal-hide="mporSubmitConfirmModal"
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                            Cancel
                        </button>

                        <button type="submit" id="mporProceedSubmissionBtn"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                            <span data-button-spinner
                                class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                            <span data-button-label>Proceed Submission</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submitForm = document.getElementById('mporSubmitForm');
            const proceedButton = document.getElementById('mporProceedSubmissionBtn');

            if (!submitForm || !proceedButton) {
                return;
            }

            submitForm.addEventListener('submit', function() {
                const label = proceedButton.querySelector('[data-button-label]');
                const spinner = proceedButton.querySelector('[data-button-spinner]');

                proceedButton.disabled = true;
                proceedButton.classList.add('cursor-not-allowed', 'opacity-80');

                if (label) {
                    label.textContent = 'Submitting...';
                }

                if (spinner) {
                    spinner.classList.remove('hidden');
                }
            });
        });
    </script>
@endpush
