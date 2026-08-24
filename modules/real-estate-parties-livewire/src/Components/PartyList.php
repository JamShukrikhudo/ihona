<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Parties\Models\Party;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class PartyList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $parties = $teamId === null
            ? collect()
            : Party::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-parties-livewire::party-list', ['parties' => $parties]);
    }
}
