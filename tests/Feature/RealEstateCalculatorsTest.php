<?php

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Application\CalculateStampDuty;
use Liberu\RealEstate\ValuationsLivewire\Components\Calculators;
use Livewire\Livewire;

beforeEach(function (): void {
    Livewire::component('test-calculators', Calculators::class);
});

it('provides the public calculator hub and delegates supported calculations', function (): void {
    Livewire::test('test-calculators')
        ->assertSet('calculatorType', 'mortgage')
        ->set('propertyPrice', 200000)->set('loanAmount', 160000)->set('interestRate', 3.5)->set('loanTerm', 25)->call('calculateMortgage')->assertSet('mortgageResult.monthly_payment', fn ($value): bool => (float) $value > 0)
        ->set('propertyValue', 250000)->set('isFirstTimeBuyer', true)->set('movingDistance', 50)->call('calculateCostOfMoving')->assertSet('costOfMovingResult.total_cost', 5795.0)
        ->set('purchasePrice', 400000)->set('buyerType', 'home_mover')->call('calculateStampDuty')->assertSet('stampDutyResult.stamp_duty', 7500.0)
        ->set('rentalPropertyValue', 200000)->set('annualRentalIncome', 12000)->set('annualExpenses', 2000)->call('calculateRentalYield')->assertSet('rentalYieldResult.net_yield', 5.0)
        ->set('calculatorType', 'moving')->assertSee('Cost of moving estimate');

    $this->get('/calculators')->assertOk()->assertSee('Property calculators');
});

it('rejects invalid calculator input before invoking domain logic', function (): void {
    expect(fn () => app(CalculateStampDuty::class)->handle(-1, 'home_mover'))->toThrow(ValidationException::class);

    Livewire::test('test-calculators')->set('propertyPrice', 'not-a-number')->set('loanAmount', null)->set('interestRate', '')->set('loanTerm', 'abc')->call('calculateMortgage')->assertHasErrors(['propertyPrice', 'loanAmount', 'interestRate', 'loanTerm']);
});
