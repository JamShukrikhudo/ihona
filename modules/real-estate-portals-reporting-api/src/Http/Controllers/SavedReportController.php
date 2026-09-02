<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\PortalsReporting\Models\SavedReport;
use Liberu\RealEstate\PortalsReportingApi\Http\Resources\SavedReportResource;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class SavedReportController
{
    public function store(Request $r): JsonResponse
    {
        $u = $r->user();
        abort_unless($u?->current_team_id !== null, 403);
        $d = $r->validate(['name' => 'required|string|max:255', 'type' => 'required|string|max:100', 'filters' => 'nullable|array', 'is_shared' => 'boolean']);

        return (new SavedReportResource(SavedReport::create([...$d, 'team_id' => $u->current_team_id, 'created_by' => $u->getAuthIdentifier()])))->response()->setStatusCode(201);
    }

    public function show(Request $r, SavedReport $savedReport): JsonResponse
    {
        $u = $r->user();
        abort_unless((string) $u?->current_team_id === (string) $savedReport->team_id && $savedReport->visibleTo($u->getAuthIdentifier()), 404);

        return (new SavedReportResource($savedReport))->response();
    }

    public function run(Request $r, SavedReport $savedReport): JsonResponse
    {
        $this->authorize($r, $savedReport);

        return response()->json(['data' => ['report' => $savedReport->type, 'filters' => $savedReport->filters ?? [], 'saved_report' => (new SavedReportResource($savedReport))->resolve($r)]]);
    }

    public function export(Request $r, SavedReport $savedReport): StreamedResponse
    {
        $this->authorize($r, $savedReport);

        return response()->streamDownload(fn () => print "report\n".$savedReport->type."\n", 'report.csv', ['Content-Type' => 'text/csv']);
    }

    private function authorize(Request $r, SavedReport $s): void
    {
        $u = $r->user();
        abort_unless((string) $u?->current_team_id === (string) $s->team_id && $s->visibleTo($u->getAuthIdentifier()), 404);
    }
}
