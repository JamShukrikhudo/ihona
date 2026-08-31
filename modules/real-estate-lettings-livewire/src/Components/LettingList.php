<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Lettings\Models\Letting;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class LettingList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function render(): View
    {
        $team = auth()->user()?->current_team_id;
        $lettings = $team === null ? collect() : Letting::query()->forTeam($team)->when($this->search !== '', fn ($q) => $q->where('subject', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-lettings-livewire::letting-list', ['lettings' => $lettings]);
    }
}
