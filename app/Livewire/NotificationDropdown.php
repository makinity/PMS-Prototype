<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $open = false;
    public array $notifications = [];
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
        if ($this->open) {
            $this->loadNotifications();
        }
    }

    public function close(): void
    {
        $this->open = false;
    }

    #[On('pms-notification-received')]
    public function refreshNotifications(): void
    {
        $this->loadNotifications();
    }

    public function markAllRead(): void
    {
        $user = auth()->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        $this->loadNotifications();
    }

    public function markRead(int $index): void
    {
        $user = auth()->user();
        if (!$user) return;

        $dbNotifications = $user->notifications()->latest()->take(10)->get();
        if (isset($dbNotifications[$index])) {
            $dbNotifications[$index]->markAsRead();
        }
        $this->loadNotifications();
    }

    private function loadNotifications(): void
    {
        $user = auth()->user();
        if (!$user) {
            $this->notifications = [];
            $this->unreadCount = 0;
            return;
        }

        $this->notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'title' => $n->data['title'] ?? 'Notification',
                'body' => $n->data['body'] ?? '',
                'time' => $n->created_at->diffForHumans(),
                'type' => $n->data['type'] ?? 'info',
                'is_read' => !is_null($n->read_at),
                'url' => $n->data['url'] ?? null,
            ])
            ->toArray();

        $this->unreadCount = collect($this->notifications)->where('is_read', false)->count();
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
