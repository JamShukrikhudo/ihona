<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\Core\Application\DefineStatus;
use Liberu\RealEstate\Core\Application\RecordAuditEntry;
use Liberu\RealEstate\Core\Application\SetTerminology;
use Liberu\RealEstate\Core\Models\AuditEntry;
use Liberu\RealEstate\Core\Models\StatusDefinition;
use Liberu\RealEstate\Core\Models\Terminology;
use Liberu\RealEstate\CoreApi\Http\Resources\CoreConfigurationResource;

final class CoreConfigurationController
{
    public function terminology(Request $request, string $key, SetTerminology $set): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $data = $request->validate(['value' => ['required', 'string', 'max:255'], 'locale' => ['sometimes', 'string', 'max:12']]);

        return (new CoreConfigurationResource($set->handle($teamId, $key, $data['value'], $data['locale'] ?? 'en')))->response();
    }

    public function status(Request $request, DefineStatus $define): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $data = $request->validate(['entity' => ['required', 'string', 'max:80'], 'key' => ['required', 'string', 'max:80'], 'label' => ['required', 'string', 'max:255'], 'active' => ['sometimes', 'boolean']]);

        return (new CoreConfigurationResource($define->handle($teamId, $data['entity'], $data['key'], $data['label'], $data['active'] ?? true)))->response();
    }

    public function audit(Request $request, RecordAuditEntry $record): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['event' => ['required', 'string', 'max:120'], 'subject_type' => ['nullable', 'string', 'max:160'], 'subject_id' => ['nullable', 'string', 'max:120'], 'metadata' => ['sometimes', 'array']]);

        return (new CoreConfigurationResource($record->handle($user->current_team_id, $user->getAuthIdentifier(), $data['event'], $data['subject_type'] ?? null, $data['subject_id'] ?? null, $data['metadata'] ?? [])))->response()->setStatusCode(201);
    }

    public function list(Request $request, string $kind): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $model = match ($kind) {
            'terminology' => Terminology::class,
            'statuses' => StatusDefinition::class,
            'audit' => AuditEntry::class,
            default => abort(404),
        };

        return CoreConfigurationResource::collection($model::query()->forTeam($teamId)->latest()->paginate(max(1, min($request->integer('page_size', 25), 100))))->response();
    }
}
