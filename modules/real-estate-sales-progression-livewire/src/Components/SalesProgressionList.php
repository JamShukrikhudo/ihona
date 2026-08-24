<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionLivewire\Components;

use Liberu\RealEstate\SalesProgression\Models\SalesProgression;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class SalesProgressionList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function render(): mixed
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $progressions = SalesProgression::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('subject', 'like', '%'.$this->search.'%'))->latest()->paginate(25);

        return view('real-estate-sales-progression-livewire::sales-progression-list', ['progressions' => $progressions]);
    }
}
