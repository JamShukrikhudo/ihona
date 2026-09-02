<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class HomeReportResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return array_merge($this->resource->toArray(), [
            'property_id' => (int) $this->resource->property_id,
            'is_expired' => $this->resource->isExpired(),
            'is_valid' => $this->resource->isValid(),
            'overall_condition' => $this->resource->overallCondition(),
            'condition_label' => $this->resource->conditionLabel(),
            'energy_improvement_points' => $this->resource->energyImprovementPoints(),
        ]);
    }
}
