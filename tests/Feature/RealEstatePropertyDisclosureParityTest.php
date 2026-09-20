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
    $rental = disclosureProperty(['deal_type' => 'rent', 'price' => 1150, 'area_sqft' => 682]);
    $euro = disclosureProperty(['currency' => 'EUR', 'price' => 565000, 'area_sqft' => 1240]);

    expect($rental->isRental())->toBeTrue()
        ->and($rental->pricePerSquareMeterForHumans())->toBe('1.69 pcm')
        ->and($rental->pricePerSquareMeterLabel())->toBe('£/m²')
        ->and($euro->currencySymbol())->toBe('€')
        ->and($euro->pricePerSquareMeterLabel())->toBe('€/m²');
});

it('formats tenure and flags only genuinely short leaseholds', function (): void {
    expect(disclosureProperty(['tenure' => 'leasehold', 'lease_years_remaining' => 68])->tenureForHumans())
        ->toBe('Leasehold, 68 years remaining')
        ->and(disclosureProperty(['tenure' => 'leasehold', 'lease_years_remaining' => 68])->hasShortLease())->toBeTrue()
        ->and(disclosureProperty(['tenure' => 'freehold', 'lease_years_remaining' => 68])->tenureForHumans())->toBe('Freehold')
        ->and(disclosureProperty(['tenure' => 'leasehold', 'lease_years_remaining' => 80])->hasShortLease())->toBeFalse();
});

it('derives listing-date facts', function (): void {
    expect(disclosureProperty(['list_date' => now()->addDay()])->daysListed())->toBeNull()
        ->and(disclosureProperty(['list_date' => now()->subDays(100), 'sold_date' => now()->subDays(40)])->daysListed())->toBe(60)
        ->and(disclosureProperty(['list_date' => now()->addDay()])->isComingSoon())->toBeTrue();
});
