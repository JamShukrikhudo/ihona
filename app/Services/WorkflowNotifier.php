<?php

namespace App\Services;

use App\Models\User;

class WorkflowNotifier
{
    public function __construct(private readonly NotificationDispatcher $dispatcher) {}

    public function notify(
        int $teamId,
        User $recipient,
        string $event,
        string $title,
        ?string $body = null,
        array $context = [],
    ): array {
        return $this->dispatcher->dispatch(
            $teamId,
            $recipient,
            $event,
            $title,
            $body,
            $context,
            NotificationDispatcher::CHANNELS,
        );
    }
}
