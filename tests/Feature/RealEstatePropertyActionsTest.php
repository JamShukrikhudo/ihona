<?php

use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Application\DeleteProperty;
use Liberu\RealEstate\Properties\Application\UpdateProperty;
use Liberu\RealEstate\Properties\Models\Property;

it('updates team properties and retains a change history', function () {
    expect(Schema::hasTable('real_estate_properties'))->toBeTrue();

    $property = app(CreateProperty::class)->handle(10, 20, ['address' => '1 High Street']);

    $updated = app(UpdateProperty::class)->handle(10, 20, $property->getKey(), ['address' => '2 High Street']);

    expect($updated->address)->toBe('2 High Street')
        ->and($updated->history->last()->event)->toBe('updated')
        ->and($updated->history->last()->changes['address']['to'])->toBe('2 High Street');
});

it('soft deletes team properties and retains the deletion history', function () {
    $property = app(CreateProperty::class)->handle(10, 20, ['address' => '1 High Street']);

    app(DeleteProperty::class)->handle(10, 20, $property->getKey());

        expect(Property::query()->find($property->getKey()))->toBeNull()
        ->and(Property::withTrashed()->find($property->getKey()))->not->toBeNull()
        ->and(Property::withTrashed()->find($property->getKey())->history()->where('event', 'deleted')->exists())->toBeTrue();
});
