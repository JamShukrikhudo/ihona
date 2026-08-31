<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Valuations\Application\AnalyzePropertyInvestment;

uses(RefreshDatabase::class);

it('returns the legacy investment analysis structure with bounded metrics', function (): void {
    $property = Property::query()->create(['team_id' => 1, 'created_by' => 5, 'title' => 'Investment home', 'property_type' => 'House', 'address' => 'London', 'price' => 300000, 'year_built' => 2015, 'status' => 'draft']);
    $analysis = app(AnalyzePropertyInvestment::class)->handle($property, ['market_data' => [['property_type' => 'House', 'avg_price' => 320000], ['property_type' => 'Flat', 'avg_price' => 200000]]]);

    expect($analysis)->toHaveKeys(['market_analysis', 'valuation', 'recommendations', 'prediction', 'cash_flow_analysis', 'market_position'])
        ->and($analysis['prediction']['predicted_roi'])->toBe(6.67)
        ->and($analysis['prediction']['risk_score'])->toBeBetween(1, 10)
        ->and($analysis['cash_flow_analysis']['net_cash_flow'])->toBe(10500.0)
        ->and($analysis['market_position']['position'])->toBe('good');
});

it('returns safe defaults when comparable market data is missing', function (): void {
    $property = Property::query()->create(['team_id' => 1, 'created_by' => 5, 'title' => 'Home', 'property_type' => 'Apartment', 'address' => 'Manchester', 'price' => 250000, 'status' => 'draft']);
    $analysis = app(AnalyzePropertyInvestment::class)->handle($property);

    expect($analysis['prediction']['predicted_roi'])->toBe(3.0)->and($analysis['market_position']['position'])->toBe('average')->and($analysis['market_position']['competitive_advantage'])->toBe('Limited market data available');
});
