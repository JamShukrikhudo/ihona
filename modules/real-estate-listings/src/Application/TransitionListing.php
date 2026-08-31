<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Listings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Listings\Domain\ListingStatus;
use Liberu\RealEstate\Listings\Models\Listing;

final class TransitionListing
{
    public function handle(
        Listing $listing,
        int|string $teamId,
        ListingStatus $status,
        array $attributes = [],
    ): Listing {
        abort_unless((string) $listing->team_id === (string) $teamId, 404);
        if (! $this->canTransition($listing->status, $status)) {
            throw ValidationException::withMessages(['status' => 'That listing transition is not allowed.']);
        }
        if ($status === ListingStatus::Published && blank($listing->title)) {
            throw ValidationException::withMessages(['title' => 'A listing title is required before publication.']);
        }

        return DB::transaction(function () use ($listing, $status, $attributes): Listing {
            $values = ['status' => $status];
            if ($status === ListingStatus::Published) {
                $values['published_at'] = now();
            }
            foreach (['channel_content', 'publication_rules', 'portal_feeds', 'reconciliation'] as $field) {
                if (array_key_exists($field, $attributes)) {
                    $values[$field] = $attributes[$field];
                }
            }
            $listing->forceFill($values)->save();

            return $listing->fresh();
        });
    }

    private function canTransition(ListingStatus $from, ListingStatus $to): bool
    {
        return match ($from) {
            ListingStatus::Draft => $to === ListingStatus::Ready,
            ListingStatus::Ready => in_array($to, [ListingStatus::Published, ListingStatus::Draft, ListingStatus::Withdrawn], true),
            ListingStatus::Published => in_array($to, [ListingStatus::Suspended, ListingStatus::Withdrawn], true),
            ListingStatus::Suspended => in_array($to, [ListingStatus::Published, ListingStatus::Withdrawn], true),
            ListingStatus::Withdrawn => false,
        };
    }
}
