<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Component;

final class PropertyComparison extends Component
{
    /** @var list<int|string> */
    public array $propertyIds = [];

    /** @var list<array<string, mixed>> */
    public array $properties = [];

    /** @var list<array<string, mixed>> */
    public array $searchResults = [];

    public string $searchTerm = '';

    /** @var list<string> */
    public array $comparisonFields = [
        'price', 'address', 'bedrooms', 'bathrooms', 'area_sqft',
        'year_built', 'property_type', 'status',
    ];

    public function mount(string|array|null $propertyIds = null): void
    {
        $this->propertyIds = $this->normalizeIds($propertyIds);
        $this->loadProperties();
    }

    public function updatedSearchTerm(): void
    {
        $this->searchProperties();
    }

    public function addProperty(int|string $propertyId): void
    {
        $propertyId = (string) $propertyId;

        if (count($this->propertyIds) >= 4 || in_array($propertyId, array_map('strval', $this->propertyIds), true)) {
            return;
        }

        $this->propertyForTeam($propertyId);
        $this->propertyIds[] = $propertyId;
        $this->loadProperties();
        $this->searchProperties();
    }

    public function removeProperty(int|string $propertyId): void
    {
        $propertyId = (string) $propertyId;
        $this->propertyIds = array_values(array_filter(
            $this->propertyIds,
            static fn (int|string $id): bool => (string) $id !== $propertyId,
        ));
        $this->loadProperties();
        $this->searchProperties();
    }

    public function searchProperties(): void
    {
        $term = trim($this->searchTerm);
        if (mb_strlen($term) < 3) {
            $this->searchResults = [];

            return;
        }

        $excluded = array_map('strval', $this->propertyIds);
        $this->searchResults = Property::query()
            ->forTeam($this->teamId())
            ->search($term)
            ->when($excluded !== [], fn ($query) => $query->whereNotIn('id', $excluded))
            ->limit(5)
            ->get()
            ->map(fn (Property $property): array => $property->comparisonData())
            ->all();
    }

    public function render(): View
    {
        return view('real-estate-properties-livewire::property-comparison');
    }

    /** @return list<int|string> */
    private function normalizeIds(string|array|null $propertyIds): array
    {
        $ids = is_string($propertyIds) ? explode(',', $propertyIds) : ($propertyIds ?? []);

        return array_values(array_unique(array_filter(
            array_map(static fn (mixed $id): string => trim((string) $id), $ids),
            static fn (string $id): bool => $id !== '',
        )));
    }

    private function loadProperties(): void
    {
        $this->properties = Property::query()
            ->forTeam($this->teamId())
            ->whereIn('id', $this->propertyIds)
            ->get()
            ->sortBy(fn (Property $property): int => array_search((string) $property->getKey(), array_map('strval', $this->propertyIds), true))
            ->values()
            ->map(fn (Property $property): array => $property->comparisonData())
            ->all();
    }

    private function propertyForTeam(string $propertyId): Property
    {
        return Property::query()->forTeam($this->teamId())->findOrFail($propertyId);
    }

    private function teamId(): int|string
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return $teamId;
    }
}
