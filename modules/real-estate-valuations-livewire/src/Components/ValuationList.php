<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Valuations\Models\Valuation;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ValuationList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $valuations = $teamId === null ? collect() : Valuation::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('subject', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-valuations-livewire::valuation-list', ['valuations' => $valuations]);
    }
}
