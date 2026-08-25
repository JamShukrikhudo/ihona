<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaLivewire\Components;

use Liberu\RealEstate\Zoopla\Models\ZooplaSync;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ZooplaSyncList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function render(): mixed
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $syncs = ZooplaSync::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('external_id', 'like', '%'.$this->search.'%'))->latest()->paginate(25);

        return view('real-estate-zoopla-livewire::zoopla-sync-list', ['syncs' => $syncs]);
    }
}
