<?php

namespace App\Services;

use App\Models\NotificationDelivery;
use App\Models\NotificationPreference;
use App\Models\ServiceIntegration;
use App\Models\User;
use App\Notifications\AutomationNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class NotificationDispatcher
{
    public const CHANNELS = ['in_app', 'email', 'sms', 'push'];

    public const EVENTS = [
        'enquiry.created',
        'offer.created',
        'offer.accepted',
        'offer.rejected',
        'offer.withdrawn',
        'viewing.requested',
        'viewing.reminder',
        'task.created',
        'task.due',
        'maintenance.created',
        'maintenance.updated',
        'tenancy.renewed',
        'tenancy.renewal_due',
        'portal.failed',
        'property.matched',
        'instruction.accepted',
        'listing.updated',
    ];

    public function dispatch(
        int $teamId,
        User $user,
        string $eventType,
        string $title,
        ?string $body,
        array $context = [],
        array $requestedChannels = ['in_app'],
    ): array {
        if (! $user->allTeams()->contains('id', $teamId)) {
            throw new RuntimeException('Notification recipient does not belong to this organisation.');
        }

        $preference = NotificationPreference::where('team_id', $teamId)
            ->where('user_id', $user->id)
            ->first();
        $enabledChannels = $preference?->channels ?? ['in_app'];
        $eventPreferences = $preference?->event_preferences ?? [];
        if (array_key_exists($eventType, $eventPreferences) && $eventPreferences[$eventType] === false) {
            $enabledChannels = [];
        }
        $channels = array_values(array_intersect(
            self::CHANNELS,
            array_unique($requestedChannels),
            $enabledChannels,
        ));
        $safeContext = Arr::except($context, ['secrets', 'credentials', 'tokens']);

        return collect($channels)->map(fn (string $channel) => $this->deliver(
            $teamId,
            $user,
            $preference,
            $eventType,
            $channel,
            $title,
            $body,
            $safeContext,
        ))->all();
    }

    private function deliver(
        int $teamId,
        User $user,
        ?NotificationPreference $preference,
        string $eventType,
        string $channel,
        string $title,
        ?string $body,
        array $context,
    ): array {
        $integration = in_array($channel, ['sms', 'push'], true)
            ? ServiceIntegration::where('team_id', $teamId)
                ->where('category', $channel)
                ->where('active', true)
                ->orderByDesc('is_default')
                ->first()
            : null;

        $delivery = NotificationDelivery::create([
            'team_id' => $teamId,
            'user_id' => $user->id,
            'event_type' => $eventType,
            'channel' => $channel,
            'status' => 'pending',
            'provider' => $integration?->provider,
            'title' => $title,
            'body' => $body,
            'context' => $context,
        ]);

        try {
            if ($channel === 'in_app') {
                $user->notify(new AutomationNotification($title, $body, $context, $eventType));
                $delivery->update(['status' => 'sent', 'sent_at' => now()]);
            } elseif ($channel === 'email') {
                Mail::html(
                    e($body ?? ''),
                    fn ($message) => $message->to($user->email)->subject($title),
                );
                $delivery->update(['status' => 'sent', 'sent_at' => now()]);
            } else {
                $this->assertAdapterReady($channel, $preference, $integration);
                $delivery->update(['status' => 'queued', 'queued_at' => now()]);
            }
        } catch (Throwable $exception) {
            $delivery->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error' => mb_substr($exception->getMessage(), 0, 2000),
            ]);
        }

        return $delivery->fresh()->only(['id', 'channel', 'status', 'provider']);
    }

    private function assertAdapterReady(
        string $channel,
        ?NotificationPreference $preference,
        ?ServiceIntegration $integration,
    ): void {
        if (! $integration) {
            throw new RuntimeException("No active {$channel} integration is configured.");
        }
        if ($channel === 'sms' && blank($preference?->phone)) {
            throw new RuntimeException('No SMS phone number is configured.');
        }
        if ($channel === 'push' && empty($preference?->push_tokens)) {
            throw new RuntimeException('No push token is configured.');
        }
    }
}
