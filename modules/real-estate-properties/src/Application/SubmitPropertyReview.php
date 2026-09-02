<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyReview;

final class SubmitPropertyReview
{
    /** @param array{rating: int, title: string, comment: string, ip_address?: string|null} $data */
    public function handle(int|string $teamId, int|string $userId, int|string $propertyId, array $data): PropertyReview
    {
        $property = Property::query()->forTeam($teamId)->whereKey($propertyId)->firstOrFail();
        $rating = (int) ($data['rating'] ?? 0);
        $title = trim((string) ($data['title'] ?? ''));
        $comment = trim((string) ($data['comment'] ?? ''));

        if ($rating < 1 || $rating > 5 || mb_strlen($title) < 3 || mb_strlen($title) > 100 || mb_strlen($comment) < 10 || mb_strlen($comment) > 1000) {
            throw ValidationException::withMessages(['review' => 'A review needs a rating from 1 to 5, a short title, and a comment between 10 and 1000 characters.']);
        }

        if (! $this->hasPropertyInteraction($teamId, $userId, $property->getKey())) {
            throw ValidationException::withMessages(['review' => 'You must have completed a viewing before reviewing this property.']);
        }

        return DB::transaction(fn (): PropertyReview => PropertyReview::query()->create([
            'team_id' => $teamId,
            'property_id' => $property->getKey(),
            'user_id' => $userId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'moderation_status' => 'pending',
            'approved' => false,
            'ip_address' => $data['ip_address'] ?? null,
        ]));
    }

    private function hasPropertyInteraction(int|string $teamId, int|string $userId, int|string $propertyId): bool
    {
        if (! Schema::hasTable('real_estate_viewings')) {
            return false;
        }

        return DB::table('real_estate_viewings')->where('team_id', $teamId)->where('property_id', $propertyId)->where('created_by', $userId)->where('status', 'completed')->exists();
    }
}
