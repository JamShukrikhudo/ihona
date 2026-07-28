<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AutomationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly ?string $body = null,
        private readonly array $data = [],
        private readonly string $type = 'automation',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
