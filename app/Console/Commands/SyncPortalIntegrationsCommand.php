<?php

namespace App\Console\Commands;

use App\Models\PortalIntegration;
use App\Services\PortalSyncService;
use Illuminate\Console\Command;

class SyncPortalIntegrationsCommand extends Command
{
    protected $signature = 'portal-integrations:sync {--force : Synchronize active integrations regardless of frequency}';

    protected $description = 'Synchronize active tenant portal integrations that are due';

    public function handle(PortalSyncService $sync): int
    {
        $processed = 0;

        PortalIntegration::query()
            ->where('active', true)
            ->orderBy('id')
            ->each(function (PortalIntegration $integration) use ($sync, &$processed): void {
                if (! $this->option('force') && ! $this->isDue($integration)) {
                    return;
                }

                $run = $sync->sync($integration);
                $processed++;
                $this->line("{$integration->provider} #{$integration->id}: {$run->status}");
            });

        $this->info("Synchronized {$processed} portal integration(s).");

        return self::SUCCESS;
    }

    private function isDue(PortalIntegration $integration): bool
    {
        if (! $integration->last_synced_at) {
            return $integration->sync_frequency !== 'manual';
        }

        return match ($integration->sync_frequency) {
            'hourly' => $integration->last_synced_at->lte(now()->subHour()),
            'weekly' => $integration->last_synced_at->lte(now()->subWeek()),
            'manual' => false,
            default => $integration->last_synced_at->lte(now()->subDay()),
        };
    }
}
