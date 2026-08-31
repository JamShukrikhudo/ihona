<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Liberu\RealEstate\Properties\Application\GeneratePropertyDescription;

it('generates a deterministic description without an external key', function (): void {
    config(['services.openai.api_key' => null]);

    $description = app(GeneratePropertyDescription::class)->handle(['property_type' => 'House', 'bedrooms' => 3, 'bathrooms' => 2, 'area_sqft' => 1500, 'location' => 'London', 'price' => 350000], 'luxury');

    expect($description)->toContain('exceptional house')->toContain('London');
});

it('uses the configured AI provider and rejects failed responses', function (): void {
    config(['services.openai.api_key' => 'test-key']);
    Http::fakeSequence('*')
        ->push(['choices' => [['message' => ['content' => 'A beautiful home.']]]])
        ->push([], 500);

    expect(app(GeneratePropertyDescription::class)->handle(['property_type' => 'House', 'location' => 'London'], 'professional'))->toBe('A beautiful home.');
    Http::assertSent(fn ($request): bool => $request->hasHeader('Authorization', 'Bearer test-key'));

    expect(fn () => app(GeneratePropertyDescription::class)->handle(['property_type' => 'House'], 'professional'))->toThrow(RuntimeException::class, 'Failed to generate AI description');
});
