<?php

declare(strict_types=1);

namespace Liberu\RealEstate\VrDesignApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\VrDesign\Application\VrDesignService;
use Liberu\RealEstate\VrDesignApi\Http\Resources\VrDesignResource;

final class VrDesignController
{
    public function styles(VrDesignService $service): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['styles' => $service->styles()]]);
    }

    public function furnitureCategories(VrDesignService $service): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['categories' => $service->furnitureCategories()]]);
    }

    public function roomTypes(VrDesignService $service): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['room_types' => $service->roomTypes()]]);
    }

    public function devices(VrDesignService $service): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['devices' => $service->supportedDevices()]]);
    }

    public function templates(Request $request, VrDesignService $service): JsonResponse
    {
        $templates = $service->templates($this->teamId($request), $request->input('style'));

        return response()->json(['success' => true, 'data' => ['templates' => $templates, 'count' => count($templates)]]);
    }

    public function indexForProperty(Request $request, VrDesignService $service, int|string $propertyId): JsonResponse
    {
        $designs = $service->propertyDesigns($this->teamId($request), $propertyId, $request->boolean('public_only'));

        return response()->json(['success' => true, 'data' => ['designs' => $designs, 'count' => count($designs)]]);
    }

    public function store(Request $request, VrDesignService $service, int|string $propertyId): JsonResponse
    {
        $validator = Validator::make($request->all(), ['name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:1000'], 'style' => ['nullable', 'string', Rule::in(array_keys($service->styles()))], 'design_data' => ['required', 'array'], 'is_public' => ['boolean']]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        $design = $service->create($this->teamId($request), $request->user()->getAuthIdentifier(), $propertyId, $request->string('name')->toString(), $request->array('design_data'), $request->input('description'), $request->input('style'), $request->boolean('is_public'));

        return (new VrDesignResource($design))->additional(['success' => true, 'message' => 'VR design created successfully'])->response()->setStatusCode(201);
    }

    public function show(Request $request, VrDesignService $service, int|string $designId): JsonResponse
    {
        $design = $service->find($this->teamId($request), $designId);
        $design->incrementViewCount();

        return (new VrDesignResource($design->refresh()))->additional(['success' => true])->response();
    }

    public function update(Request $request, VrDesignService $service, int|string $designId): JsonResponse
    {
        $design = $service->find($this->teamId($request), $designId);
        $validator = Validator::make($request->all(), ['name' => ['sometimes', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:1000'], 'style' => ['nullable', 'string', Rule::in(array_keys($service->styles()))], 'design_data' => ['sometimes', 'array'], 'room_layout' => ['sometimes', 'array'], 'furniture_items' => ['sometimes', 'array'], 'materials' => ['sometimes', 'array'], 'lighting' => ['sometimes', 'array'], 'is_public' => ['boolean']]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        return (new VrDesignResource($service->update($design, $request->all())))->additional(['success' => true, 'message' => 'VR design updated successfully'])->response();
    }

    public function destroy(Request $request, VrDesignService $service, int|string $designId): JsonResponse
    {
        $service->delete($service->find($this->teamId($request), $designId));

        return response()->json(['success' => true, 'message' => 'VR design deleted successfully']);
    }

    public function addFurniture(Request $request, VrDesignService $service, int|string $designId): JsonResponse
    {
        $validator = Validator::make($request->all(), ['category' => ['required', 'string'], 'type' => ['required', 'string'], 'position' => ['required', 'array'], 'position.*' => ['numeric'], 'rotation' => ['sometimes', 'array'], 'rotation.*' => ['numeric'], 'scale' => ['sometimes', 'array'], 'scale.*' => ['numeric'], 'material' => ['sometimes', 'array']]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        $design = $service->find($this->teamId($request), $designId);

        return (new VrDesignResource($service->addFurniture($design, $request->string('category')->toString(), $request->string('type')->toString(), $request->array('position'), $request->array('rotation') ?: [0, 0, 0], $request->array('scale') ?: [1, 1, 1], $request->array('material'))))->additional(['success' => true, 'message' => 'Furniture added successfully'])->response();
    }

    public function removeFurniture(Request $request, VrDesignService $service, int|string $designId, string $furnitureId): JsonResponse
    {
        $design = $service->find($this->teamId($request), $designId);

        return (new VrDesignResource($service->removeFurniture($design, $furnitureId)))->additional(['success' => true, 'message' => 'Furniture removed successfully'])->response();
    }

    public function cloneDesign(Request $request, VrDesignService $service, int|string $designId): JsonResponse
    {
        $validator = Validator::make($request->all(), ['name' => ['required', 'string', 'max:255']]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        $teamId = $this->teamId($request);
        $clone = $service->cloneDesign($service->find($teamId, $designId), $teamId, $request->user()->getAuthIdentifier(), $request->string('name')->toString());

        return (new VrDesignResource($clone))->additional(['success' => true, 'message' => 'Design cloned successfully'])->response()->setStatusCode(201);
    }

    public function uploadThumbnail(Request $request, VrDesignService $service, int|string $designId): JsonResponse
    {
        $validator = Validator::make($request->all(), ['thumbnail' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120']]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        return (new VrDesignResource($service->uploadThumbnail($service->find($this->teamId($request), $designId), $request->file('thumbnail'))))->additional(['success' => true, 'message' => 'Thumbnail uploaded successfully'])->response();
    }

    public function export(Request $request, VrDesignService $service, int|string $designId): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $service->export($service->find($this->teamId($request), $designId))]);
    }

    private function teamId(Request $request): int|string
    {
        abort_unless($request->user()?->current_team_id !== null, 403);

        return $request->user()->current_team_id;
    }
}
