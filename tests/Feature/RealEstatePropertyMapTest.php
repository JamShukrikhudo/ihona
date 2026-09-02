<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyMap;

uses(RefreshDatabase::class);

it('shapes caller-provided listings into safe map points', function (): void {
    $points = PropertyMap::points([[
        'id' => 7,
        'address' => '1 High Street',
        'price' => 565000,
        'currency' => 'GBP',
        'latitude' => 51.45,
        'longitude' => -0.97,
    ]]);

    expect($points->all())->toBe([[
        'id' => 7,
        'title' => '1 High Street',
        'price' => 565000,
        'currency' => '£',
        'latitude' => 51.45,
        'longitude' => -0.97,
    ]]);
});

it('bounds default map data to properties with real coordinates', function (): void {
    $create = app(CreateProperty::class);
    $create->handle(10, 20, ['address' => 'Mapped', 'latitude' => 51.45, 'longitude' => -0.97]);
    $create->handle(10, 20, ['address' => 'No coordinates']);
    $create->handle(11, 21, ['address' => 'Other team', 'latitude' => 52.0, 'longitude' => -1.0]);

    expect(PropertyMap::mappable(10)->count())->toBe(1);
});

it('renders a map container and does not interpolate popup titles as markup', function (): void {
    $html = Blade::render('<x-property-map :properties="$points" />', [
        'points' => PropertyMap::points([[
            'id' => 1,
            'title' => '<img src=x onerror=alert(1)>',
            'price' => 100,
            'currency' => 'GBP',
            'latitude' => 51.5,
            'longitude' => -0.1,
        ]]),
    ]);

    expect($html)
        ->toContain('data-map')
        ->toContain('wire:ignore')
        ->not->toContain('<img src=x onerror=alert(1)>');

    $view = file_get_contents(base_path('modules/real-estate-properties-livewire/resources/views/property-map.blade.php'));

    expect($view)
        ->toContain('textContent')
        ->not->toContain("'<strong>' + property.title");
});
