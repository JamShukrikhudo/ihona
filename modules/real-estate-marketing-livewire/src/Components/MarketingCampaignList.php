<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingLivewire\Components;

use Liberu\RealEstate\Marketing\Models\MarketingCampaign;
use Livewire\Component;

final class MarketingCampaignList extends Component
{
    public string $search = '';

    public function render(): mixed
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $campaigns = MarketingCampaign::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))->latest()->paginate(25);

        return view('real-estate-marketing-livewire::marketing-campaign-list', ['campaigns' => $campaigns]);
    }
}
