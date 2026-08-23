<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveLivewire\Components;

use Liberu\RealEstate\Rightmove\Models\RightmoveSync;
use Livewire\Component;

final class RightmoveSyncList extends Component
{
    public string $search = '';

    public function render(): mixed
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $syncs = RightmoveSync::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('external_id', 'like', '%'.$this->search.'%'))->latest()->paginate(25);

        return view('real-estate-rightmove-livewire::rightmove-sync-list', ['syncs' => $syncs]);
    }
}
