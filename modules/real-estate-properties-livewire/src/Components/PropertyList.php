<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Component;

final class PropertyList extends Component
{
    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $properties = $teamId === null
            ? collect()
            : Property::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('address', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-properties-livewire::property-list', ['properties' => $properties]);
    }
}
