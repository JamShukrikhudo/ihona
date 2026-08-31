<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\Properties\Application\ConfigurePropertyArTour;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\PropertiesApi\Http\Resources\PropertyResource;

final class PropertyArTourController
{
    /** PropertyResource remains the canonical property serialization boundary for this adapter. */
    public function config(Property $property, ConfigurePropertyArTour $arTour): JsonResponse
    {
        abort_unless($arTour->available($property), 404);

        return response()->json(['available' => true, 'config' => $arTour->configuration($property), 'property' => ['id' => $property->getKey(), 'title' => $property->title, 'location' => $property->address]]);
    }

    public function availability(Property $property, ConfigurePropertyArTour $arTour): JsonResponse
    {
        return response()->json(['available' => $arTour->available($property), 'stats' => $arTour->statistics($property)]);
    }

    public function enable(Request $request, Property $property, ConfigurePropertyArTour $arTour): JsonResponse
    {
        $this->ensureTeamProperty($request, $property);
        $updated = $arTour->enable($request->user()->current_team_id, $request->user()->getAuthIdentifier(), $property->getKey(), $request->validate($this->settingsRules()));

        return response()->json(['success' => true, 'message' => 'AR tour enabled successfully.', 'config' => $arTour->configuration($updated)]);
    }

    public function disable(Request $request, Property $property, ConfigurePropertyArTour $arTour): JsonResponse
    {
        $this->ensureTeamProperty($request, $property);
        $updated = $arTour->disable($request->user()->current_team_id, $property->getKey());

        return response()->json(['success' => true, 'message' => 'AR tour disabled successfully.', 'config' => $arTour->configuration($updated)]);
    }

    public function updateSettings(Request $request, Property $property, ConfigurePropertyArTour $arTour): JsonResponse
    {
        $this->ensureTeamProperty($request, $property);
        $updated = $arTour->update($request->user()->current_team_id, $property->getKey(), $request->validate($this->settingsRules()));

        return response()->json(['success' => true, 'message' => 'AR tour settings updated successfully.', 'config' => $arTour->configuration($updated)]);
    }

    /** @return array<string, array<int, string>> */
    private function settingsRules(): array
    {
        return ['ar_modes' => ['sometimes', 'array', 'max:5'], 'ar_modes.*' => ['string', 'in:webxr,scene-viewer,quick-look'], 'enable_controls' => ['sometimes', 'boolean'], 'auto_rotate' => ['sometimes', 'boolean'], 'shadow_intensity' => ['sometimes', 'numeric', 'between:0,2'], 'camera_orbit' => ['sometimes', 'string', 'max:100'], 'min_camera_orbit' => ['sometimes', 'string', 'max:100'], 'max_camera_orbit' => ['sometimes', 'string', 'max:100'], 'interaction_prompt' => ['sometimes', 'string', 'max:50'], 'ar_model_scale' => ['sometimes', 'numeric', 'between:0.1,10'], 'ar_placement_guide' => ['sometimes', 'string', 'in:floor,wall,ceiling']];
    }

    private function ensureTeamProperty(Request $request, Property $property): void
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $property->team_id, 404);
    }
}
