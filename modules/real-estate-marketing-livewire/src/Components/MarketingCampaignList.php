<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingLivewire\Components;

use Liberu\RealEstate\Marketing\Application\TransitionMarketingCampaign;
use Liberu\RealEstate\Marketing\Application\UpdateMarketingCampaignSection;
use Liberu\RealEstate\Marketing\Domain\MarketingCampaignSection;
use Liberu\RealEstate\Marketing\Domain\MarketingCampaignStatus;
use Liberu\RealEstate\Marketing\Models\MarketingCampaign;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class MarketingCampaignList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    /** @param array<string, mixed> $value */
    public function updateSection(int $campaignId, string $section, array $value): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $campaign = MarketingCampaign::query()->forTeam($teamId)->findOrFail($campaignId);
        app(UpdateMarketingCampaignSection::class)->handle($campaign, $teamId, MarketingCampaignSection::from($section), $value);
    }

    public function transition(int $campaignId, string $status): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $campaign = MarketingCampaign::query()->forTeam($teamId)->findOrFail($campaignId);
        app(TransitionMarketingCampaign::class)->handle($campaign, $teamId, MarketingCampaignStatus::from($status));
    }

    public function render(): mixed
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $campaigns = MarketingCampaign::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))->latest()->paginate(25);

        return view('real-estate-marketing-livewire::marketing-campaign-list', ['campaigns' => $campaigns]);
    }
}
