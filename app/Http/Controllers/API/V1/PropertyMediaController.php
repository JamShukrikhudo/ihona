<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Image;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyMediaController
{
    private const TYPES = [
        'image', 'floorplan', 'epc', 'video', 'virtual_tour', 'tour_360', 'document', 'brochure',
    ];

    public function index(Request $request, int $property): JsonResponse
    {
        $record = $this->property($request, $property);
        $query = $record->images()->where('team_id', $this->teamId($request));

        if ($request->filled('type')) {
            $request->validate(['type' => [Rule::in(self::TYPES)]]);
            $query->where('type', $request->string('type'));
        }

        return response()->json([
            'data' => $query->orderBy('sort_order')->orderBy('image_id')->get(),
        ]);
    }

    public function store(Request $request, int $property): JsonResponse
    {
        $record = $this->property($request, $property);
        $attributes = $request->validate([
            'file' => ['required', 'file', 'max:51200', 'mimetypes:image/jpeg,image/png,image/webp,application/pdf,video/mp4,video/webm'],
            'type' => ['required', Rule::in(self::TYPES)],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['sometimes', 'boolean'],
            'is_public' => ['sometimes', 'boolean'],
            'watermark' => ['sometimes', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ]);

        $file = $request->file('file');
        $teamId = $this->teamId($request);
        $path = $file->store("property-media/$teamId/{$record->id}", 'local');

        try {
            $media = DB::transaction(function () use ($attributes, $file, $path, $record, $teamId): Image {
                if ($attributes['is_primary'] ?? false) {
                    $record->images()->where('type', $attributes['type'])->update(['is_primary' => false]);
                }

                return Image::create([
                    ...collect($attributes)->except('file')->all(),
                    'team_id' => $teamId,
                    'property_id' => $record->id,
                    'disk' => 'local',
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'sort_order' => (int) $record->images()->max('sort_order') + 1,
                    'is_public' => $attributes['is_public'] ?? true,
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return response()->json(['data' => $media], 201);
    }

    public function show(Request $request, int $property, int $medium): JsonResponse
    {
        return response()->json(['data' => $this->media($request, $property, $medium)]);
    }

    public function update(Request $request, int $property, int $medium): JsonResponse
    {
        $media = $this->media($request, $property, $medium);
        $attributes = $request->validate([
            'type' => ['sometimes', Rule::in(self::TYPES)],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_primary' => ['sometimes', 'boolean'],
            'is_public' => ['sometimes', 'boolean'],
            'watermark' => ['sometimes', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($media, $attributes): void {
            if ($attributes['is_primary'] ?? false) {
                Image::query()
                    ->where('team_id', $media->team_id)
                    ->where('property_id', $media->property_id)
                    ->where('type', $attributes['type'] ?? $media->type)
                    ->whereKeyNot($media->getKey())
                    ->update(['is_primary' => false]);
            }
            $media->update($attributes);
        });

        return response()->json(['data' => $media->fresh()]);
    }

    public function reorder(Request $request, int $property): JsonResponse
    {
        $record = $this->property($request, $property);
        $attributes = $request->validate([
            'media' => ['required', 'array', 'min:1'],
            'media.*.id' => [
                'required',
                'integer',
                Rule::exists('images', 'image_id')
                    ->where('team_id', $this->teamId($request))
                    ->where('property_id', $record->id),
            ],
            'media.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($attributes, $record): void {
            foreach ($attributes['media'] as $item) {
                $record->images()->whereKey($item['id'])->update(['sort_order' => $item['sort_order']]);
            }
        });

        return response()->json([
            'data' => $record->images()->orderBy('sort_order')->orderBy('image_id')->get(),
        ]);
    }

    public function download(Request $request, int $property, int $medium): StreamedResponse
    {
        $media = $this->media($request, $property, $medium);

        abort_unless($media->file_path && Storage::disk($media->disk)->exists($media->file_path), 404);

        return Storage::disk($media->disk)->download($media->file_path, $media->file_name);
    }

    public function destroy(Request $request, int $property, int $medium): JsonResponse
    {
        $media = $this->media($request, $property, $medium);
        DB::transaction(function () use ($media): void {
            if ($media->file_path) {
                Storage::disk($media->disk)->delete($media->file_path);
            }
            $media->delete();
        });

        return response()->json(null, 204);
    }

    private function property(Request $request, int $property): Property
    {
        return Property::query()->where('team_id', $this->teamId($request))->findOrFail($property);
    }

    private function media(Request $request, int $property, int $medium): Image
    {
        $record = $this->property($request, $property);

        return $record->images()
            ->where('team_id', $this->teamId($request))
            ->whereKey($medium)
            ->firstOrFail();
    }

    private function teamId(Request $request): int
    {
        $user = $request->user();
        $teamId = $user->current_team_id;

        if (! $teamId || ! $user->allTeams()->contains('id', $teamId)) {
            throw ValidationException::withMessages(['team' => ['Select an organisation you belong to first.']]);
        }

        return (int) $teamId;
    }
}
