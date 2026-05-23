<?php

namespace App\Notifications;

use App\Models\OrsEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public OrsEntry $entry, public string $supervisorName) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Task Reminder',
            'body' => "Your supervisor ({$this->supervisorName}) is reminding you to submit your task for {$this->entry->work_date}.",
            'ors_entry_id' => $this->entry->id,
            'type' => 'alert',
            'url' => route('employee.my-task', ['task_id' => $this->entry->id]),
        ];
    }
}
