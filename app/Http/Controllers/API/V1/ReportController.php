<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\AgencyTask;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\MaintenanceRequest;
use App\Models\Offer;
use App\Models\Property;
use App\Models\PropertyValuation;
use App\Models\SalesProgression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $teamId = $this->teamId($request);

        return response()->json(['data' => [
            'properties' => Property::where('team_id', $teamId)->count(),
            'available_properties' => Property::where('team_id', $teamId)
                ->whereIn('status', ['available', 'to_let', 'approved', 'For Sale', 'For Rent'])->count(),
            'contacts' => Contact::where('team_id', $teamId)->count(),
            'open_offers' => Offer::where('team_id', $teamId)->where('status', 'pending')->count(),
            'upcoming_viewings' => Appointment::where('team_id', $teamId)
                ->where('appointment_date', '>=', now())->count(),
            'open_tasks' => AgencyTask::where('team_id', $teamId)
                ->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'open_maintenance' => MaintenanceRequest::where('team_id', $teamId)
                ->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'active_sales' => SalesProgression::where('team_id', $teamId)
                ->where('stage', '!=', 'completed')->count(),
            'valuations_due' => PropertyValuation::where('team_id', $teamId)
                ->where('valuation_date', '>=', now()->startOfDay())->count(),
        ]]);
    }

    public function pipeline(Request $request): JsonResponse
    {
        $teamId = $this->teamId($request);

        return response()->json(['data' => [
            'properties' => $this->grouped(Property::where('team_id', $teamId), 'status'),
            'offers' => $this->grouped(Offer::where('team_id', $teamId), 'status'),
            'sales_progression' => $this->grouped(SalesProgression::where('team_id', $teamId), 'stage'),
            'valuations' => $this->grouped(PropertyValuation::where('team_id', $teamId), 'status'),
            'maintenance' => $this->grouped(MaintenanceRequest::where('team_id', $teamId), 'status'),
        ]]);
    }

    private function grouped(Builder $query, string $column): array
    {
        return $query->selectRaw("$column as label, count(*) as total")
            ->groupBy($column)
            ->pluck('total', 'label')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    private function teamId(Request $request): int
    {
        $user = $request->user();
        $teamId = $user?->current_team_id;

        if (! $teamId || ! $user->allTeams()->contains('id', $teamId)) {
            throw ValidationException::withMessages([
                'team' => ['Select an organisation you belong to before using reports.'],
            ]);
        }

        return (int) $teamId;
    }
}
