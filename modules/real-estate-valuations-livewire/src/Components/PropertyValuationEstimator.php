<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Valuations\Application\GeneratePropertyValuation;
use Livewire\Component;

final class PropertyValuationEstimator extends Component
{
    /** @var array<string, mixed> */
    public array $property = [];

    public int $comparablesCount = 0;

    public int $trainingSamples = 0;

    /** @var array<string, mixed>|null */
    public ?array $valuation = null;

    public ?string $error = null;

    /** @param array<string, mixed> $property */
    public function mount(array $property = [], int $comparablesCount = 0, int $trainingSamples = 0): void
    {
        $this->property = $property;
        $this->comparablesCount = $comparablesCount;
        $this->trainingSamples = $trainingSamples;
    }

    public function generateValuation(GeneratePropertyValuation $generate): void
    {
        try {
            $this->valuation = $generate->handle($this->property, $this->comparablesCount, $this->trainingSamples);
            $this->error = null;
        } catch (\Throwable $exception) {
            $this->valuation = null;
            $this->error = $exception->getMessage();
        }
    }

    public function resetValuation(): void
    {
        $this->valuation = null;
        $this->error = null;
    }

    public function render(): View
    {
        return view('real-estate-valuations-livewire::property-valuation-estimator');
    }
}
