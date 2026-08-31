<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\Properties\Application\RecordPropertyKey;
use Liberu\RealEstate\Properties\Application\TogglePropertyFavorite;
use Liberu\RealEstate\Properties\Application\TransitionProperty;
use Liberu\RealEstate\Properties\Application\UpsertPropertyUnit;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

final class PropertyList extends Component
{
    use WithPagination;

    protected $queryString = [
        'search' => ['except' => ''],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'minBedrooms' => ['except' => null],
        'maxBedrooms' => ['except' => null],
        'minBathrooms' => ['except' => null],
        'maxBathrooms' => ['except' => null],
        'minArea' => ['except' => null],
        'maxArea' => ['except' => null],
        'propertyType' => ['except' => null],
        'selectedAmenities' => ['except' => []],
        'status' => ['except' => null],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'country' => ['except' => null],
        'energyRating' => ['except' => null],
        'minEnergyScore' => ['except' => null],
        'minWalkabilityScore' => ['except' => null],
        'minTransitScore' => ['except' => null],
        'minBikeScore' => ['except' => null],
        'featuredOnly' => ['except' => false],
        'needsSyncingOnly' => ['except' => false],
    ];

    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public ?string $postalCode = null;

    public bool $needsSyncingOnly = false;

    public ?string $propertyType = null;

    public ?string $status = null;

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public ?float $minPrice = null;

    public ?float $maxPrice = null;

    public ?int $minBedrooms = null;

    public ?int $maxBedrooms = null;

    public ?int $minBathrooms = null;

    public ?int $maxBathrooms = null;

    public ?float $minArea = null;

    public ?float $maxArea = null;

    public ?int $minYearBuilt = null;

    public ?int $maxYearBuilt = null;

    /** @var array<int, string> */
    public array $selectedAmenities = [];

    public ?string $country = null;

    public ?string $energyRating = null;

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
        $this->transitionProperty($propertyId, PropertyStatus::Available, $transition);
    }

    public function markUnderOffer(int $propertyId, TransitionProperty $transition): void
    {
        $this->transitionProperty($propertyId, PropertyStatus::UnderOffer, $transition);
    }

    public function markSold(int $propertyId, TransitionProperty $transition): void
    {
        $this->transitionProperty($propertyId, PropertyStatus::Sold, $transition);
    }

    public function withdraw(int $propertyId, TransitionProperty $transition): void
    {
        $this->transitionProperty($propertyId, PropertyStatus::Withdrawn, $transition);
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $propertyTypes = Property::TYPES;
        $properties = $teamId === null
            ? collect()
            : $this->buildQuery()->sorted($this->sortBy, $this->sortDirection)->paginate(20);

        return view('real-estate-properties-livewire::property-list', ['properties' => $properties, 'propertyTypes' => $propertyTypes]);
    }

    public function resultCount(): int
    {
        return $this->buildQuery()->count();
    }

    /** @return array<string, string> */
    public function appliedFilters(): array
    {
        $applied = [];

        if (filled($this->search)) {
            $applied['search'] = '“'.$this->search.'”';
        }

        foreach ([
            'propertyType' => 'Property type', 'status' => 'Status',
            'country' => 'Country', 'energyRating' => 'Energy rating',
        ] as $property => $label) {
            if (filled($this->{$property})) {
                $applied[$property] = $label.': '.$this->{$property};
            }
        }

        foreach ([
            'minPrice' => 'Minimum price', 'maxPrice' => 'Maximum price',
            'minBedrooms' => 'Minimum bedrooms', 'maxBedrooms' => 'Maximum bedrooms',
            'minBathrooms' => 'Minimum bathrooms', 'maxBathrooms' => 'Maximum bathrooms',
            'minArea' => 'Minimum area', 'maxArea' => 'Maximum area',
            'minYearBuilt' => 'Minimum year built', 'maxYearBuilt' => 'Maximum year built',
            'minEnergyScore' => 'Minimum energy score', 'minWalkabilityScore' => 'Minimum walkability score',
            'minTransitScore' => 'Minimum transit score', 'minBikeScore' => 'Minimum bike score',
        ] as $property => $label) {
            if ($this->{$property} !== null && $this->{$property} !== '') {
                $applied[$property] = $label.': '.$this->{$property};
            }
        }

        if ($this->selectedAmenities !== []) {
            $applied['selectedAmenities'] = 'Amenities: '.count($this->selectedAmenities);
        }

        if ($this->needsSyncingOnly) {
            $applied['needsSyncingOnly'] = 'Needs syncing';
        }

        if ($this->featuredOnly) {
            $applied['featuredOnly'] = 'Featured only';
        }

        return $applied;
    }

    public function clearFilter(string $filter): void
    {
        if (! array_key_exists($filter, $this->appliedFilters())) {
            return;
        }

        $this->{$filter} = match ($filter) {
            'selectedAmenities' => [],
            'needsSyncingOnly', 'featuredOnly' => false,
            default => null,
        };
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        foreach (array_keys($this->appliedFilters()) as $filter) {
            $this->clearFilter($filter);
        }
    }

    private function buildQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;

        return Property::query()->forTeam($teamId ?? 0)
            ->search($this->search)
            ->postalCode($this->postalCode)
            ->when($this->needsSyncingOnly, fn ($query) => $query->needsSyncing())
            ->priceRange($this->minPrice, $this->maxPrice)
            ->bedrooms($this->minBedrooms, $this->maxBedrooms)
            ->bathrooms($this->minBathrooms, $this->maxBathrooms)
            ->areaRange($this->minArea, $this->maxArea)
            ->yearBuiltRange($this->minYearBuilt, $this->maxYearBuilt)
            ->propertyType($this->propertyType)
            ->status($this->status)
            ->hasAmenities($this->selectedAmenities)
            ->country($this->country)
            ->energyRating($this->energyRating)
            ->minEnergyScore($this->minEnergyScore)
            ->walkabilityScore($this->minWalkabilityScore)
            ->transitScore($this->minTransitScore)
            ->bikeScore($this->minBikeScore)
            ->when($this->featuredOnly, fn (Builder $query): Builder => $query->featured());
    }

    private function transitionProperty(int $propertyId, PropertyStatus $status, TransitionProperty $transition): void
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
