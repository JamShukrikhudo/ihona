<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\RealEstate\Properties\Models\Property;

uses(RefreshDatabase::class);

function disclosureProperty(array $attributes = []): Property
{
    return Property::query()->make(array_merge(['address' => '1 High Street', 'currency' => 'GBP'], $attributes));
}

it('preserves rental-aware rates and currency labels', function (): void {
    $rental = disclosureProperty(['status' => 'to_let', 'price' => 1150, 'area_sqft' => 682]);
    $euro = disclosureProperty(['currency' => 'EUR', 'price' => 565000, 'area_sqft' => 1240]);

    expect($rental->isRental())->toBeTrue()
        ->and($rental->pricePerSquareFootForHumans())->toBe('1.69 pcm')
        ->and($rental->pricePerSquareFootLabel())->toBe('£/sq ft')
        ->and($euro->currencySymbol())->toBe('€')
        ->and($euro->pricePerSquareFootLabel())->toBe('€/sq ft');
});

it('formats tenure and flags only genuinely short leaseholds', function (): void {
    expect(disclosureProperty(['tenure' => 'leasehold', 'lease_years_remaining' => 68])->tenureForHumans())
        ->toBe('Leasehold, 68 years remaining')
        ->and(disclosureProperty(['tenure' => 'leasehold', 'lease_years_remaining' => 68])->hasShortLease())->toBeTrue()
        ->and(disclosureProperty(['tenure' => 'freehold', 'lease_years_remaining' => 68])->tenureForHumans())->toBe('Freehold')
        ->and(disclosureProperty(['tenure' => 'leasehold', 'lease_years_remaining' => 80])->hasShortLease())->toBeFalse();
});

it('derives annual energy cost only from recorded certificate costs', function (): void {
    expect(disclosureProperty(['epc' => ['annual_energy_cost' => 1240]])->annualEnergyCost())->toBe(1240.0)
        ->and(disclosureProperty(['epc' => ['heating_cost' => 800, 'hot_water_cost' => 150, 'lighting_cost' => 90]])->annualEnergyCost())->toBe(1040.0)
        ->and(disclosureProperty(['epc' => ['rating' => 'C', 'score' => 72]])->annualEnergyCost())->toBeNull();
});

it('normalizes legacy closed statuses and listing dates', function (): void {
    expect(disclosureProperty(['status' => 'sstc'])->closedStateLabel())->toBe('Sold STC')
        ->and(disclosureProperty(['status' => 'archived'])->closedStateLabel())->toBe('Withdrawn')
        ->and(disclosureProperty(['status' => 'to_let'])->closedStateLabel())->toBeNull()
        ->and(disclosureProperty(['list_date' => now()->addDay()])->daysListed())->toBeNull()
        ->and(disclosureProperty(['list_date' => now()->subDays(100), 'sold_date' => now()->subDays(40)])->daysListed())->toBe(60)
        ->and(disclosureProperty(['list_date' => now()->addDay()])->isComingSoon())->toBeTrue();
});
