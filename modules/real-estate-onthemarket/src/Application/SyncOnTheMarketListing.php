<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarket\Application;

use Liberu\RealEstate\OnTheMarket\Models\OnTheMarketSync;
use Liberu\RealEstate\OnTheMarket\Transport\OnTheMarketClient;

final class SyncOnTheMarketListing
{
    public function __construct(private readonly OnTheMarketClient $client) {}

    public function handle(OnTheMarketSync $sync, string $reference, array $payload, array $credentials): OnTheMarketSync
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
