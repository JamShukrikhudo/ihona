<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Zoopla\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Zoopla\Models\ZooplaSync;

final class CreateZooplaSync
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): ZooplaSync
    {
        $listingId = $attributes['listing_id'] ?? null;
        if ($listingId === null) {
            throw ValidationException::withMessages(['listing_id' => 'A listing is required for Zoopla synchronization.']);
        }

        return DB::transaction(fn (): ZooplaSync => ZooplaSync::query()->updateOrCreate(['team_id' => $teamId, 'listing_id' => $listingId], ['created_by' => $actorId, 'property_id' => $attributes['property_id'] ?? null, 'external_id' => $attributes['external_id'] ?? null, 'status' => $attributes['status'] ?? 'pending', 'payload' => $attributes['payload'] ?? [], 'last_synced_at' => $attributes['last_synced_at'] ?? null, 'error' => $attributes['error'] ?? null]));
    }
}
