<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyCategory;
use Liberu\RealEstate\Properties\Models\PropertyTemplate;
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

    public ?int $minYearBuilt = null;

    public ?int $maxYearBuilt = null;

    public ?string $propertyType = null;

    public ?int $propertyCategoryId = null;

    public ?int $propertyTemplateId = null;

    public ?string $country = null;

    public ?string $energyRating = null;

    /** @var array<int, string> */
    public array $selectedAmenities = [];

    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?float $radius = null;

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
            'minYearBuilt' => ['nullable', ...Property::yearBuiltRules()],
            'maxYearBuilt' => ['nullable', ...Property::yearBuiltRules(), 'gte:minYearBuilt'],
            'propertyType' => ['nullable', 'string', 'max:40'],
            'propertyCategoryId' => ['nullable', 'integer', 'exists:real_estate_property_categories,id'],
            'propertyTemplateId' => ['nullable', 'integer', 'exists:real_estate_property_templates,id'],
            'postalCode' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'size:2'],
            'energyRating' => ['nullable', 'string', 'max:10'],
            'selectedAmenities' => ['array', 'max:20'],
            'selectedAmenities.*' => ['string', 'max:80'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'gt:0', 'max:500'],
            'featuredOnly' => ['boolean'],
            'needsSyncingOnly' => ['boolean'],
        ]);

        $this->resetPage();
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $categories = $teamId === null ? collect() : PropertyCategory::query()->forTeam($teamId)->orderBy('name')->get();
        $templates = $teamId === null ? collect() : PropertyTemplate::query()->forTeam($teamId)->orderBy('name')->get();
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
                ->yearBuiltRange($this->minYearBuilt, $this->maxYearBuilt)
                ->propertyType($this->propertyType)
                ->category($this->propertyCategoryId)
                ->where('property_template_id', $this->propertyTemplateId)
                ->country($this->country)
                ->energyRating($this->energyRating)
                ->hasAmenities($this->selectedAmenities)
                ->when($this->latitude !== null && $this->longitude !== null && $this->radius !== null, fn ($query) => $query->nearby($this->latitude, $this->longitude, $this->radius))
                ->when($this->featuredOnly, fn ($query) => $query->featured())
                ->latest()->paginate(12);

        return view('real-estate-properties-livewire::advanced-property-search', compact('properties', 'categories', 'templates'));
    }
}
