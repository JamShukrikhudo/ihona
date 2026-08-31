<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Matching\Application;

use Illuminate\Validation\ValidationException;

final class RankPropertyRecommendations
{
    public function __construct(private readonly CalculateMatchScore $calculateScore) {}

    /** @param array<string, mixed> $criteria @param list<array<string, mixed>> $properties @param list<int|string> $excludedIds @return list<array<string, mixed>> */
    public function handle(array $criteria, array $properties, int $limit = 6, array $excludedIds = []): array
    {
        if ($limit < 1 || $limit > 100) {
            throw ValidationException::withMessages(['limit' => 'Recommendation limits must be between 1 and 100.']);
        }

        $excluded = array_map('strval', $excludedIds);
        $ranked = [];
        foreach ($properties as $property) {
            if (isset($property['id']) && in_array((string) $property['id'], $excluded, true)) {
                continue;
            }

            $score = $this->calculateScore->handle($criteria, $property);
            $ranked[] = array_merge($property, [
                'recommendation_score' => $score['match_score'],
                'match_breakdown' => $score,
            ]);
        }

        usort($ranked, static fn (array $left, array $right): int => ($right['recommendation_score'] <=> $left['recommendation_score'])
            ?: ((string) ($left['id'] ?? '') <=> (string) ($right['id'] ?? '')));

        return array_values(array_slice($ranked, 0, $limit));
    }
}
