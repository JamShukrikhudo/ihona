<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyKey;

final class RecordPropertyKey
{
    /** @param array<string, mixed> $attributes */
    public function handle(Property $property, int|string $teamId, array $attributes): PropertyKey
    {
        if ((string) $property->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['property' => 'The property does not belong to this team.']);
        }
        $reference = trim((string) ($attributes['key_reference'] ?? ''));
        $quantity = (int) ($attributes['quantity'] ?? 1);
        if ($reference === '') {
            throw ValidationException::withMessages(['key_reference' => 'A key reference is required.']);
        }
        if ($quantity < 1) {
            throw ValidationException::withMessages(['quantity' => 'Key quantity must be at least one.']);
        }

        return PropertyKey::query()->create(['property_id' => $property->getKey(), 'team_id' => $teamId, 'key_reference' => $reference, 'quantity' => $quantity, 'status' => $attributes['status'] ?? 'held', 'notes' => $attributes['notes'] ?? null]);
    }
}
