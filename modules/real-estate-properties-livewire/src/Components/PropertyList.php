<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Properties\Application\RecordPropertyKey;
use Liberu\RealEstate\Properties\Application\TransitionProperty;
use Liberu\RealEstate\Properties\Application\TogglePropertyFavorite;
use Liberu\RealEstate\Properties\Application\UpsertPropertyUnit;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

final class PropertyList extends Component
{
    use WithPagination;

    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public ?string $postalCode = null;

    public bool $needsSyncingOnly = false;

    public ?string $propertyType = null;

    public ?float $minPrice = null;

    public ?float $maxPrice = null;

    public ?int $minBedrooms = null;

    public ?int $maxBedrooms = null;

    public bool $featuredOnly = false;

    public ?int $minEnergyScore = null;

    public ?int $minWalkabilityScore = null;

    public ?int $minTransitScore = null;

    public ?int $minBikeScore = null;

    /** @var array<int, array<string, mixed>> */
    public array $similarProperties = [];

    public function showSimilar(int $propertyId): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        $property = Property::query()->forTeam($user->current_team_id)->findOrFail($propertyId);
        $this->similarProperties = $property->similarProperties()->map(fn (Property $similar): array => [
            'id' => $similar->getKey(),
            'title' => $similar->title ?: $similar->address,
        ])->all();
    }

    public function toggleFavorite(int $propertyId, TogglePropertyFavorite $toggle): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        $toggle->handle($user->current_team_id, $user->getAuthIdentifier(), $propertyId);
    }

    public ?string $error = null;

    /** @param array<string, mixed> $attributes */
    public function saveUnit(int $propertyId, array $attributes, UpsertPropertyUnit $upsert): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        $property = Property::query()->forTeam($user->current_team_id)->findOrFail($propertyId);
        $upsert->handle($property, $user->current_team_id, $attributes);
    }

    /** @param array<string, mixed> $attributes */
    public function recordKey(int $propertyId, array $attributes, RecordPropertyKey $record): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        $property = Property::query()->forTeam($user->current_team_id)->findOrFail($propertyId);
        $record->handle($property, $user->current_team_id, $attributes);
    }

    public function publish(int $propertyId, TransitionProperty $transition): void
    {
        $this->transition($propertyId, PropertyStatus::Available, $transition);
    }

    public function markUnderOffer(int $propertyId, TransitionProperty $transition): void
    {
        $this->transition($propertyId, PropertyStatus::UnderOffer, $transition);
    }

    public function markSold(int $propertyId, TransitionProperty $transition): void
    {
        $this->transition($propertyId, PropertyStatus::Sold, $transition);
    }

    public function withdraw(int $propertyId, TransitionProperty $transition): void
    {
        $this->transition($propertyId, PropertyStatus::Withdrawn, $transition);
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $propertyTypes = Property::TYPES;
        $properties = $teamId === null
            ? collect()
            : Property::query()->forTeam($teamId)
                ->search($this->search)
                ->postalCode($this->postalCode)
                ->when($this->needsSyncingOnly, fn ($query) => $query->needsSyncing())
                ->priceRange($this->minPrice, $this->maxPrice)
                ->bedrooms($this->minBedrooms, $this->maxBedrooms)
                ->propertyType($this->propertyType)
                ->minEnergyScore($this->minEnergyScore)
                ->walkabilityScore($this->minWalkabilityScore)
                ->transitScore($this->minTransitScore)
                ->bikeScore($this->minBikeScore)
                ->when($this->featuredOnly, fn ($query) => $query->featured())
                ->latest()->paginate(20);

        return view('real-estate-properties-livewire::property-list', ['properties' => $properties, 'propertyTypes' => $propertyTypes]);
    }

    private function transition(int $propertyId, PropertyStatus $status, TransitionProperty $transition): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        try {
            $transition->handle($user->current_team_id, $user->getAuthIdentifier(), $propertyId, $status);
            $this->error = null;
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
        }
    }
}
