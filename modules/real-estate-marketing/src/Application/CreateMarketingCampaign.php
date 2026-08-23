<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Marketing\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Marketing\Domain\MarketingCampaignStatus;
use Liberu\RealEstate\Marketing\Models\MarketingCampaign;

final class CreateMarketingCampaign
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): MarketingCampaign
    {
        $name = trim((string) ($attributes['name'] ?? ''));
        $channel = trim((string) ($attributes['channel'] ?? ''));
        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'A campaign name is required.']);
        }if ($channel === '') {
            throw ValidationException::withMessages(['channel' => 'A campaign channel is required.']);
        }

        return DB::transaction(fn (): MarketingCampaign => MarketingCampaign::query()->create(['team_id' => $teamId, 'created_by' => $actorId, 'property_id' => $attributes['property_id'] ?? null, 'listing_id' => $attributes['listing_id'] ?? null, 'name' => $name, 'channel' => $channel, 'status' => $attributes['status'] ?? MarketingCampaignStatus::Draft, 'audience' => $attributes['audience'] ?? [], 'content' => $attributes['content'] ?? [], 'schedule' => $attributes['schedule'] ?? [], 'metrics' => $attributes['metrics'] ?? [], 'notes' => $attributes['notes'] ?? null]));
    }
}
