<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Core\Models\Branch;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class BranchList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $branches = $teamId === null
            ? collect()
            : Branch::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-core-livewire::branch-list', ['branches' => $branches]);
    }
}
