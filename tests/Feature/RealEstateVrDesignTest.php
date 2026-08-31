<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\VrDesign\Application\VrDesignService;
use Liberu\RealEstate\VrDesign\Models\VrDesign;
use Liberu\RealEstate\VrDesignLivewire\Components\DesignStudio;
use Livewire\Livewire;

it('exposes the VR configuration catalog through the modular API', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));

    $this->actingAs($user, 'sanctum')->getJson('/api/v1/real-estate/vr-design/styles')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.styles.modern.name', 'Modern');
    $this->getJson('/api/v1/real-estate/vr-design/furniture-categories')->assertOk()->assertJsonPath('data.categories.seating.0', 'Sofa');
    $this->getJson('/api/v1/real-estate/vr-design/room-types')->assertOk()->assertJsonPath('data.room_types.bedroom', 'Bedroom');
    $this->getJson('/api/v1/real-estate/vr-design/devices')->assertOk()->assertJsonPath('data.devices.browser', 'WebXR-compatible browsers');
});

it('keeps VR design CRUD and furniture operations tenant-scoped', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $otherUser = User::factory()->create(['current_team_id' => 11]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => '1 High Street']);
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/vr-design/properties/'.$property->getKey().'/designs', [
        'name' => 'Modern living room',
        'description' => 'A test design',
        'style' => 'modern',
        'design_data' => ['room' => 'living_room'],
    ])->assertCreated()->assertJsonPath('success', true);
    $designId = $response->json('data.id');

    $this->postJson('/api/v1/real-estate/vr-design/designs/'.$designId.'/furniture', ['category' => 'seating', 'type' => 'Sofa', 'position' => [0, 0, 0]])
        ->assertOk()->assertJsonPath('data.furniture_items.0.type', 'Sofa');
    $this->actingAs($otherUser, 'sanctum')->getJson('/api/v1/real-estate/vr-design/designs/'.$designId)->assertNotFound();
    $this->actingAs($user, 'sanctum')->getJson('/api/v1/real-estate/vr-design/designs/'.$designId.'/export')->assertOk()->assertJsonPath('data.name', 'Modern living room');
});

it('supports service-level thumbnail, clone, and export behavior', function (): void {
    Storage::fake('public');
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => '1 High Street']);
    $service = app(VrDesignService::class);
    $design = $service->create(10, $user->getKey(), $property->getKey(), 'Original', ['room' => 'bedroom'], style: 'modern');

    $service->uploadThumbnail($design, UploadedFile::fake()->image('thumbnail.jpg'));
    $clone = $service->cloneDesign($design, 10, $user->getKey(), 'Copy');

    expect($clone->name)->toBe('Copy')
        ->and($service->export($design))->toHaveKeys(['id', 'name', 'design_data', 'metadata'])
        ->and(DB::table('real_estate_vr_designs')->count())->toBe(2);
    Storage::disk('public')->assertExists($design->refresh()->thumbnail_path);
});

it('supports creating a design through the Livewire studio', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => '1 High Street']);
    Livewire::component('test-vr-design-studio', DesignStudio::class);

    Livewire::actingAs($user)->test('test-vr-design-studio', ['propertyId' => $property->getKey()])
        ->set('designName', 'Studio design')
        ->set('designStyle', 'modern')
        ->call('saveDesign')
        ->assertHasNoErrors()
        ->assertSet('message', 'VR design created successfully.');

    expect(VrDesign::query()->where('name', 'Studio design')->exists())->toBeTrue();
});
