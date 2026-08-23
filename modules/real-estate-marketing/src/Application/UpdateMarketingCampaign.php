<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Marketing\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Marketing\Models\MarketingCampaign;

final class UpdateMarketingCampaign
{
    public function handle(MarketingCampaign $campaign, int|string $teamId, array $attributes): MarketingCampaign
    {
        if ((string) $campaign->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['campaign' => 'The campaign does not belong to this team.']);
        }$data = $attributes;
        if (array_key_exists('name', $data) && trim((string) $data['name']) === '') {
            throw ValidationException::withMessages(['name' => 'A campaign name is required.']);
        }$campaign->fill($data)->save();

        return $campaign->refresh();
    }
}
