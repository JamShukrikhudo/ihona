<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ValuationsLivewire\Components;
use Liberu\RealEstate\Valuations\Application\CalculateCostOfMoving;
use Liberu\RealEstate\Valuations\Application\CalculateMortgage;
use Liberu\RealEstate\Valuations\Application\CalculateRentalYield;
use Liberu\RealEstate\Valuations\Application\CalculateStampDuty;
use Livewire\Attributes\Validate;
use Livewire\Component;
final class Calculators extends Component {
    public string $calculatorType = 'mortgage';
    #[Validate('required|numeric|gt:0')] public float|string $propertyPrice = 0;
    #[Validate('required|numeric|gt:0')] public float|string $loanAmount = 0;
    #[Validate('required|numeric|gte:0|lte:100')] public float|string $interestRate = 0;
    #[Validate('required|integer|between:1,50')] public int|string $loanTerm = 25;
    public float|string $propertyValue = 0; public bool $isFirstTimeBuyer = false; public float|string $movingDistance = 0; public float|string $purchasePrice = 0; public string $buyerType = 'home_mover'; public float|string $rentalPropertyValue = 0; public float|string $annualRentalIncome = 0; public float|string $annualExpenses = 0;
    public ?array $mortgageResult = null; public ?array $costOfMovingResult = null; public ?array $stampDutyResult = null; public ?array $rentalYieldResult = null;
    public function calculateMortgage(CalculateMortgage $calculate): void { $this->validateOnly('propertyPrice'); $this->validateOnly('loanAmount'); $this->validateOnly('interestRate'); $this->validateOnly('loanTerm'); $this->mortgageResult = $calculate->handle((float) $this->propertyPrice, (float) $this->loanAmount, (float) $this->interestRate, (int) $this->loanTerm); }
    public function calculateCostOfMoving(CalculateCostOfMoving $calculate): void { $this->costOfMovingResult = $calculate->handle((float) $this->propertyValue, $this->isFirstTimeBuyer, (float) $this->movingDistance); }
    public function calculateStampDuty(CalculateStampDuty $calculate): void { $this->stampDutyResult = $calculate->handle((float) $this->purchasePrice, $this->buyerType); }
    public function calculateRentalYield(CalculateRentalYield $calculate): void { $this->rentalYieldResult = $calculate->handle((float) $this->rentalPropertyValue, (float) $this->annualRentalIncome, (float) $this->annualExpenses); }
    public function render(): mixed { return view('real-estate-valuations-livewire::calculators'); }
}
