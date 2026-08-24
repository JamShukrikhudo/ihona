<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Core\Models\Agency;
use Livewire\Component;

final class AgencyList extends Component
{
    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $agencies = $teamId === null ? collect() : Agency::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-core-livewire::agency-list', ['agencies' => $agencies]);
    }
}
