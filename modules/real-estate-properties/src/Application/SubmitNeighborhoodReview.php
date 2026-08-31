<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Models\Neighborhood;
use Liberu\RealEstate\Properties\Models\NeighborhoodReview;

final class SubmitNeighborhoodReview
{
    /** @param array<string, mixed> $data */
    public function handle(int|string $teamId, int|string $userId, int|string $neighborhoodId, array $data): NeighborhoodReview
    {
        $neighborhood = Neighborhood::query()->forTeam($teamId)->findOrFail($neighborhoodId);

        if (NeighborhoodReview::query()->forTeam($teamId)->where('neighborhood_id', $neighborhood->getKey())->where('user_id', $userId)->exists()) {
            throw ValidationException::withMessages(['review' => 'You have already reviewed this neighborhood.']);
        }

        return DB::transaction(fn (): NeighborhoodReview => NeighborhoodReview::query()->create([
            'team_id' => $teamId,
            'neighborhood_id' => $neighborhood->getKey(),
            'user_id' => $userId,
            'rating' => $data['rating'],
            'title' => $data['title'],
            'comment' => $data['comment'],
            'review_date' => now()->toDateString(),
            'ip_address' => $data['ip_address'] ?? null,
        ]));
    }
}
