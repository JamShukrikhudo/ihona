<?php

namespace App\Services;

use App\Models\PortalIntegration;
use App\Models\PortalListing;
use App\Models\PortalSyncRun;
use App\Models\User;
use Throwable;

class PortalSyncService
{
    public function sync(PortalIntegration $integration, User $requestedBy): PortalSyncRun
    {
        $run = PortalSyncRun::create([
            'team_id' => $integration->team_id,
            'portal_integration_id' => $integration->id,
            'requested_by' => $requestedBy->id,
            'status' => 'running',
            'started_at' => now(),
        ]);
        $errors = [];
        $succeeded = 0;
        $listings = $integration->listings()->whereIn('status', ['pending', 'published', 'failed'])->with('property')->get();

        foreach ($listings as $listing) {
            try {
                if (! $listing->property) {
                    throw new \RuntimeException('Property no longer exists.');
                }
                $listing->update([
                    'payload' => $this->payload($listing),
                    'status' => 'published',
                    'published_at' => $listing->published_at ?? now(),
                    'last_synced_at' => now(),
                    'last_error' => null,
                ]);
                $succeeded++;
            } catch (Throwable $exception) {
                $listing->update(['status' => 'failed', 'last_error' => $exception->getMessage()]);
                $errors[] = ['listing_id' => $listing->id, 'error' => $exception->getMessage()];
            }
        }

        $failed = count($errors);
        $status = $failed === 0 ? 'completed' : ($succeeded > 0 ? 'partial' : 'failed');
        $run->update([
            'status' => $status,
            'processed' => $listings->count(),
            'succeeded' => $succeeded,
            'failed' => $failed,
            'errors' => $errors ?: null,
            'completed_at' => now(),
        ]);
        $integration->update([
            'last_synced_at' => now(),
            'last_sync_status' => $status,
            'last_error' => $errors[0]['error'] ?? null,
        ]);

        return $run->fresh();
    }

    private function payload(PortalListing $listing): array
    {
        $property = $listing->property;
        return [
            'property_id' => $property->id,
            'title' => $property->title,
            'description' => $property->description,
            'location' => $property->location,
            'postal_code' => $property->postal_code,
            'country' => $property->country,
            'coordinates' => ['latitude' => $property->latitude, 'longitude' => $property->longitude],
            'price' => $property->price,
            'bedrooms' => $property->bedrooms,
            'bathrooms' => $property->bathrooms,
            'area_sqft' => $property->area_sqft,
            'property_type' => $property->property_type,
            'status' => $property->status,
            'energy_rating' => $property->energy_rating,
            'virtual_tour_url' => $property->virtual_tour_url,
            'media' => $property->images()->orderBy('image_id')->get()->map(fn ($image) => [
                'url' => $image->url,
                'type' => $image->mime_type ?? 'image',
            ])->all(),
        ];
    }
}
