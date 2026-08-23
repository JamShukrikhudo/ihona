<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Matching\Application\CreateMatchProfile;
use Liberu\RealEstate\Matching\Application\DeleteMatchProfile;
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
