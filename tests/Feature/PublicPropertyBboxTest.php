<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;

uses(RefreshDatabase::class);

it('filters the public property list to a map viewport bounding box', function (): void {
    $team = Team::factory()->create();

    $inside = app(CreateProperty::class)->handle($team->id, $team->user_id, [
        'address' => 'Inside the viewport', 'latitude' => 38.56, 'longitude' => 68.78,
    ]);
    $inside->forceFill(['status' => PropertyStatus::Available])->save();

    $outside = app(CreateProperty::class)->handle($team->id, $team->user_id, [
        'address' => 'Outside the viewport', 'latitude' => 40.0, 'longitude' => 70.0,
    ]);
    $outside->forceFill(['status' => PropertyStatus::Available])->save();

    $response = $this->getJson('/api/v1/public/properties?bbox=68.7,38.5,68.9,38.6');

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($inside->id)->not->toContain($outside->id);
});

it('rejects a malformed bbox', function (): void {
    Team::factory()->create();

    $this->getJson('/api/v1/public/properties?bbox=not-a-bbox')->assertStatus(422);
});

it('scopes properties to a rectangle without loading unrelated rows', function (): void {
    $team = Team::factory()->create();
    $near = app(CreateProperty::class)->handle($team->id, $team->user_id, [
        'address' => 'Near', 'latitude' => 38.56, 'longitude' => 68.78,
    ]);
    $far = app(CreateProperty::class)->handle($team->id, $team->user_id, [
        'address' => 'Far', 'latitude' => -10.0, 'longitude' => -10.0,
    ]);

    $ids = Property::query()->withinBounds(38.5, 68.7, 38.6, 68.9)->pluck('id');

    expect($ids)->toContain($near->id)->not->toContain($far->id);
});
