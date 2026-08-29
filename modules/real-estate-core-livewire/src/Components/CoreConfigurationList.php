<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Core\Models\StatusDefinition;
use Liberu\RealEstate\Core\Models\Terminology;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class CoreConfigurationList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $terminology = $teamId === null ? collect() : Terminology::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('key', 'like', '%'.$this->search.'%'))->latest()->get();
        $statuses = $teamId === null ? collect() : StatusDefinition::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('entity', 'like', '%'.$this->search.'%')->orWhere('key', 'like', '%'.$this->search.'%'))->latest()->get();

        return view('real-estate-core-livewire::core-configuration-list', compact('terminology', 'statuses'));
    }
}
