<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Application\CalculateComparables;
use Liberu\RealEstate\Valuations\Application\CalculateHomeValuation;
use Liberu\RealEstate\Valuations\Application\CompleteValuation;
use Liberu\RealEstate\Valuations\Application\ConvertValuation;
use Liberu\RealEstate\Valuations\Application\CreateValuation;
use Liberu\RealEstate\Valuations\Application\DeleteValuation;
use Liberu\RealEstate\Valuations\Application\ScheduleValuation;
use Liberu\RealEstate\Valuations\Models\Valuation;

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
