<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\MediaAndDocuments\Application\CreateHomeReport;
use Liberu\RealEstate\MediaAndDocuments\Application\UploadHomeReportFile;
use Liberu\RealEstate\MediaAndDocuments\Models\HomeReport;
use Liberu\RealEstate\MediaAndDocumentsApi\Http\Resources\HomeReportResource;
use Liberu\RealEstate\Properties\Models\Property;

final class HomeReportController
{
    public function index(Request $r, Property $property): JsonResponse
    {
        $team = $r->user()?->current_team_id;
        abort_unless((string) $team === (string) $property->team_id, 404);

        return HomeReportResource::collection(HomeReport::query()->forTeam($team)->where('property_id', $property->id)->latest()->get())->response();
    }

    public function store(Request $r, Property $property, CreateHomeReport $create): JsonResponse
    {
        $team = $r->user()?->current_team_id;
        abort_unless((string) $team === (string) $property->team_id, 404);
        $data = $r->validate(['survey_date' => 'nullable|date', 'expiry_date' => 'nullable|date', 'energy_band' => 'nullable|string', 'energy_current_score' => 'nullable|integer', 'energy_potential_score' => 'nullable|integer', 'property_condition' => 'nullable|string']);

        return (new HomeReportResource($create->handle($property, $r->user()->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function upload(Request $r, HomeReport $homeReport, UploadHomeReportFile $upload): JsonResponse
    {
        $team = $r->user()?->current_team_id;
        abort_unless((string) $team === (string) $homeReport->team_id, 404);
        $r->validate(['file' => 'required|file|mimes:pdf']);

        return (new HomeReportResource($upload->handle($homeReport, $team, $r->file('file'))))->response();
    }
}
