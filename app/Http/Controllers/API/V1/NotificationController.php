<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\NotificationDelivery;
use App\Models\NotificationPreference;
use App\Services\NotificationDispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->notifications()
            ->when($request->boolean('unread'), fn ($query) => $query->whereNull('read_at'))
            ->when($request->filled('type'), fn ($query) => $query->where('data->type', $request->string('type')));

        $paginator = $query->paginate(min(max($request->integer('per_page', 20), 1), 100));

        return response()->json([
            'data' => $paginator->items(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ],
        ]);
    }

    public function read(Request $request, string $notification): JsonResponse
    {
        $record = $request->user()->notifications()->findOrFail($notification);
        $record->markAsRead();

        return response()->json(['data' => $record->fresh()]);
    }

    public function readAll(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['data' => ['marked_read' => $count]]);
    }

    public function preferences(Request $request): JsonResponse
    {
        $preference = NotificationPreference::where('team_id', $this->teamId($request))
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json(['data' => $this->preferenceData($preference)]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['required', 'distinct', Rule::in(NotificationDispatcher::CHANNELS)],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^\\+?[0-9 ()-]{7,50}$/'],
            'push_tokens' => ['nullable', 'array', 'max:20'],
            'push_tokens.*' => ['required', 'string', 'max:2048'],
            'event_preferences' => ['nullable', 'array'],
            'event_preferences.*' => ['boolean'],
        ]);

        $preference = NotificationPreference::updateOrCreate([
            'team_id' => $this->teamId($request),
            'user_id' => $request->user()->id,
        ], $validated);

        return response()->json(['data' => $this->preferenceData($preference)]);
    }

    public function deliveries(Request $request): JsonResponse
    {
        $query = NotificationDelivery::where('team_id', $this->teamId($request))
            ->where('user_id', $request->user()->id)
            ->when($request->filled('channel'), fn ($query) => $query->where('channel', $request->string('channel')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('event_type'), fn ($query) => $query->where('event_type', $request->string('event_type')));

        return response()->json($query->latest()->paginate(
            min(max($request->integer('per_page', 20), 1), 100)
        ));
    }

    private function teamId(Request $request): int
    {
        return (int) $request->user()->current_team_id;
    }

    private function preferenceData(?NotificationPreference $preference): array
    {
        return [
            'channels' => $preference?->channels ?? ['in_app'],
            'phone' => $preference?->phone,
            'push_token_count' => count($preference?->push_tokens ?? []),
            'event_preferences' => $preference?->event_preferences ?? [],
        ];
    }
}
