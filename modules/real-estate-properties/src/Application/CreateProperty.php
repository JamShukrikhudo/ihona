<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;

final class CreateProperty
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Property
    {
        $address = trim((string) ($attributes['address'] ?? ''));
        if ($address === '') {
            throw ValidationException::withMessages(['address' => 'An address is required.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $attributes, $address): Property {
            $property = Property::query()->create([
                'team_id' => $teamId,
                'created_by' => $actorId,
                'address' => $address,
                'property_type' => $attributes['property_type'] ?? 'residential',
                'characteristics' => $attributes['characteristics'] ?? [],
                'utilities' => $attributes['utilities'] ?? [],
                'features' => $attributes['features'] ?? [],
                'status' => PropertyStatus::Draft,
            ]);

            $property->history()->create([
                'team_id' => $teamId,
                'actor_id' => $actorId,
                'event' => 'created',
                'changes' => ['status' => PropertyStatus::Draft->value],
            ]);

            return $property->fresh('history');
        });
    }
}
