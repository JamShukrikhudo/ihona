<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class NewPropertyMatches extends Notification
{
    use Queueable;

    public function __construct(private readonly Collection $matches)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_property_matches',
            'count' => $this->matches->count(),
            'matches' => $this->matches->take(10)->map(fn ($match) => [
                'id' => $match->id,
                'property_id' => $match->property_id,
                'match_score' => $match->match_score,
            ])->values()->all(),
        ];
    }
}
