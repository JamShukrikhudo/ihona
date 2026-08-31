<?php

declare(strict_types=1);

namespace Liberu\RealEstate\VrDesignApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class VrDesignResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'property_id' => $this->resource->property_id,
            'user_id' => $this->resource->user_id,
            'team_id' => $this->resource->team_id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'vr_provider' => $this->resource->vr_provider,
            'design_data' => $this->resource->design_data,
            'room_layout' => $this->resource->room_layout,
            'furniture_items' => $this->resource->furniture_items,
            'materials' => $this->resource->materials,
            'lighting' => $this->resource->lighting,
            'thumbnail_path' => $this->resource->thumbnail_path,
            'vr_scene_url' => $this->resource->vr_scene_url,
            'is_public' => $this->resource->is_public,
            'is_template' => $this->resource->is_template,
            'style' => $this->resource->style,
            'view_count' => $this->resource->view_count,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
