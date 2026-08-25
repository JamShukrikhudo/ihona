<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;

final class TransitionProperty
{
    public function handle(
        int|string $teamId,
        int|string $actorId,
        int|string $propertyId,
        PropertyStatus $status,
    ): Property {
        return DB::transaction(function () use ($teamId, $actorId, $propertyId, $status): Property {
            $property = Property::query()->forTeam($teamId)->findOrFail($propertyId);
            if (! $this->canTransition($property->status, $status)) {
                throw ValidationException::withMessages(['status' => 'That property status transition is not allowed.']);
            }

            $values = ['status' => $status];
            if ($status === PropertyStatus::Available && $property->published_at === null) {
                $values['published_at'] = now();
            }
            $property->forceFill($values)->save();
            $property->history()->create([
                'team_id' => $teamId,
                'actor_id' => $actorId,
                'event' => 'status_changed',
                'changes' => ['from' => $property->getRawOriginal('status'), 'to' => $status->value],
            ]);

            return $property->fresh('history');
        });
    }

    private function canTransition(PropertyStatus $from, PropertyStatus $to): bool
    {
        return match ($from) {
            PropertyStatus::Draft => in_array($to, [PropertyStatus::Available, PropertyStatus::Withdrawn], true),
            PropertyStatus::Available => in_array($to, [PropertyStatus::UnderOffer, PropertyStatus::Sold, PropertyStatus::Let, PropertyStatus::Withdrawn], true),
            PropertyStatus::UnderOffer => in_array($to, [PropertyStatus::Available, PropertyStatus::Sold, PropertyStatus::Withdrawn], true),
            PropertyStatus::Sold, PropertyStatus::Let, PropertyStatus::Withdrawn => false,
        };
    }
}
