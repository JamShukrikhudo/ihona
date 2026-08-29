<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Valuations\Application\CalculateRentalYield;
use Livewire\Component;

final class RentalYieldCalculator extends Component
{
    public float $propertyValue = 0;
    public float $annualRentalIncome = 0;
    public float $annualExpenses = 0;
    /** @var array<string, mixed>|null */
    public ?array $result = null;
    public ?string $error = null;

    public function calculateRentalYield(CalculateRentalYield $calculate): void
    {
        try {
            $this->result = $calculate->handle($this->propertyValue, $this->annualRentalIncome, $this->annualExpenses);
            $this->error = null;
        } catch (\Throwable $exception) {
            $this->result = null;
            $this->error = $exception->getMessage();
        }
    }

    public function resetCalculation(): void
    {
        $this->result = null;
        $this->error = null;
    }

    public function render(): View
    {
        return view('real-estate-valuations-livewire::rental-yield-calculator');
    }
}
