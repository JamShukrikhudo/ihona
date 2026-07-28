<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\AgencyTask;
use App\Models\Appointment;
use App\Models\Inspection;
use App\Models\MaintenanceRequest;
use App\Models\PropertyValuation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
            'types' => ['nullable', 'array'],
            'types.*' => ['in:viewing,valuation,inspection,maintenance,task'],
            'staff_id' => ['nullable', 'integer'],
        ]);
        $teamId = (int) $request->user()->current_team_id;
        $start = isset($validated['start']) ? Carbon::parse($validated['start'])->startOfDay() : now()->startOfMonth();
        $end = isset($validated['end']) ? Carbon::parse($validated['end'])->endOfDay() : now()->endOfMonth();
        $types = collect($validated['types'] ?? ['viewing', 'valuation', 'inspection', 'maintenance', 'task']);
        $staffId = $validated['staff_id'] ?? null;
        $events = collect();

        if ($types->contains('viewing')) {
            $events = $events->concat(Appointment::where('team_id', $teamId)
                ->whereBetween('appointment_date', [$start, $end])
                ->when($staffId, fn ($query) => $query->where(fn ($query) => $query->where('agent_id', $staffId)->orWhere('staff_id', $staffId)))
                ->get()->map(fn ($record) => $this->event('viewing', $record->appointment_id, $record->name ?: 'Property viewing', $record->appointment_date, null, false, $record->status, $record->property_id)));
        }
        if ($types->contains('valuation')) {
            $events = $events->concat(PropertyValuation::where('team_id', $teamId)
                ->whereBetween('valuation_date', [$start->toDateString(), $end->toDateString()])
                ->when($staffId, fn ($query) => $query->where('user_id', $staffId))
                ->get()->map(fn ($record) => $this->event('valuation', $record->id, ucfirst($record->valuation_type).' valuation', $record->valuation_date, null, true, $record->status, $record->property_id)));
        }
        if ($types->contains('inspection')) {
            $events = $events->concat(Inspection::where('team_id', $teamId)
                ->whereBetween('scheduled_at', [$start, $end])
                ->when($staffId, fn ($query) => $query->where('assigned_to', $staffId))
                ->get()->map(fn ($record) => $this->event('inspection', $record->id, ucfirst(str_replace('_', ' ', $record->type)).' inspection', $record->scheduled_at, $record->completed_at, false, $record->status, $record->property_id)));
        }
        if ($types->contains('maintenance')) {
            $events = $events->concat(MaintenanceRequest::where('team_id', $teamId)
                ->whereBetween('requested_date', [$start->toDateString(), $end->toDateString()])
                ->get()->map(fn ($record) => $this->event('maintenance', $record->id, $record->title, $record->requested_date, $record->completed_at, true, $record->status, $record->property_id)));
        }
        if ($types->contains('task')) {
            $events = $events->concat(AgencyTask::where('team_id', $teamId)
                ->whereBetween('due_at', [$start, $end])
                ->when($staffId, fn ($query) => $query->where('assigned_to', $staffId))
                ->get()->map(fn ($record) => $this->event('task', $record->id, $record->title, $record->due_at, $record->completed_at, false, $record->status)));
        }

        return response()->json([
            'data' => $events->sortBy('start')->values(),
            'meta' => ['start' => $start->toIso8601String(), 'end' => $end->toIso8601String(), 'total' => $events->count()],
        ]);
    }

    private function event(
        string $type,
        int $id,
        string $title,
        mixed $start,
        mixed $end,
        bool $allDay,
        ?string $status,
        ?int $propertyId = null,
    ): array {
        return [
            'id' => "$type:$id",
            'record_id' => $id,
            'type' => $type,
            'title' => $title,
            'start' => $start?->toIso8601String(),
            'end' => $end?->toIso8601String(),
            'all_day' => $allDay,
            'status' => $status,
            'property_id' => $propertyId,
        ];
    }
}
