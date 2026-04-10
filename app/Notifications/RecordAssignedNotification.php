<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecordAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $recordType,
        protected int $recordId,
        protected string $title,
        protected string $message,
        protected string $url,
        protected ?int $assignedBy = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'record_type' => $this->recordType,
            'record_id' => $this->recordId,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'assigned_by' => $this->assignedBy,
        ];
    }
}
''