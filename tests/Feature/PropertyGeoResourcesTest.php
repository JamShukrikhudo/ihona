<?php

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\RealEstate\Properties\Models\City;
use Liberu\RealEstate\Properties\Models\District;
use Liberu\RealEstate\Properties\Models\Region;
use Liberu\RealEstate\PropertiesFilament\Resources\CityResource;
use Liberu\RealEstate\PropertiesFilament\Resources\CityResource\Pages\ListCities;
use Liberu\RealEstate\PropertiesFilament\Resources\DistrictResource;
use Liberu\RealEstate\PropertiesFilament\Resources\DistrictResource\Pages\ListDistricts;
use Liberu\RealEstate\PropertiesFilament\Resources\RegionResource;
use Liberu\RealEstate\PropertiesFilament\Resources\RegionResource\Pages\ListRegions;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $team = Team::factory()->create();
    $admin = User::factory()->create(['current_team_id' => $team->id]);

    Gate::before(fn () => true);
    $this->actingAs($admin);
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::setTenant($team);
});

it('is not scoped to the team tenant (geography is not a per-team fact)', function () {
    expect(RegionResource::isScopedToTenant())->toBeFalse()
        ->and(CityResource::isScopedToTenant())->toBeFalse()
        ->and(DistrictResource::isScopedToTenant())->toBeFalse();
});

it('renders the region, city, and district lists', function () {
    $region = Region::query()->create(['name' => 'Хатлон', 'slug' => 'khatlon']);
    $city = City::query()->create(['region_id' => $region->id, 'name' => 'Куляб', 'slug' => 'kulob']);
    District::query()->create(['city_id' => $city->id, 'name' => 'Центральный', 'slug' => 'central']);

    Livewire::test(ListRegions::class)->assertOk()->assertCanSeeTableRecords([$region]);
    Livewire::test(ListCities::class)->assertOk()->assertCanSeeTableRecords([$city]);
    Livewire::test(ListDistricts::class)->assertOk()->assertCanSeeTableRecords([District::first()]);
});

it('creates a region, then a city under it, then a district under that city', function () {
    Livewire::test(RegionResource\Pages\CreateRegion::class)
        ->fillForm(['name' => 'Согд'])
        ->call('create')
        ->assertHasNoFormErrors();

    $region = Region::query()->where('name', 'Согд')->firstOrFail();
    expect($region->slug)->toBe('sogd');

    Livewire::test(CityResource\Pages\CreateCity::class)
        ->fillForm(['region_id' => $region->id, 'name' => 'Худжанд'])
        ->call('create')
        ->assertHasNoFormErrors();

    $city = City::query()->where('name', 'Худжанд')->firstOrFail();
    expect($city->region_id)->toBe($region->id);

    Livewire::test(DistrictResource\Pages\CreateDistrict::class)
        ->fillForm(['city_id' => $city->id, 'name' => 'Северный'])
        ->call('create')
        ->assertHasNoFormErrors();

    $district = District::query()->where('name', 'Северный')->firstOrFail();
    expect($district->city_id)->toBe($city->id);
});
