<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $open = false;
    public array $notifications = [];

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
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

    public function getUnreadCountProperty(): int
    {
        $count = 0;
        foreach ($this->notifications as $notification) {
            if (empty($notification['is_read'])) {
                $count++;
            }
        }
        return $count;
    }

    private function loadNotifications(): void
    {
        $user = auth()->user();
        if (!$user) {
            $this->notifications = [];
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
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
