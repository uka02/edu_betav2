<?php

namespace App\Notifications;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LessonDeletedByAdminNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Lesson $lesson,
        private readonly User $admin,
        private readonly string $reason,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'lesson_id' => $this->lesson->id,
            'lesson_title' => $this->lesson->title,
            'admin_name' => $this->admin->name,
            'reason' => $this->reason,
            'deleted_at' => now()->toIso8601String(),
        ];
    }
}
