<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Zoopla\Application;

use Liberu\RealEstate\Zoopla\Models\ZooplaSync;
use Liberu\RealEstate\Zoopla\Transport\ZooplaClient;

final class SyncZooplaListing
{
    public function __construct(private readonly ZooplaClient $client) {}

    public function handle(ZooplaSync $sync, string $reference, array $payload, array $credentials): ZooplaSync
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
