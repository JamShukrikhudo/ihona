<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Contact;
use App\Models\EmailCampaign;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmailCampaignController extends TenantCrudController
{
    protected string $model = EmailCampaign::class;

    protected string $routeParameter = 'email_campaign';

    protected array $searchable = ['name', 'subject'];

    protected array $filterable = ['status'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'subject' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'content' => [$record ? 'sometimes' : 'required', 'string'],
            'status' => ['sometimes', Rule::in(['draft', 'scheduled', 'sending', 'sent', 'cancelled'])],
            'scheduled_at' => ['nullable', 'date'],
            'audience_filters' => ['nullable', 'array'],
            'audience_filters.types' => ['nullable', 'array'],
            'audience_filters.types.*' => ['string', 'max:50'],
            'audience_filters.tags' => ['nullable', 'array'],
            'audience_filters.tags.*' => ['string', 'max:100'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['created_by'] = $request->user()->id;
        $attributes['status'] ??= 'draft';

        return $attributes;
    }

    public function preview(Request $request, int $emailCampaign): JsonResponse
    {
        $campaign = $this->campaign($request, $emailCampaign);
        $recipients = $this->audience($campaign)->get(['id', 'first_name', 'last_name', 'emails']);

        return response()->json([
            'data' => $recipients,
            'meta' => ['recipients_count' => $recipients->count()],
        ]);
    }

    public function schedule(Request $request, int $emailCampaign): JsonResponse
    {
        $campaign = $this->campaign($request, $emailCampaign);
        $validated = $request->validate(['scheduled_at' => ['required', 'date', 'after:now']]);
        $campaign->update([
            'status' => 'scheduled',
            'scheduled_at' => $validated['scheduled_at'],
            'recipients_count' => $this->audience($campaign)->count(),
        ]);

        return response()->json(['data' => $campaign->fresh()]);
    }

    public function cancel(Request $request, int $emailCampaign): JsonResponse
    {
        $campaign = $this->campaign($request, $emailCampaign);
        abort_if($campaign->status === 'sent', 422, 'A sent campaign cannot be cancelled.');
        $campaign->update(['status' => 'cancelled', 'scheduled_at' => null]);

        return response()->json(['data' => $campaign->fresh()]);
    }

    public function metrics(Request $request, int $emailCampaign): JsonResponse
    {
        $campaign = $this->campaign($request, $emailCampaign);
        $rate = fn (int $value, int $base) => $base > 0 ? round(($value / $base) * 100, 2) : 0;

        return response()->json(['data' => [
            'recipients' => $campaign->recipients_count,
            'delivered' => $campaign->delivered_count,
            'opened' => $campaign->opened_count,
            'clicked' => $campaign->clicked_count,
            'delivery_rate' => $rate($campaign->delivered_count, $campaign->recipients_count),
            'open_rate' => $rate($campaign->opened_count, $campaign->delivered_count),
            'click_rate' => $rate($campaign->clicked_count, $campaign->delivered_count),
        ]]);
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

    private function campaign(Request $request, int $id): EmailCampaign
    {
        return EmailCampaign::query()
            ->where('team_id', $this->teamId($request))
            ->findOrFail($id);
    }
}
