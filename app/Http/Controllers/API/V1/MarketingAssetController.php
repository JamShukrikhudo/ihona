<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\PropertyBrochureService;
use App\Services\QRCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MarketingAssetController extends Controller
{
    public function __construct(
        private readonly PropertyBrochureService $brochures,
        private readonly QRCodeService $qrCodes,
    ) {}

    public function brochure(Request $request, int $property): Response
    {
        $record = $this->property($request, $property);
        $options = $request->validate([
            'template' => ['sometimes', 'in:standard,luxury,minimal'],
            'include_floor_plan' => ['sometimes', 'boolean'],
            'include_map' => ['sometimes', 'boolean'],
            'include_epc' => ['sometimes', 'boolean'],
        ]);

        return response($this->brochures->generateHtmlBrochure($record, $options))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function windowCard(Request $request, int $property): Response
    {
        return response($this->brochures->generateWindowCard($this->property($request, $property)))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function qrCode(Request $request, int $property): JsonResponse
    {
        $validated = $request->validate(['size' => ['sometimes', 'integer', 'between:50,1000']]);

        return response()->json([
            'data' => $this->qrCodes->generatePropertyQRCodeData(
                $this->property($request, $property),
                $validated['size'] ?? 200,
            ),
        ]);
    }

    public function social(Request $request, int $property): JsonResponse
    {
        $record = $this->property($request, $property);
        $url = url("/properties/{$record->id}");

        return response()->json(['data' => [
            'property_id' => $record->id,
            'headline' => $record->title,
            'caption' => trim("{$record->title} in {$record->location}. {$record->bedrooms} bedrooms, {$record->bathrooms} bathrooms."),
            'price' => $record->price,
            'url' => $url,
            'share_links' => [
                'facebook' => 'https://www.facebook.com/sharer/sharer.php?u='.urlencode($url),
                'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url='.urlencode($url),
            ],
        ]]);
    }

    private function property(Request $request, int $id): Property
    {
        return Property::query()
            ->where('team_id', $request->user()->current_team_id)
            ->findOrFail($id);
    }
}
