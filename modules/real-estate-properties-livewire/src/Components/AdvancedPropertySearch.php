<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

final class AdvancedPropertySearch extends Component
{
    use WithPagination;

    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public ?string $postalCode = null;

    public bool $needsSyncingOnly = false;

    public ?float $minPrice = null;

    public ?float $maxPrice = null;

    public ?int $minBedrooms = null;

    public ?int $maxBedrooms = null;

    public ?int $minBathrooms = null;

    public ?int $maxBathrooms = null;

    public ?float $minArea = null;

    public ?float $maxArea = null;

    public ?string $propertyType = null;

    public ?string $country = null;

    public ?string $energyRating = null;

    public bool $featuredOnly = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function applyFilters(): void
    {
        $this->validate([
            'minPrice' => ['nullable', 'numeric', 'min:0'],
            'maxPrice' => ['nullable', 'numeric', 'min:0', 'gte:minPrice'],
            'minBedrooms' => ['nullable', 'integer', 'min:0'],
            'maxBedrooms' => ['nullable', 'integer', 'min:0', 'gte:minBedrooms'],
            'minBathrooms' => ['nullable', 'integer', 'min:0'],
            'maxBathrooms' => ['nullable', 'integer', 'min:0', 'gte:minBathrooms'],
            'minArea' => ['nullable', 'numeric', 'min:0'],
            'maxArea' => ['nullable', 'numeric', 'min:0', 'gte:minArea'],
            'propertyType' => ['nullable', 'string', 'max:40'],
            'postalCode' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'size:2'],
            'energyRating' => ['nullable', 'string', 'max:10'],
            'featuredOnly' => ['boolean'],
            'needsSyncingOnly' => ['boolean'],
        ]);

        $this->resetPage();
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $properties = $teamId === null
            ? Property::query()->whereRaw('1 = 0')->paginate(12)
            : Property::query()->forTeam($teamId)
                ->search($this->search)
                ->postalCode($this->postalCode)
                ->when($this->needsSyncingOnly, fn ($query) => $query->needsSyncing())
                ->priceRange($this->minPrice, $this->maxPrice)
                ->bedrooms($this->minBedrooms, $this->maxBedrooms)
                ->bathrooms($this->minBathrooms, $this->maxBathrooms)
                ->areaRange($this->minArea, $this->maxArea)
                ->propertyType($this->propertyType)
                ->country($this->country)
                ->energyRating($this->energyRating)
                ->when($this->featuredOnly, fn ($query) => $query->featured())
                ->latest()->paginate(12);

        return view('real-estate-properties-livewire::advanced-property-search', compact('properties'));
    }
}
