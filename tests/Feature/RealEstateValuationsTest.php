<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Application\CalculateComparables;
use Liberu\RealEstate\Valuations\Application\CalculateHomeValuation;
use Liberu\RealEstate\Valuations\Application\CalculateMortgage;
use Liberu\RealEstate\Valuations\Application\GeneratePropertyValuation;
use Liberu\RealEstate\Valuations\Application\CompleteValuation;
use Liberu\RealEstate\Valuations\Application\ConvertValuation;
use Liberu\RealEstate\Valuations\Application\CreateValuation;
use Liberu\RealEstate\Valuations\Application\DeleteValuation;
use Liberu\RealEstate\Valuations\Application\ScheduleValuation;
use Liberu\RealEstate\Valuations\Models\Valuation;
use Liberu\RealEstate\ValuationsLivewire\Components\PropertyValuationEstimator;
use Livewire\Livewire;

uses(RefreshDatabase::class);
it('creates a draft valuation with pricing metadata', function (): void {
    $valuation = app(CreateValuation::class)->handle(1, 5, ['subject' => 'Market appraisal', 'valued_amount' => 350000, 'fee_amount' => 250]);
    expect($valuation->status->value)->toBe('draft')->and($valuation->valued_amount)->toBe('350000.00');
});
it('rejects empty subjects and archives a valuation for its team', function (): void {
    expect(fn () => app(CreateValuation::class)->handle(1, 5, ['subject' => '']))->toThrow(ValidationException::class);
    $valuation = Valuation::query()->create(['team_id' => 1, 'subject' => 'Valuation', 'status' => 'draft']);
    app(DeleteValuation::class)->handle($valuation, 1);
    expect(Valuation::withTrashed()->find($valuation->id)->deleted_at)->not->toBeNull();
});

it('supports comparable pricing, appraisal completion, follow-up, and conversion', function (): void {
    $valuation = app(CreateValuation::class)->handle(1, 5, ['subject' => 'Full appraisal']);
    $valuation = app(CalculateComparables::class)->handle($valuation, 1, [
        ['reference' => 'A', 'amount' => 300000],
        ['reference' => 'B', 'amount' => 400000],
    ]);
    $valuation = app(ScheduleValuation::class)->handle($valuation, 1, now()->addDay()->toDateTimeString());
    $valuation = app(CompleteValuation::class)->handle($valuation, 1, [
        'valued_amount' => 350000,
        'recommendation' => ['price_band' => ['min' => 340000, 'max' => 360000]],
        'follow_up_at' => now()->addWeek()->toDateTimeString(),
    ]);
    $valuation = app(ConvertValuation::class)->handle($valuation, 1, ['type' => 'instruction', 'id' => 42]);

    expect($valuation->status->value)->toBe('converted')
        ->and($valuation->comparable_data['average_amount'])->toBe(350000)
        ->and($valuation->recommendation['price_band']['min'])->toBe(340000)
        ->and($valuation->conversion['type'])->toBe('instruction')
        ->and($valuation->follow_up_at)->not->toBeNull();
});

it('carries the legacy home valuation calculator into the valuation boundary', function (): void {
    $result = app(CalculateHomeValuation::class)->handle(1200, 3, 2, 2018, 'semi-detached', 'good', 'prime', 3000);

    expect($result['estimated_value'])->toBeGreaterThan($result['min_value'])
        ->and($result['max_value'])->toBeGreaterThan($result['estimated_value'])
        ->and($result['confidence_level'])->toBeBetween(70, 95)
        ->and($result['breakdown']['room_bonus'])->toBe(23000.0);
});

it('carries the legacy explainable property valuation report into the valuation boundary', function (): void {
    $result = app(GeneratePropertyValuation::class)->handle([
        'address' => 'Valuation Street',
        'area_sqft' => 1200,
        'bedrooms' => 3,
        'bathrooms' => 2,
        'year_built' => 2018,
        'property_type' => 'detached',
        'is_featured' => true,
        'status' => 'available',
    ], 4, 20);

    expect($result['estimated'])->toBeTrue()
        ->and($result['estimated_value'])->toBeGreaterThan(0)
        ->and($result['price_range']['min'])->toBeLessThan($result['estimated_value'])
        ->and($result['price_range']['max'])->toBeGreaterThan($result['estimated_value'])
        ->and($result['confidence_level'])->toBeBetween(70, 100)
        ->and($result['comparables_count'])->toBe(4)
        ->and($result['feature_importance'])->not->toBeEmpty()
        ->and($result['disclaimer'])->toContain('not a professional appraisal');
});

it('serves property valuation reports through the API and Livewire adapters', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
    $property = [
        'area_sqft' => 1200,
        'bedrooms' => 3,
        'bathrooms' => 2,
        'year_built' => 2018,
        'property_type' => 'detached',
        'address' => 'Valuation Street',
    ];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/real-estate/valuations/calculate-property', ['property' => $property])
        ->assertOk()
        ->assertJsonPath('data.estimated', true)
        ->assertJsonPath('data.method', 'explainable_heuristic');

    $this->actingAs($user);
    Livewire::component('test-property-valuation-estimator', PropertyValuationEstimator::class);

    Livewire::test('test-property-valuation-estimator', ['property' => $property])
        ->call('generateValuation')
        ->assertSet('valuation.estimated', true)
        ->assertSet('valuation.method', 'explainable_heuristic')
        ->call('resetValuation')
        ->assertSet('valuation', null);
});

it('calculates amortization safely, including zero-interest loans', function (): void {
    $calculate = app(CalculateMortgage::class);
    $interestFree = $calculate->handle(240000, 120000, 0, 10);

    expect($interestFree['estimated'])->toBeTrue()
        ->and($interestFree['monthly_payment'])->toBe(1000.0)
        ->and($interestFree['total_interest'])->toBe(0.0)
        ->and($interestFree['loan_to_value'])->toBe(50.0)
        ->and($interestFree['amortization_schedule'])->toHaveCount(120)
        ->and($interestFree['amortization_schedule'][119]['balance'])->toBe(0.0)
        ->and($interestFree['disclaimer'])->toContain('actual offers');

    expect(fn () => $calculate->handle(240000, 250000, 5, 25))->toThrow(ValidationException::class);
});

it('serves mortgage estimates through the authenticated API and Livewire adapters', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/real-estate/valuations/calculate-mortgage', [
            'property_price' => 240000,
            'loan_amount' => 120000,
            'interest_rate' => 0,
            'loan_term_years' => 10,
        ])
        ->assertOk()
        ->assertJsonPath('data.estimated', true)
        ->assertJsonPath('data.monthly_payment', 1000);

    $this->actingAs($user);
    Livewire::component('test-mortgage-calculator', \Liberu\RealEstate\ValuationsLivewire\Components\MortgageCalculator::class);

    Livewire::test('test-mortgage-calculator', ['propertyPrice' => 240000, 'loanAmount' => 120000, 'interestRate' => 0, 'loanTermYears' => 10])
        ->call('calculateMortgage')
        ->assertSee('Mortgage estimate')
        ->assertSet('result.monthly_payment', 1000)
        ->call('resetCalculation')
        ->assertSet('result', null);
});
