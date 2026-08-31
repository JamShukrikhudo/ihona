<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use InvalidArgumentException;
use Liberu\RealEstate\Properties\Models\Property;

final class GeneratePropertyQrCode
{
    private const GOOGLE_CHART_BASE = 'https://chart.googleapis.com/chart';

    /** @return array{url: string, property_url: string, property_id: int|string, property_title: string|null, size: int} */
    public function forProperty(Property $property, int $size = 200): array
    {
        $propertyUrl = url('/properties/'.$property->getKey());

        return [
            'url' => $this->forContent($propertyUrl, $size)['url'],
            'property_url' => $propertyUrl,
            'property_id' => $property->getKey(),
            'property_title' => $property->title,
            'size' => $size,
        ];
    }

    /** @return array{url: string, content: string, size: int} */
    public function forContent(string $content, int $size = 200): array
    {
        if (trim($content) === '') {
            throw new InvalidArgumentException('QR code content cannot be empty.');
        }
        if ($size < 50 || $size > 1000) {
            throw new InvalidArgumentException('QR code size must be between 50 and 1000 pixels.');
        }

        return [
            'url' => self::GOOGLE_CHART_BASE.'?'.http_build_query([
                'cht' => 'qr', 'chs' => $size.'x'.$size, 'chl' => $content, 'choe' => 'UTF-8',
            ]),
            'content' => $content,
            'size' => $size,
        ];
    }
}
