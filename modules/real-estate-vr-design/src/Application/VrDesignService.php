<?php

declare(strict_types=1);

namespace Liberu\RealEstate\VrDesign\Application;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\VrDesign\Models\VrDesign;

final class VrDesignService
{
    public function styles(): array
    {
        return config('vr-design.styles', []);
    }

    public function furnitureCategories(): array
    {
        return config('vr-design.furniture_categories', []);
    }

    public function roomTypes(): array
    {
        return config('vr-design.room_types', []);
    }

    public function supportedDevices(): array
    {
        return config('vr-design.supported_devices', []);
    }

    public function create(int|string $teamId, int|string $userId, int|string $propertyId, string $name, array $designData, ?string $description = null, ?string $style = null, bool $isPublic = false): VrDesign
    {
        $this->property($teamId, $propertyId);
        if (trim($name) === '' || $designData === []) {
            throw ValidationException::withMessages(['name' => 'A design name is required.', 'design_data' => 'Design data is required.']);
        }
        if ($style !== null && ! array_key_exists($style, $this->styles())) {
            throw ValidationException::withMessages(['style' => 'The selected design style is not supported.']);
        }

        return DB::transaction(fn (): VrDesign => VrDesign::query()->create(['team_id' => $teamId, 'user_id' => $userId, 'property_id' => $propertyId, 'name' => trim($name), 'description' => $description, 'vr_provider' => config('vr-design.provider', 'mock'), 'design_data' => $designData, 'style' => $style, 'is_public' => $isPublic, 'furniture_items' => []]));
    }

    public function update(VrDesign $design, array $updates): VrDesign
    {
        $allowed = ['name', 'description', 'design_data', 'room_layout', 'furniture_items', 'materials', 'lighting', 'style', 'is_public'];
        $data = array_intersect_key($updates, array_flip($allowed));
        if (isset($data['style']) && $data['style'] !== null && ! array_key_exists($data['style'], $this->styles())) {
            throw ValidationException::withMessages(['style' => 'The selected design style is not supported.']);
        }
        $design->fill($data)->save();
        $this->clearCache($design);

        return $design->refresh();
    }

    public function delete(VrDesign $design): bool
    {
        $disk = $this->disk();
        if ($design->thumbnail_path) {
            $disk->delete($design->thumbnail_path);
        }
        if ($design->vr_scene_url) {
            $disk->delete($design->vr_scene_url);
        }
        $this->clearCache($design);

        return (bool) $design->delete();
    }

    /** @return array<int, array<string, mixed>> */
    public function propertyDesigns(int|string $teamId, int|string $propertyId, bool $publicOnly = false): array
    {
        $this->property($teamId, $propertyId);
        $query = VrDesign::query()->forTeam($teamId)->where('property_id', $propertyId);
        if ($publicOnly) {
            $query->public();
        }

        return $query->latest()->get()->toArray();
    }

    public function find(int|string $teamId, int|string $designId): VrDesign
    {
        return VrDesign::query()->forTeam($teamId)->whereKey($designId)->firstOrFail();
    }

    public function addFurniture(VrDesign $design, string $category, string $type, array $position, array $rotation = [0, 0, 0], array $scale = [1, 1, 1], array $material = []): VrDesign
    {
        if (trim($category) === '' || trim($type) === '' || $position === []) {
            throw ValidationException::withMessages(['type' => 'Furniture type and position are required.']);
        }
        $items = $design->furniture_items ?? [];
        $items[] = ['id' => uniqid('furniture_', true), 'category' => $category, 'type' => $type, 'position' => array_values($position), 'rotation' => array_values($rotation), 'scale' => array_values($scale), 'material' => $material, 'created_at' => now()->toIso8601String()];
        $design->furniture_items = $items;
        $design->save();
        $this->clearCache($design);

        return $design->refresh();
    }

    public function removeFurniture(VrDesign $design, string $furnitureId): VrDesign
    {
        $design->furniture_items = array_values(array_filter($design->furniture_items ?? [], fn (array $item): bool => ($item['id'] ?? null) !== $furnitureId));
        $design->save();
        $this->clearCache($design);

        return $design->refresh();
    }

    public function cloneDesign(VrDesign $design, int|string $teamId, int|string $userId, string $name): VrDesign
    {
        $clone = $design->replicate();
        $clone->team_id = $teamId;
        $clone->user_id = $userId;
        $clone->name = trim($name);
        $clone->view_count = 0;
        $clone->is_template = false;
        $clone->save();

        return $clone;
    }

    /** @return array<int, array<string, mixed>> */
    public function templates(int|string $teamId, ?string $style = null): array
    {
        $query = VrDesign::query()->forTeam($teamId)->templates()->public();
        if ($style !== null) {
            $query->byStyle($style);
        }

        return $query->latest('view_count')->get()->toArray();
    }

    public function uploadThumbnail(VrDesign $design, UploadedFile $file): VrDesign
    {
        $disk = $this->disk();
        if ($design->thumbnail_path) {
            $disk->delete($design->thumbnail_path);
        }
        $design->thumbnail_path = $file->store(config('vr-design.storage.thumbnail_path', 'vr-designs/thumbnails'), config('vr-design.storage.disk', 'public'));
        $design->save();
        $this->clearCache($design);

        return $design->refresh();
    }

    /** @return array<string, mixed> */
    public function export(VrDesign $design): array
    {
        return ['id' => $design->id, 'name' => $design->name, 'description' => $design->description, 'style' => $design->style, 'design_data' => $design->design_data, 'room_layout' => $design->room_layout, 'furniture_items' => $design->furniture_items, 'materials' => $design->materials, 'lighting' => $design->lighting, 'metadata' => ['provider' => $design->vr_provider, 'created_at' => $design->created_at, 'updated_at' => $design->updated_at]];
    }

    private function property(int|string $teamId, int|string $propertyId): Property
    {
        return Property::query()->forTeam($teamId)->whereKey($propertyId)->firstOrFail();
    }

    private function disk(): Filesystem
    {
        return Storage::disk(config('vr-design.storage.disk', 'public'));
    }

    private function clearCache(VrDesign $design): void
    {
        if (config('vr-design.cache.enabled', true)) {
            Cache::forget(config('vr-design.cache.prefix', 'vr_design_').$design->getKey());
        }
    }
}
