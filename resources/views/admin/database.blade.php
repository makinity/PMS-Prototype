@extends('layouts.admin')

@section('main-content')
    @php
        $canOperate = (bool) ($status['supported'] ?? false) && (bool) ($status['mysqldump_available'] ?? false) && (bool) ($status['mysql_available'] ?? false);
    @endphp

    <section class="space-y-5 px-3 md:px-6">
        <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-5 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-white sm:text-2xl">Database</h1>
                    <p class="mt-1 text-sm text-slate-300">Create, download, restore, and delete private database backups with manual safeguards.</p>
                </div>
                <div class="rounded-xl border border-amber-400/25 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                    Restore overwrites current MySQL data. Use only when you intend to replace the active database.
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4">
                <p class="text-xs uppercase tracking-[0.28em] text-slate-400">Connection</p>
                <p class="mt-2 text-lg font-semibold text-white">{{ strtoupper($status['driver'] ?? 'unknown') }}</p>
                <p class="mt-1 text-sm text-slate-400">{{ $status['connection'] ?? 'unknown' }}</p>
            </div>
            <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4">
                <p class="text-xs uppercase tracking-[0.28em] text-slate-400">Database</p>
                <p class="mt-2 text-lg font-semibold text-white">{{ $status['database'] ?? 'unknown' }}</p>
                <p class="mt-1 text-sm text-slate-400">{{ $status['host'] ?? 'localhost' }}:{{ $status['port'] ?? '3306' }}</p>
            </div>
            <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4">
                <p class="text-xs uppercase tracking-[0.28em] text-slate-400">mysqldump</p>
                <p class="mt-2 text-lg font-semibold {{ !empty($status['mysqldump_available']) ? 'text-emerald-300' : 'text-rose-300' }}">
                    {{ !empty($status['mysqldump_available']) ? 'Available' : 'Unavailable' }}
                </p>
                <p class="mt-1 text-xs text-slate-400">{{ $status['mysqldump_message'] ?? 'Unknown' }}</p>
            </div>
            <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4">
                <p class="text-xs uppercase tracking-[0.28em] text-slate-400">mysql client</p>
                <p class="mt-2 text-lg font-semibold {{ !empty($status['mysql_available']) ? 'text-emerald-300' : 'text-rose-300' }}">
                    {{ !empty($status['mysql_available']) ? 'Available' : 'Unavailable' }}
                </p>
                <p class="mt-1 text-xs text-slate-400">{{ $status['mysql_message'] ?? 'Unknown' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-5 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Create Backup</h2>
                        <p class="mt-1 text-sm text-slate-300">Backups are stored privately under Laravel storage and never exposed publicly.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.database.backups.store') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center rounded-xl px-4 py-3 text-sm font-semibold {{ $canOperate ? 'bg-blue-600 text-white hover:bg-blue-500' : 'cursor-not-allowed bg-slate-800 text-slate-500' }}"
                            @disabled(!$canOperate)>
                            Create Backup
                        </button>
                    </form>
                </div>

                <div class="mt-5 rounded-2xl border border-white/10 bg-slate-950/80 p-4 text-sm text-slate-300">
                    <p><span class="font-semibold text-white">Backup directory:</span> {{ $status['backup_directory'] ?? 'storage/app/private/database-backups' }}</p>
                    <p class="mt-2"><span class="font-semibold text-white">Supported mode:</span> MySQL only</p>
                    @unless($canOperate)
                        <p class="mt-3 rounded-xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-rose-200">
                            Backup and restore actions are currently blocked because the active environment is not ready for MySQL dump operations.
                        </p>
                    @endunless
                </div>
            </div>

            <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-amber-100">Restore Guard</h2>
                <p class="mt-2 text-sm text-amber-50/90">
                    Every restore replaces the current database contents with the selected SQL backup. To continue, the admin must type
                    <span class="font-semibold text-white">{{ $confirmationPhrase }}</span>.
                </p>
                <ul class="mt-4 space-y-2 text-sm text-amber-50/90">
                    <li>Only system-created backup files are accepted.</li>
                    <li>Restore is blocked when the active connection is not MySQL.</li>
                    <li>Use download first if you want an extra copy before restoring.</li>
                </ul>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 shadow-sm">
            <div class="border-b border-white/10 px-5 py-4">
                <h2 class="text-lg font-semibold text-white">Stored Backups</h2>
                <p class="mt-1 text-sm text-slate-300">Private backup inventory derived from files under the local storage disk.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead class="bg-slate-950/70 text-xs uppercase tracking-[0.24em] text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Filename</th>
                            <th class="px-4 py-3 text-left">Database</th>
                            <th class="px-4 py-3 text-left">Created</th>
                            <th class="px-4 py-3 text-left">Size</th>
                            <th class="px-4 py-3 text-left">Restore Confirmation</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-slate-200">
                        @forelse ($backups as $backup)
                            <tr class="align-top hover:bg-white/5">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-white">{{ $backup['filename'] }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ strtoupper($backup['driver']) }}</div>
                                </td>
                                <td class="px-4 py-4">{{ $backup['database'] }}</td>
                                <td class="px-4 py-4">{{ $backup['created_label'] }}</td>
                                <td class="px-4 py-4">{{ $backup['size_label'] }}</td>
                                <td class="px-4 py-4">
                                    <form method="POST" action="{{ route('admin.database.backups.restore', ['backup' => $backup['filename']]) }}" class="space-y-2">
                                        @csrf
                                        <input
                                            type="text"
                                            name="restore_confirmation"
                                            placeholder="Type {{ $confirmationPhrase }}"
                                            style="background-color:#020617;color:#e2e8f0;"
                                            class="w-full rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-sm text-slate-100 outline-none focus:border-amber-400" />
                                        <button
                                            type="submit"
                                            class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold {{ $canOperate ? 'border border-amber-400/30 bg-amber-500/10 text-amber-100 hover:bg-amber-500/20' : 'cursor-not-allowed border border-white/10 bg-slate-900 text-slate-500' }}"
                                            @disabled(!$canOperate)>
                                            Restore Backup
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.database.backups.download', ['backup' => $backup['filename']]) }}"
                                            class="inline-flex items-center rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-200 hover:bg-emerald-500/20">
                                            Download
                                        </a>
                                        <form method="POST" action="{{ route('admin.database.backups.destroy', ['backup' => $backup['filename']]) }}" onsubmit="return confirm('Delete this backup file?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-xl border border-rose-400/30 bg-rose-500/10 px-3 py-2 text-xs font-semibold text-rose-200 hover:bg-rose-500/20">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">
                                    No database backups found in private storage.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
