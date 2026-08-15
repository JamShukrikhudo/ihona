<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves a property's media to the public site.
 *
 * The V1 media API stores uploads on the `local` disk, which is also the
 * column default, and `local` has no public URL — so every photograph the app
 * itself writes had no address the browser could fetch. The gallery either
 * rendered a path that 404s or dropped the row entirely.
 *
 * `is_public` is the agency's own switch and this route is the only thing
 * between a private floor plan and anyone who can count, so it is checked here
 * rather than trusted from the caller.
 */
class PropertyMediaController extends Controller
{
    public function __invoke(int $property, int $medium): StreamedResponse
    {
        $image = Image::query()
            ->where('image_id', $medium)
            ->where('property_id', $property)
            ->where('is_public', true)
            ->whereNotNull('file_path')
            ->firstOrFail();

        $disk = Storage::disk($image->disk ?: 'public');

        abort_unless($disk->exists($image->file_path), 404);

        return $disk->response($image->file_path, null, [
            'Content-Type' => $image->mime_type ?: 'application/octet-stream',
            // Media is replaced by uploading a new row, so the URL is stable
            // for as long as the file is.
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
