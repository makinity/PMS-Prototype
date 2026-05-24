@forelse ($mpors as $mpor)
    <tr class="border-b border-gray-700/50 transition hover:bg-slate-800/30">
        <td class="px-4 py-3">
            <div class="flex items-center gap-3">
                @if ($mpor->employee?->profile_photo_url)
                    <img src="{{ $mpor->employee->profile_photo_url }}" alt="{{ $mpor->employee->name }}"
                        class="h-9 w-9 shrink-0 rounded-full object-cover ring-2 ring-slate-700">
                @else
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-xs font-bold text-emerald-300 ring-2 ring-slate-700">
                        {{ $mpor->employee?->initials ?? '?' }}
                    </span>
                @endif
                <div class="min-w-0">
                    <p class="truncate font-semibold text-white">{{ $mpor->employee?->name ?? 'â€”' }}</p>
                    <p class="truncate text-xs text-slate-400">{{ $mpor->employee?->position ?? 'Employee' }}</p>
                </div>
            </div>
        </td>
        <td class="px-4 py-3 text-center">
            @php
                $statusKey = strtolower((string) ($mpor->status ?? ''));
                $badgeClass = match ($statusKey) {
                    'submitted' => 'border-blue-500/30 bg-blue-500/10 text-blue-200',
                    'approved' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200',
                    'endorsed' => 'border-violet-400/30 bg-violet-400/10 text-violet-200',
                    default => 'border-slate-700 bg-slate-800 text-slate-200',
                };
            @endphp
            <span class="inline-block rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">{{ strtoupper($statusKey ?: 'â€”') }}</span>
        </td>
        <td class="px-4 py-3 text-right">
            <a href="{{ route('supervisor.mpor.show', ['mpor' => $mpor->id]) }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-700 bg-slate-800 text-slate-300 transition hover:bg-slate-700 hover:text-white"
                title="View MPOR" aria-label="View MPOR">
                <i class="fa-regular fa-eye text-sm"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="px-4 py-12 text-center text-slate-500">
            <div class="flex flex-col items-center gap-2">
                <i class="fa-regular fa-folder-open text-2xl text-slate-600"></i>
                <p>No MPOR records found.</p>
            </div>
        </td>
    </tr>
@endforelse
