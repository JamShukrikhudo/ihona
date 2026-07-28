<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PropertyMediaProcessor
{
    private const MAX_DIMENSION = 2560;

    public function process(
        string $disk,
        string $path,
        bool $watermark,
        string $watermarkText,
        array $metadata = [],
    ): array {
        $contents = Storage::disk($disk)->get($path);
        $originalSize = strlen($contents);
        $imageInfo = @getimagesizefromstring($contents);

        if ($imageInfo === false || ! in_array($imageInfo['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return [
                'mime_type' => $imageInfo['mime'] ?? null,
                'file_size' => $originalSize,
                'metadata' => array_merge($metadata, [
                    'processing' => [
                        'optimized' => false,
                        'watermark_applied' => false,
                        'original_bytes' => $originalSize,
                        'processed_bytes' => $originalSize,
                        'checksum_sha256' => hash('sha256', $contents),
                        'processed_at' => now()->toIso8601String(),
                    ],
                ]),
            ];
        }

        $image = @imagecreatefromstring($contents);
        if ($image === false) {
            throw new RuntimeException('The uploaded image could not be decoded safely.');
        }

        try {
            $image = $this->normalizeOrientation($image, $disk, $path, $imageInfo['mime']);
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);
            $image = $this->resize($image);
            if ($watermark) {
                $this->watermark($image, $watermarkText);
            }

            $processed = $this->encode($image, $imageInfo['mime']);
            Storage::disk($disk)->put($path, $processed);

            return [
                'mime_type' => $imageInfo['mime'],
                'file_size' => strlen($processed),
                'metadata' => array_merge($metadata, [
                    'processing' => [
                        'optimized' => true,
                        'watermark_applied' => $watermark,
                        'original_width' => $originalWidth,
                        'original_height' => $originalHeight,
                        'width' => imagesx($image),
                        'height' => imagesy($image),
                        'original_bytes' => $originalSize,
                        'processed_bytes' => strlen($processed),
                        'checksum_sha256' => hash('sha256', $processed),
                        'processed_at' => now()->toIso8601String(),
                    ],
                ]),
            ];
        } finally {
            imagedestroy($image);
        }
    }

    private function normalizeOrientation(\GdImage $image, string $disk, string $path, string $mime): \GdImage
    {
        if ($mime !== 'image/jpeg' || ! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data(Storage::disk($disk)->path($path));
        $orientation = (int) ($exif['Orientation'] ?? 1);
        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };
        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        if ($rotated === false) {
            return $image;
        }
        imagedestroy($image);

        return $rotated;
    }

    private function resize(\GdImage $image): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, self::MAX_DIMENSION / max($width, $height));
        if ($scale === 1.0) {
            return $image;
        }

        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }

    private function watermark(\GdImage $image, string $text): void
    {
        $font = 5;
        $padding = 8;
        $availableCharacters = max(1, (int) floor((imagesx($image) - $padding * 2) / imagefontwidth($font)));
        $text = mb_substr(trim($text) ?: 'Agency', 0, min(80, $availableCharacters));
        $width = imagefontwidth($font) * strlen($text);
        $height = imagefontheight($font);
        $x = max($padding, imagesx($image) - $width - $padding * 2);
        $y = max($padding, imagesy($image) - $height - $padding * 2);

        imagealphablending($image, true);
        $background = imagecolorallocatealpha($image, 0, 0, 0, 55);
        $foreground = imagecolorallocatealpha($image, 255, 255, 255, 10);
        imagefilledrectangle($image, $x - $padding, $y - $padding, $x + $width + $padding, $y + $height + $padding, $background);
        imagestring($image, $font, $x, $y, $text, $foreground);
    }

    private function encode(\GdImage $image, string $mime): string
    {
        ob_start();
        $success = match ($mime) {
            'image/jpeg' => imagejpeg($image, null, 85),
            'image/png' => imagepng($image, null, 6),
            'image/webp' => imagewebp($image, null, 82),
            default => false,
        };
        $contents = ob_get_clean();

        if (! $success || ! is_string($contents)) {
            throw new RuntimeException('The optimized image could not be encoded.');
        }

        return $contents;
    }
}
