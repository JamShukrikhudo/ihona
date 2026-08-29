<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Marketing\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Marketing\Domain\MarketingCampaignSection;
use Liberu\RealEstate\Marketing\Models\MarketingCampaign;

final class UpdateMarketingCampaignSection
{
    /** @param array<string, mixed> $value */
    public function handle(MarketingCampaign $campaign, int|string $teamId, MarketingCampaignSection $section, array $value): MarketingCampaign
    {
        if ((string) $campaign->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['campaign' => 'The campaign does not belong to this team.']);
        }
        $campaign->forceFill([$section->value => $value])->save();

        return $campaign->refresh();
    }
}
