<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Properties\Application\RemovePropertyFavorite;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

final class WishlistManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'created_at';

    public ?string $removed = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
    ];

    public function updatingSearch(): void
    {
        $this->removed = null;
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->removed = null;
        $this->resetPage();
    }

    public function removeFavorite(int $propertyId, RemovePropertyFavorite $remove): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        if ($remove->handle($user->current_team_id, $user->getAuthIdentifier(), $propertyId)) {
            $this->removed = __('Removed from your shortlist.');
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $user = auth()->user();
        $properties = collect();
        $totalFavorites = 0;

        if ($user?->current_team_id !== null) {
            $query = Property::query()
                ->forTeam($user->current_team_id)
                ->favoritedBy($user->current_team_id, $user->getAuthIdentifier())
                ->search($this->search)
                ->when(in_array($this->sortBy, Property::SORTABLE_COLUMNS, true), fn ($query) => $query->orderBy($this->sortBy, $this->sortBy === 'created_at' ? 'desc' : 'asc'));
            $properties = $query->paginate(12);
            $totalFavorites = Property::query()->forTeam($user->current_team_id)->favoritedBy($user->current_team_id, $user->getAuthIdentifier())->count();
        }

        return view('real-estate-properties-livewire::wishlist-manager', compact('properties', 'totalFavorites'));
    }
}
