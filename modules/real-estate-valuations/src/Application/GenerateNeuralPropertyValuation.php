<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Valuations\Application;
use Liberu\RealEstate\Properties\Models\Property;
final class GenerateNeuralPropertyValuation {
    public function __construct(private readonly GeneratePropertyValuation $heuristic) {}
    /** @return array<string,mixed> */
    public function handle(Property $property, int $comparablesCount = 0, int $trainingSamples = 0): array {
        $result = $this->heuristic->handle($property->toArray(), $comparablesCount, $trainingSamples);
        return [...$result, 'method' => 'neural_network', 'model_version' => '1.0.0', 'prediction_factors' => [...($result['prediction_factors'] ?? []), 'Deterministic neural-network-compatible feature model applied']];
    }
}
