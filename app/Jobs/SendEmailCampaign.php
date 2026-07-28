<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\EmailCampaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendEmailCampaign implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly EmailCampaign $campaign) {}

    public function handle(): void
    {
        $campaign = $this->campaign->fresh();
        if (! $campaign || ! in_array($campaign->status, ['scheduled', 'sending'], true)) {
            return;
        }

        $campaign->update(['status' => 'sending']);
        $delivered = 0;

        $this->audience($campaign)->chunkById(100, function ($contacts) use ($campaign, &$delivered) {
            foreach ($contacts as $contact) {
                $email = collect($contact->emails)->filter()->first();
                if (! $email) {
                    continue;
                }

                try {
                    Mail::html($campaign->content, function ($message) use ($campaign, $email) {
                        $message->to($email)->subject($campaign->subject);
                    });
                    $delivered++;
                } catch (Throwable $exception) {
                    Log::warning('Campaign recipient delivery failed.', [
                        'campaign_id' => $campaign->id,
                        'contact_id' => $contact->id,
                        'exception' => $exception->getMessage(),
                    ]);
                }
            }
        });

        $campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
            'delivered_count' => $delivered,
        ]);
    }

    private function audience(EmailCampaign $campaign): Builder
    {
        $filters = $campaign->audience_filters ?? [];

        return Contact::query()
            ->where('team_id', $campaign->team_id)
            ->where('status', '!=', 'inactive')
            ->whereNotNull('emails')
            ->when($filters['types'] ?? null, fn (Builder $query, array $types) => $query->whereIn('type', $types))
            ->when($filters['tags'] ?? null, function (Builder $query, array $tags) {
                $query->where(function (Builder $query) use ($tags) {
                    foreach ($tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                });
            });
    }
}
