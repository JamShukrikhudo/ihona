<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Core\Application\CreateCalendarEntry;
use Liberu\RealEstate\Core\Models\CalendarEntry;
use Liberu\RealEstate\CoreApi\Http\Resources\CalendarEntryResource;

final class CalendarEntryController
{
    public function store(Request $request, CreateCalendarEntry $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['type' => ['required', 'in:meeting,reminder'], 'title' => ['required', 'string', 'max:255'], 'starts_at' => ['required', 'date'], 'ends_at' => ['nullable', 'date'], 'status' => ['nullable', 'string'], 'attendee_user_ids' => ['nullable', 'array'], 'recurrence' => ['nullable', 'array']]);

        return (new CalendarEntryResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function update(Request $request, CalendarEntry $calendarEntry): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $calendarEntry->team_id, 404);
        $calendarEntry->update($request->validate(['status' => ['sometimes', 'string'], 'title' => ['sometimes', 'string', 'max:255']]));

        return (new CalendarEntryResource($calendarEntry->refresh()))->response();
    }

    public function destroy(Request $request, CalendarEntry $calendarEntry): Response
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $calendarEntry->team_id, 404);
        $calendarEntry->delete();

        return response()->noContent();
    }
}
