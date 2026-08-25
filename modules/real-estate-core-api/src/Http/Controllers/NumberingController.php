<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\Core\Application\NextNumber;
use Liberu\RealEstate\CoreApi\Http\Resources\NumberingResource;

final class NumberingController
{
    public function next(Request $request, string $key, NextNumber $nextNumber): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $data = $request->validate(['prefix' => ['nullable', 'string', 'max:30'], 'padding' => ['sometimes', 'integer', 'between:1,20']]);

        return (new NumberingResource(['key' => $key, 'number' => $nextNumber->handle($teamId, $key, $data['prefix'] ?? null, $data['padding'] ?? 6)]))->response();
    }
}
