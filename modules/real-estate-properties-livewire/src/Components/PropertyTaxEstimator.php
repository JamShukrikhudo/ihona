<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Properties\Application\EstimatePropertyTax;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Component;

final class PropertyTaxEstimator extends Component
{
    public int|string $propertyId;

    public string $buyerType = 'home_mover';

    public string $country = 'GB';

    /** @var array<string, mixed>|null */
    public ?array $estimate = null;

    public function mount(int|string $propertyId): void
    {
        $this->propertyId = $propertyId;
        $this->country = (string) (Property::query()->forTeam($this->teamId())->findOrFail($propertyId)->country ?: 'GB');
    }

    public function calculateTax(EstimatePropertyTax $estimate): void
    {
        $property = $this->property();

        $this->estimate = $estimate->handle((float) $property->price, $this->country, [
            'buyer_type' => $this->buyerType,
        ]);
    }

    public function resetCalculation(): void
    {
        $this->estimate = null;
        $this->buyerType = 'home_mover';
    }

    public function render(): View
    {
        return view('real-estate-properties-livewire::property-tax-estimator', ['property' => $this->property()]);
    }

    private function property(): Property
    {
        return Property::query()->forTeam($this->teamId())->findOrFail($this->propertyId);
    }

    private function teamId(): int|string
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return $teamId;
    }
}
