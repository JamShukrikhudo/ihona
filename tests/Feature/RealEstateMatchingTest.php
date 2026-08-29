<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Matching\Application\CalculateMatchScore;
use Liberu\RealEstate\Matching\Application\CreateMatchProfile;
use Liberu\RealEstate\Matching\Application\DeleteMatchProfile;
use Liberu\RealEstate\Matching\Application\UpdateMatchProfileSection;
use Liberu\RealEstate\Matching\Domain\MatchProfileSection;
use Liberu\RealEstate\Matching\Models\MatchProfile;

uses(RefreshDatabase::class);
it('creates a bounded matching profile', function (): void {
    $profile = app(CreateMatchProfile::class)->handle(1, 5, ['subject' => 'First-time buyer', 'score' => 125, 'affordability' => ['max' => 400000]]);
    expect($profile->score)->toBe(100)->and($profile->affordability['max'])->toBe(400000);
});
it('rejects empty subjects and archives a profile for its team', function (): void {
    expect(fn () => app(CreateMatchProfile::class)->handle(1, 5, ['subject' => '']))->toThrow(ValidationException::class);
    $profile = MatchProfile::query()->create(['team_id' => 1, 'subject' => 'Profile']);
    app(DeleteMatchProfile::class)->handle($profile, 1);
    expect(MatchProfile::withTrashed()->find($profile->id)->deleted_at)->not->toBeNull();
});

it('updates each matching concern through an explicit section boundary', function (): void {
    $profile = MatchProfile::query()->create(['team_id' => 1, 'subject' => 'Profile']);

    app(UpdateMatchProfileSection::class)->handle($profile, 1, MatchProfileSection::Requirements, ['min_bedrooms' => 3]);
    app(UpdateMatchProfileSection::class)->handle($profile, 1, MatchProfileSection::Scoring, ['score' => 87]);

    $profile->refresh();
    expect($profile->requirements)->toBe(['min_bedrooms' => 3])->and($profile->score)->toBe(87);
    expect(fn () => app(UpdateMatchProfileSection::class)->handle($profile, 1, MatchProfileSection::Scoring, ['score' => 101]))->toThrow(ValidationException::class);
});

it('reproduces the legacy weighted property match score', function (): void {
    $score = app(CalculateMatchScore::class)->handle(
        ['min_price' => 300000, 'max_price' => 400000, 'min_bedrooms' => 3, 'property_type' => 'house', 'location' => 'SW1', 'required_features' => ['garden']],
        ['price' => 350000, 'bedrooms' => 3, 'property_type' => 'house', 'location' => 'SW1A', 'features' => ['garden'], 'transit_score' => 80],
    );

    expect($score['match_score'])->toBe(90.0)->and($score['price_match'])->toBe(100.0);
});
