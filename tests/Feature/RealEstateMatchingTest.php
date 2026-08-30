<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Matching\Application\CalculateMatchScore;
use Liberu\RealEstate\Matching\Application\CreateMatchProfile;
use Liberu\RealEstate\Matching\Application\DeleteMatchProfile;
use Liberu\RealEstate\Matching\Application\RankPropertyRecommendations;
use Liberu\RealEstate\Matching\Application\UpdateMatchProfileSection;
use Liberu\RealEstate\Matching\Domain\MatchProfileSection;
use Liberu\RealEstate\Matching\Models\MatchProfile;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Liberu\RealEstate\MatchingLivewire\Components\PropertyRecommendations;
use Livewire\Livewire;

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

it('ranks bounded property recommendations and excludes seen properties', function (): void {
    $recommendations = app(RankPropertyRecommendations::class)->handle(
        ['min_price' => 300000, 'max_price' => 400000, 'min_bedrooms' => 3, 'property_type' => 'house'],
        [
            ['id' => 1, 'title' => 'Strong match', 'price' => 350000, 'bedrooms' => 3, 'property_type' => 'house'],
            ['id' => 2, 'title' => 'Weak match', 'price' => 900000, 'bedrooms' => 1, 'property_type' => 'apartment'],
            ['id' => 3, 'title' => 'Seen match', 'price' => 350000, 'bedrooms' => 3, 'property_type' => 'house'],
        ],
        2,
        [3],
    );

    expect($recommendations)->toHaveCount(2)
        ->and($recommendations[0]['id'])->toBe(1)
        ->and($recommendations[0]['recommendation_score'])->toBeGreaterThan($recommendations[1]['recommendation_score'])
        ->and($recommendations[1]['id'])->not->toBe(3)
        ->and($recommendations[0]['match_breakdown'])->toHaveKey('match_score');
});

it('serves recommendations through the authenticated API and Livewire adapters', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $properties = [
        ['id' => 1, 'title' => 'Recommended home', 'price' => 350000, 'bedrooms' => 3, 'property_type' => 'house'],
        ['id' => 2, 'title' => 'Other home', 'price' => 900000, 'bedrooms' => 1, 'property_type' => 'apartment'],
    ];
    $criteria = ['min_price' => 300000, 'max_price' => 400000, 'min_bedrooms' => 3, 'property_type' => 'house'];
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/real-estate/matching/recommend-properties', compact('criteria', 'properties'))
        ->assertOk()
        ->assertJsonPath('data.recommendations.0.id', 1)
        ->assertJsonPath('data.recommendations.0.recommendation_score', 72.5);

    $this->actingAs($user);
    Livewire::component('test-property-recommendations', PropertyRecommendations::class);

    Livewire::test('test-property-recommendations', ['criteria' => $criteria, 'candidates' => $properties])
        ->assertSee('Recommended properties')
        ->assertSee('Recommended home')
        ->assertSee('Match score');
});
