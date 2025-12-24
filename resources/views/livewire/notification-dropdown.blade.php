<div class="relative" wire:keydown.escape.window="close">
    <button type="button" wire:click="toggle" aria-expanded="{{ $open ? 'true' : 'false' }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-900/70 text-slate-300 shadow-sm transition hover:bg-slate-800">
        <span class="sr-only">View notifications</span>
        <i class="fa-regular fa-bell"></i>
        @if ($this->unreadCount)
            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-rose-500"></span>
        @endif
    </button>

    <div class="{{ $open ? '' : 'hidden' }} absolute right-0 mt-3 w-80 rounded-2xl border border-slate-800 bg-slate-900/95 shadow-xl shadow-slate-950/40" wire:click.outside="close">
        <div class="flex items-center justify-between px-4 py-3">
            <div>
                <p class="text-sm font-semibold text-white">Notifications</p>
                <p class="text-xs text-slate-400">{{ $this->unreadCount }} unread</p>
            </div>
            <button type="button" wire:click="markAllRead" class="text-xs font-semibold text-emerald-300 transition hover:text-emerald-200">
                Mark all
            </button>
        </div>

        <div class="max-h-80 overflow-y-auto px-2 pb-2">
            @forelse ($notifications as $index => $notification)
                @php
                    $type = $notification['type'] ?? 'info';
                    $icon = match ($type) {
                        'alert' => 'fa-solid fa-triangle-exclamation',
                        'success' => 'fa-solid fa-circle-check',
                        'info' => 'fa-regular fa-bell',
                        default => 'fa-regular fa-bell',
                    };
                    $accent = match ($type) {
                        'alert' => 'text-rose-300 bg-rose-500/10',
                        'success' => 'text-emerald-300 bg-emerald-500/10',
                        'info' => 'text-sky-300 bg-sky-500/10',
                        default => 'text-slate-300 bg-slate-500/10',
                    };
                @endphp
                <button type="button" wire:click="markRead({{ $index }})" class="flex w-full items-start gap-3 rounded-xl px-3 py-3 text-left transition hover:bg-slate-800/70">
                    <span class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full {{ $accent }}">
                        <i class="{{ $icon }}"></i>
                    </span>
                    <span class="flex-1">
                        <span class="flex items-center justify-between gap-3">
                            <span class="text-sm font-semibold text-white">{{ $notification['title'] }}</span>
                            <span class="text-[11px] text-slate-400">{{ $notification['time'] }}</span>
                        </span>
                        <span class="mt-1 block text-xs text-slate-400">{{ $notification['body'] }}</span>
                    </span>
                    @if (empty($notification['is_read']))
                        <span class="mt-2 h-2 w-2 rounded-full bg-emerald-400"></span>
                    @endif
                </button>
            @empty
                <div class="px-3 py-6 text-center text-xs text-slate-400">
                    You're all caught up.
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-800 px-4 py-3">
            <a href="#" class="text-xs font-semibold text-slate-300 transition hover:text-white">View all notifications</a>
        </div>
    </div>
</div>
