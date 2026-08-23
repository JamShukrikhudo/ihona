<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Liberu\RealEstate\Properties\Models\Property;

final class DeleteProperty
{
    public function handle(int|string $teamId, int|string $actorId, int|string $propertyId): void
    {
        DB::transaction(function () use ($teamId, $actorId, $propertyId): void {
            $property = Property::query()->forTeam($teamId)->findOrFail($propertyId);

            $property->history()->create([
                'team_id' => $teamId,
                'actor_id' => $actorId,
                'event' => 'deleted',
                'changes' => ['property_id' => $property->getKey()],
            ]);

            $property->delete();
        });
    }
}
