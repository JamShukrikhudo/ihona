<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Marketing\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Marketing\Domain\MarketingCampaignStatus;
use Liberu\RealEstate\Marketing\Models\MarketingCampaign;

final class TransitionMarketingCampaign
{
    public function handle(MarketingCampaign $campaign, int|string $teamId, MarketingCampaignStatus $status): MarketingCampaign
    {
        if ((string) $campaign->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['campaign' => 'The campaign does not belong to this team.']);
        }

        $current = $campaign->status;
        $allowed = match ($current) {
            MarketingCampaignStatus::Draft => [MarketingCampaignStatus::Scheduled, MarketingCampaignStatus::Cancelled],
            MarketingCampaignStatus::Scheduled => [MarketingCampaignStatus::Active, MarketingCampaignStatus::Paused, MarketingCampaignStatus::Cancelled],
            MarketingCampaignStatus::Active => [MarketingCampaignStatus::Paused, MarketingCampaignStatus::Completed, MarketingCampaignStatus::Cancelled],
            MarketingCampaignStatus::Paused => [MarketingCampaignStatus::Active, MarketingCampaignStatus::Cancelled],
            MarketingCampaignStatus::Completed, MarketingCampaignStatus::Cancelled => [],
        };

        if (! in_array($status, $allowed, true)) {
            throw ValidationException::withMessages(['status' => "Cannot transition a {$current->value} campaign to {$status->value}."]);
        }

        $campaign->forceFill(['status' => $status])->save();

        return $campaign->refresh();
    }
}
