<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Properties\Application\TogglePropertyFavorite;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Component;

final class PropertyDetail extends Component
{
    public int|string $propertyId;

    public bool $isFavorited = false;

    public bool $showVirtualTour = false;

    public function mount(int|string $propertyId): void
    {
        $this->propertyId = $propertyId;
        $this->isFavorited = $this->property()->favorites()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function toggleFavorite(TogglePropertyFavorite $toggle): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        $this->isFavorited = $toggle->handle(
            $user->current_team_id,
            $user->getAuthIdentifier(),
            $this->property()->getKey(),
        );
    }

    public function requestViewing(): void
    {
        $this->dispatch('property-viewing-requested', propertyId: $this->property()->getKey());
    }

    public function toggleVirtualTour(): void
    {
        $this->showVirtualTour = ! $this->showVirtualTour;
    }

    public function render(): View
    {
        $property = $this->property();

        return view('real-estate-properties-livewire::property-detail', [
            'property' => $property,
            'facts' => $property->disclosureFacts(),
        ]);
    }

    private function property(): Property
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return Property::query()->forTeam($teamId)->findOrFail($this->propertyId);
    }
}
