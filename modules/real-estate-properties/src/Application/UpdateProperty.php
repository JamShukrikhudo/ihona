<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Models\Property;

final class UpdateProperty
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $actorId, int|string $propertyId, array $attributes): Property
    {
        $address = array_key_exists('address', $attributes)
            ? trim((string) $attributes['address'])
            : null;

        if ($address === '') {
            throw ValidationException::withMessages(['address' => 'An address is required.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $propertyId, $attributes, $address): Property {
            $property = Property::query()->forTeam($teamId)->findOrFail($propertyId);
            $changes = [];

            foreach (['address', 'property_type', 'characteristics', 'utilities', 'features'] as $field) {
                if (! array_key_exists($field, $attributes)) {
                    continue;
                }

                $value = $field === 'address' ? $address : $attributes[$field];
                if ($property->getAttribute($field) !== $value) {
                    $changes[$field] = ['from' => $property->getAttribute($field), 'to' => $value];
                }
            }

            if ($changes !== []) {
                $property->fill(array_intersect_key($attributes, array_flip([
                    'property_type', 'characteristics', 'utilities', 'features',
                ])) + ($address === null ? [] : ['address' => $address]));
                $property->save();

                $property->history()->create([
                    'team_id' => $teamId,
                    'actor_id' => $actorId,
                    'event' => 'updated',
                    'changes' => $changes,
                ]);
            }

            return $property->fresh('history');
        });
    }
}
