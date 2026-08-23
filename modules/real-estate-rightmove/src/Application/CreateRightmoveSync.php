<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Rightmove\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Rightmove\Models\RightmoveSync;

final class CreateRightmoveSync
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): RightmoveSync
    {
        $listingId = $attributes['listing_id'] ?? null;
        if ($listingId === null) {
            throw ValidationException::withMessages(['listing_id' => 'A listing is required for Rightmove synchronization.']);
        }

return DB::transaction(fn (): RightmoveSync => RightmoveSync::query()->updateOrCreate(['team_id' => $teamId, 'listing_id' => $listingId], ['created_by' => $actorId, 'property_id' => $attributes['property_id'] ?? null, 'external_id' => $attributes['external_id'] ?? null, 'status' => $attributes['status'] ?? 'pending', 'payload' => $attributes['payload'] ?? [], 'last_synced_at' => $attributes['last_synced_at'] ?? null, 'error' => $attributes['error'] ?? null]));
    }
}
