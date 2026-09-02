<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\PropertiesLivewire\Components\AdvancedPropertySearch;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyComparison;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyDetail;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyList;
use Liberu\RealEstate\ViewingsLivewire\Components\ViewingBooking;

uses(RefreshDatabase::class);

it('registers the modular property web entry points', function (): void {
    expect(Route::getRoutes()->getByName('property.list')->getActionName())->toContain(PropertyList::class)
        ->and(Route::getRoutes()->getByName('property.search')->getActionName())->toContain(AdvancedPropertySearch::class)
        ->and(Route::getRoutes()->getByName('property.detail')->getActionName())->toContain(PropertyDetail::class)
        ->and(Route::getRoutes()->getByName('property.compare')->getActionName())->toContain(PropertyComparison::class)
        ->and(Route::getRoutes()->getByName('property.book')->getActionName())->toContain(ViewingBooking::class);
});

it('renders the authenticated property web surfaces from the modular routes', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), [
        'address' => '1 High Street',
        'title' => 'Web route property',
        'list_date' => now()->subDay()->toDateString(),
    ]);

    $this->actingAs($user)
        ->get(route('property.list'))
        ->assertOk()
        ->assertSee('1 High Street');

    $this->get(route('property.search'))->assertOk()->assertSee('Advanced property search');
    $this->get(route('property.detail', $property->getKey()))->assertOk()->assertSee('Web route property');
    $this->get(route('property.compare', (string) $property->getKey()))->assertOk();
    $this->get(route('property.book', $property->getKey()))->assertOk()->assertSee('Book a viewing');
});
