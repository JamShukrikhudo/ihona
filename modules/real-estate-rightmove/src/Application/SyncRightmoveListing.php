<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Rightmove\Application;

use Liberu\RealEstate\Rightmove\Models\RightmoveSync;
use Liberu\RealEstate\Rightmove\Transport\RightmoveClient;

final class SyncRightmoveListing
{
    public function __construct(private readonly RightmoveClient $client) {}

    public function handle(RightmoveSync $sync, string $reference, array $payload, array $credentials): RightmoveSync
    {
        $sync->update(['status' => 'syncing', 'payload' => $payload, 'error' => null]);
        try {
            $result = $this->client->sendProperty($reference, $payload, $credentials);
            $sync->update(['status' => 'synced', 'payload' => array_merge($payload, ['response' => $result]), 'last_synced_at' => now()]);
        } catch (\Throwable $exception) {
            $sync->update(['status' => 'failed', 'error' => $exception->getMessage()]);
            throw $exception;
        }

        return $sync->refresh();
    }
}
