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
use App\Models\SavedReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->dashboardData($request)]);
    }

    public function pipeline(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->pipelineData($request)]);
    }

    public function run(Request $request, int $savedReport): JsonResponse
    {
        $report = $this->savedReport($request, $savedReport);
        $data = $report->type === 'dashboard'
            ? $this->dashboardData($request)
            : $this->pipelineData($request, $report->filters ?? []);

        return response()->json([
            'data' => $data,
            'meta' => ['report' => $report, 'chart' => $this->chartData($data)],
        ]);
    }

    public function export(Request $request, int $savedReport): StreamedResponse
    {
        $report = $this->savedReport($request, $savedReport);
        $data = $report->type === 'dashboard'
            ? $this->dashboardData($request)
            : $this->pipelineData($request, $report->filters ?? []);

        return response()->streamDownload(function () use ($data) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['section', 'metric', 'value']);
            foreach ($data as $section => $values) {
                foreach (is_array($values) ? $values : [$section => $values] as $metric => $value) {
                    fputcsv($output, [is_array($values) ? $section : 'summary', $metric, $value]);
                }
            }
            fclose($output);
        }, "report-{$report->id}.csv", ['Content-Type' => 'text/csv']);
    }

    private function dashboardData(Request $request): array
    {
        $teamId = $this->teamId($request);

        return [
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
        ];
    }

    private function pipelineData(Request $request, array $filters = []): array
    {
        $teamId = $this->teamId($request);

        return [
            'properties' => $this->grouped($this->filtered(Property::where('team_id', $teamId), $filters), 'status'),
            'offers' => $this->grouped($this->filtered(Offer::where('team_id', $teamId), $filters), 'status'),
            'sales_progression' => $this->grouped($this->filtered(SalesProgression::where('team_id', $teamId), $filters), 'stage'),
            'valuations' => $this->grouped($this->filtered(PropertyValuation::where('team_id', $teamId), $filters), 'status'),
            'maintenance' => $this->grouped($this->filtered(MaintenanceRequest::where('team_id', $teamId), $filters), 'status'),
        ];
    }

    private function grouped(Builder $query, string $column): array
    {
        return $query->selectRaw("$column as label, count(*) as total")
            ->groupBy($column)
            ->pluck('total', 'label')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    private function filtered(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to));
    }

    private function chartData(array $data): array
    {
        return collect($data)
            ->filter(fn ($values) => is_array($values))
            ->map(fn (array $values, string $section) => [
                'label' => str_replace('_', ' ', $section),
                'labels' => array_keys($values),
                'values' => array_values($values),
            ])->values()->all();
    }

    private function savedReport(Request $request, int $id): SavedReport
    {
        return SavedReport::query()
            ->where('team_id', $this->teamId($request))
            ->where(fn (Builder $query) => $query
                ->where('is_shared', true)
                ->orWhere('created_by', $request->user()->id))
            ->findOrFail($id);
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
