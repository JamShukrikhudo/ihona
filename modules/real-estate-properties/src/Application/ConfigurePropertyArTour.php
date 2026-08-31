<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Models\Property;

final class ConfigurePropertyArTour
{
    /** @return array<string, mixed> */
    public function configuration(Property $property): array
    {
        $settings = is_array($property->ar_tour_settings) ? $property->ar_tour_settings : [];

        return ['model_url' => $property->model3dUrl(), 'scale' => (float) ($property->ar_model_scale ?: 1), 'placement_guide' => $property->ar_placement_guide ?: 'floor', 'ar_modes' => $settings['ar_modes'] ?? ['webxr', 'scene-viewer', 'quick-look'], 'enable_controls' => $settings['enable_controls'] ?? true, 'auto_rotate' => $settings['auto_rotate'] ?? true, 'shadow_intensity' => $settings['shadow_intensity'] ?? 1, 'camera_orbit' => $settings['camera_orbit'] ?? '0deg 75deg 2.5m', 'min_camera_orbit' => $settings['min_camera_orbit'] ?? 'auto auto 1m', 'max_camera_orbit' => $settings['max_camera_orbit'] ?? 'auto auto 10m', 'interaction_prompt' => $settings['interaction_prompt'] ?? 'auto'];
    }

    public function available(Property $property): bool
    {
        return (bool) $property->ar_tour_enabled && $property->model3dUrl() !== null;
    }

    /** @return array{ar_enabled: bool, has_3d_model: bool, is_available: bool} */
    public function statistics(Property $property): array
    {
        $hasModel = $property->model3dUrl() !== null;

        return ['ar_enabled' => (bool) $property->ar_tour_enabled, 'has_3d_model' => $hasModel, 'is_available' => (bool) $property->ar_tour_enabled && $hasModel];
    }

    /** @param array<string, mixed> $settings */
    public function enable(int|string $teamId, int|string $actorId, int|string $propertyId, array $settings = []): Property
    {
        return DB::transaction(function () use ($teamId, $propertyId, $settings): Property {
            $property = Property::query()->forTeam($teamId)->findOrFail($propertyId);
            if ($property->model3dUrl() === null) {
                throw ValidationException::withMessages(['ar_tour_enabled' => 'A valid 3D model is required before enabling AR tour.']);
            }
            $property->forceFill([
                'ar_tour_enabled' => true,
                'ar_model_scale' => $settings['ar_model_scale'] ?? $property->ar_model_scale,
                'ar_placement_guide' => $settings['ar_placement_guide'] ?? $property->ar_placement_guide,
                'ar_tour_settings' => array_merge($this->settings($property), array_diff_key($settings, array_flip(['ar_model_scale', 'ar_placement_guide']))),
            ])->save();

            return $property->fresh();
        });
    }

    public function disable(int|string $teamId, int|string $propertyId): Property
    {
        $property = Property::query()->forTeam($teamId)->findOrFail($propertyId);
        $property->forceFill(['ar_tour_enabled' => false])->save();

        return $property->fresh();
    }

    /** @param array<string, mixed> $settings */
    public function update(int|string $teamId, int|string $propertyId, array $settings): Property
    {
        $property = Property::query()->forTeam($teamId)->findOrFail($propertyId);
        $property->forceFill([
            'ar_model_scale' => $settings['ar_model_scale'] ?? $property->ar_model_scale,
            'ar_placement_guide' => $settings['ar_placement_guide'] ?? $property->ar_placement_guide,
            'ar_tour_settings' => array_merge($this->settings($property), array_diff_key($settings, array_flip(['ar_model_scale', 'ar_placement_guide']))),
        ])->save();

        return $property->fresh();
    }

    /** @return array<string, mixed> */
    private function settings(Property $property): array
    {
        return array_merge(['ar_modes' => ['webxr', 'scene-viewer', 'quick-look'], 'enable_controls' => true, 'auto_rotate' => true, 'shadow_intensity' => 1, 'camera_orbit' => '0deg 75deg 2.5m', 'min_camera_orbit' => 'auto auto 1m', 'max_camera_orbit' => 'auto auto 10m', 'interaction_prompt' => 'auto'], (array) $property->ar_tour_settings);
    }
}
