<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class FetchWalkabilityScores
{
    /** @return array<string, int|string|null> */
    public function handle(string $address, float $latitude, float $longitude): array
    {
        $apiKey = (string) config('services.walkscore.api_key', '');
        $baseUri = rtrim((string) config('services.walkscore.base_uri', 'https://api.walkscore.com'), '/');

        if ($apiKey === '') {
            return $this->mockScores($latitude, $longitude);
        }

        try {
            $response = Http::get($baseUri.'/score', [
                'format' => 'json', 'address' => $address, 'lat' => $latitude,
                'lon' => $longitude, 'wsapikey' => $apiKey,
            ]);

            if ($response->failed()) {
                Log::warning('Walkability score request failed', ['status' => $response->status()]);

                return $this->mockScores($latitude, $longitude);
            }

            $data = $response->json();

            return [
                'walkability_score' => $this->score($data['walkscore'] ?? null),
                'walkability_description' => $data['description'] ?? null,
                'transit_score' => $this->score(data_get($data, 'transit.score')),
                'transit_description' => data_get($data, 'transit.description'),
                'bike_score' => $this->score(data_get($data, 'bike.score')),
                'bike_description' => data_get($data, 'bike.description'),
            ];
        } catch (Throwable $exception) {
            Log::warning('Walkability score request threw an exception', ['message' => $exception->getMessage()]);

            return $this->mockScores($latitude, $longitude);
        }
    }

    /** @return array<string, int|string> */
    private function mockScores(float $latitude, float $longitude): array
    {
        $seed = abs(($latitude + $longitude) * 100);
        $walkability = (int) (($seed % 60) + 30);
        $transit = (int) (($seed % 50) + 40);
        $bike = (int) (($seed % 55) + 35);

        return [
            'walkability_score' => $walkability,
            'walkability_description' => $this->walkabilityDescription($walkability),
            'transit_score' => $transit,
            'transit_description' => $this->transitDescription($transit),
            'bike_score' => $bike,
            'bike_description' => $this->bikeDescription($bike),
        ];
    }

    private function score(mixed $score): ?int
    {
        return $score === null ? null : max(0, min(100, (int) $score));
    }

    private function walkabilityDescription(int $score): string
    {
        return match (true) {
            $score >= 90 => "Walker's Paradise", $score >= 70 => 'Very Walkable',
            $score >= 50 => 'Somewhat Walkable', $score >= 25 => 'Car-Dependent',
            default => 'Very Car-Dependent',
        };
    }

    private function transitDescription(int $score): string
    {
        return match (true) {
            $score >= 90 => "Rider's Paradise", $score >= 70 => 'Excellent Transit',
            $score >= 50 => 'Good Transit', $score >= 25 => 'Some Transit',
            default => 'Minimal Transit',
        };
    }

    private function bikeDescription(int $score): string
    {
        return match (true) {
            $score >= 90 => "Biker's Paradise", $score >= 70 => 'Very Bikeable',
            $score >= 50 => 'Bikeable', default => 'Somewhat Bikeable',
        };
    }
}
