<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Valuations\Application\CalculateMortgage;
use Livewire\Component;

final class MortgageCalculator extends Component
{
    public float $propertyPrice = 0;
    public float $loanAmount = 0;
    public float $interestRate = 0;
    public int $loanTermYears = 25;
    /** @var array<string, mixed>|null */
    public ?array $result = null;
    public ?string $error = null;

    public function calculateMortgage(CalculateMortgage $calculate): void
    {
        try {
            $this->result = $calculate->handle($this->propertyPrice, $this->loanAmount, $this->interestRate, $this->loanTermYears);
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
        return view('real-estate-valuations-livewire::mortgage-calculator');
    }
}
