<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
    Storage::fake('public');
});

it('lists staging styles and uploads an optionally staged original', function (): void {
    $user = User::factory()->create(['current_team_id' => 41]);
    $this->actingAs($user, 'sanctum')->getJson('/api/v1/real-estate/media-and-documents/staging/styles')->assertOk()->assertJsonPath('data.styles.modern', 'Modern');

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/media-and-documents/staging/upload', ['property_id' => 99, 'image' => UploadedFile::fake()->image('lounge.jpg'), 'staging_style' => 'modern', 'auto_stage' => true]);
    $response->assertCreated()->assertJsonPath('data.image.metadata.staged', false)->assertJsonPath('data.staged_image.metadata.staging_style', 'modern')->assertJsonPath('data.has_staged_versions', true);
    expect(MediaDocument::query()->where('team_id', 41)->count())->toBe(2);
});

it('stages an existing original once and keeps it team scoped', function (): void {
    $user = User::factory()->create(['current_team_id' => 41]);
    $path = 'property-images/41/original.jpg';
    Storage::disk('public')->put($path, 'image');
    $original = MediaDocument::query()->create(['team_id' => 41, 'created_by' => $user->getKey(), 'property_id' => 99, 'kind' => 'photo', 'path' => $path, 'metadata' => ['staged' => false]]);

    $staged = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/media-and-documents/staging/'.$original->getKey().'/stage', ['staging_style' => 'luxury'])->assertCreated()->assertJsonPath('data.staged_image.metadata.staging_style', 'luxury')->json('data.staged_image.id');
    $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/media-and-documents/staging/'.$staged.'/stage', ['staging_style' => 'modern'])->assertUnprocessable()->assertJsonValidationErrors(['media']);

    $other = User::factory()->create(['current_team_id' => 42]);
    $this->actingAs($other, 'sanctum')->postJson('/api/v1/real-estate/media-and-documents/staging/'.$original->getKey().'/stage', ['staging_style' => 'modern'])->assertNotFound();
});
