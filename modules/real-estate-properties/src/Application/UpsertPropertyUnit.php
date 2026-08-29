<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyUnit;

final class UpsertPropertyUnit
{
    /** @param array<string, mixed> $attributes */
    public function handle(Property $property, int|string $teamId, array $attributes): PropertyUnit
    {
        if ((string) $property->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['property' => 'The property does not belong to this team.']);
        }
        $label = trim((string) ($attributes['label'] ?? ''));
        if ($label === '') {
            throw ValidationException::withMessages(['label' => 'A unit label is required.']);
        }

        return PropertyUnit::query()->updateOrCreate(['property_id' => $property->getKey(), 'label' => $label], ['team_id' => $teamId, 'status' => $attributes['status'] ?? 'active', 'floor' => $attributes['floor'] ?? null, 'bedrooms' => $attributes['bedrooms'] ?? null, 'bathrooms' => $attributes['bathrooms'] ?? null, 'area_sqft' => $attributes['area_sqft'] ?? null, 'characteristics' => $attributes['characteristics'] ?? []]);
    }
}
