<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Marketing\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Marketing\Models\MarketingCampaign;

final class DeleteMarketingCampaign
{
    public function handle(MarketingCampaign $campaign, int|string $teamId): void
    {
        if ((string) $campaign->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['campaign' => 'The campaign does not belong to this team.']);
        }$campaign->delete();
    }
}
