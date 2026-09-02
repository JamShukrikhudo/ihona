<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyPreview;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertySubmissionForm;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');
    Livewire::component('test-property-submission-form', PropertySubmissionForm::class);
    Livewire::component('test-property-preview', PropertyPreview::class);
});

function validPropertySubmission(): array
{
    return [
        'title' => 'Test Property',
        'description' => 'A test property description',
        'location' => 'Test Location',
        'price' => 250000,
        'bedrooms' => 3,
        'bathrooms' => 2,
        'area_sqft' => 1500,
        'year_built' => 2020,
        'property_type' => 'House',
    ];
}

it('renders the modular property submission form', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);

    Livewire::actingAs($user)->test('test-property-submission-form')
        ->assertSee('Submit a property')
        ->assertSee('Generate description')
        ->assertSee('Preview property')
        ->assertSee('Submit property');
});

it('submits a property with uploaded image and video media records', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $component = Livewire::actingAs($user)->test('test-property-submission-form');

    $component->fill(validPropertySubmission())
        ->set('images', [UploadedFile::fake()->image('property.jpg')])
        ->set('video', UploadedFile::fake()->create('property.mp4', 1000, 'video/mp4'))
        ->call('submit')
        ->assertHasNoErrors();

    $property = DB::table('real_estate_properties')->first();
    expect($property)->not->toBeNull()
        ->and($property->status)->toBe('draft')
        ->and(DB::table('real_estate_media_documents')->where('property_id', $property->id)->where('kind', 'photo')->count())->toBe(1)
        ->and(DB::table('real_estate_media_documents')->where('property_id', $property->id)->where('kind', 'video')->count())->toBe(1);
});

it('validates required submission fields and video mime types', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);

    Livewire::actingAs($user)->test('test-property-submission-form')
        ->call('submit')
        ->assertHasErrors(['title', 'description', 'location', 'price', 'bedrooms', 'bathrooms', 'area_sqft', 'year_built', 'property_type']);

    Livewire::actingAs($user)->test('test-property-submission-form')
        ->fill(validPropertySubmission())
        ->set('video', UploadedFile::fake()->create('property.avi', 1000, 'video/avi'))
        ->call('submit')
        ->assertHasErrors(['video']);
});

it('generates a tone-aware description and dispatches a preview', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $component = Livewire::actingAs($user)->test('test-property-submission-form')->fill(validPropertySubmission());

    $component->set('descriptionTone', 'luxury')
        ->call('generateAIDescription')
        ->assertSet('description', 'A exceptional house in Test Location with 3 bedrooms, 2 bathrooms, and 1500 square feet of space.')
        ->call('preview')
        ->assertDispatched('previewProperty');
});

it('renders the preview payload without persisting it', function (): void {
    Livewire::test('test-property-preview')
        ->assertSee('No property selected for preview.')
        ->dispatch('previewProperty', property: [
            'title' => 'Preview home',
            'location' => 'Preview Street',
            'price' => 123000,
            'description' => 'A preview description',
        ])
        ->assertSee('Preview home')
        ->assertSee('Preview Street')
        ->assertSee('A preview description');
});
