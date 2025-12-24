<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $open = false;
    public array $notifications = [];

    public function mount(): void
    {
        $this->notifications = [
            [
                'title' => 'Missing Output',
                'body' => 'Client Form Review is due today.',
                'time' => 'Just now',
                'type' => 'alert',
                'is_read' => false,
            ],
            [
                'title' => 'SMPOR Deadline',
                'body' => 'Cutoff in 3 days. Prepare your submission.',
                'time' => '1h ago',
                'type' => 'info',
                'is_read' => false,
            ],
            [
                'title' => 'Performance Review',
                'body' => 'Monthly review scheduled for next week.',
                'time' => 'Yesterday',
                'type' => 'success',
                'is_read' => true,
            ],
        ];
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
        foreach ($this->notifications as $index => $notification) {
            $notification['is_read'] = true;
            $this->notifications[$index] = $notification;
        }
    }

    public function markRead(int $index): void
    {
        if (! isset($this->notifications[$index])) {
            return;
        }

        $notification = $this->notifications[$index];
        $notification['is_read'] = true;
        $this->notifications[$index] = $notification;
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

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
