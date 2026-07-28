<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\AgencyTask;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\MaintenanceRequest;
use App\Models\Offer;
use App\Models\PortalIntegration;
use App\Models\PortalListing;
use App\Models\PortalSyncRun;
use App\Models\Property;
use App\Models\PropertyValuation;
use App\Models\SalesProgression;
use App\Models\SavedReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

    public function performance(Request $request): JsonResponse
    {
        $filters = $request->validate($this->performanceFilterRules($request));

        return response()->json(['data' => $this->performanceData($request, $filters)]);
    }

    public function run(Request $request, int $savedReport): JsonResponse
    {
        $report = $this->savedReport($request, $savedReport);
        $data = match ($report->type) {
            'dashboard' => $this->dashboardData($request),
            'performance' => $this->performanceData($request, $report->filters ?? []),
            default => $this->pipelineData($request, $report->filters ?? []),
        };

        return response()->json([
            'data' => $data,
            'meta' => ['report' => $report, 'chart' => $this->chartData($data)],
        ]);
    }

    public function export(Request $request, int $savedReport): StreamedResponse
    {
        $report = $this->savedReport($request, $savedReport);
        $data = match ($report->type) {
            'dashboard' => $this->dashboardData($request),
            'performance' => $this->performanceData($request, $report->filters ?? []),
            default => $this->pipelineData($request, $report->filters ?? []),
        };

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

    private function performanceData(Request $request, array $filters = []): array
    {
        $teamId = $this->teamId($request);
        $branchId = isset($filters['branch_id']) ? (int) $filters['branch_id'] : null;
        $members = DB::table('team_user')
            ->join('users', 'users.id', '=', 'team_user.user_id')
            ->where('team_user.team_id', $teamId)
            ->when($branchId, fn ($query) => $query->where('team_user.branch_id', $branchId))
            ->select(['users.id', 'users.name', 'team_user.branch_id'])
            ->get();
        $staffIds = $members->pluck('id')->map(fn ($id) => (int) $id)->all();

        $properties = $this->filtered(Property::where('team_id', $teamId), $filters)
            ->when($branchId, fn (Builder $query) => $query->whereIn('agent_id', $staffIds));
        $offers = $this->filtered(Offer::where('team_id', $teamId), $filters)
            ->when($branchId, fn (Builder $query) => $query->whereIn('negotiator_id', $staffIds));
        $viewings = $this->filtered(Appointment::where('team_id', $teamId), $filters)
            ->when($branchId, fn (Builder $query) => $query->whereIn('staff_id', $staffIds));
        $valuations = $this->filtered(PropertyValuation::where('team_id', $teamId), $filters)
            ->when($branchId, fn (Builder $query) => $query->whereIn('assigned_to', $staffIds));

        $propertyCounts = (clone $properties)->whereNotNull('agent_id')
            ->selectRaw('agent_id as staff_id, count(*) as total')->groupBy('agent_id')->pluck('total', 'staff_id');
        $offerCounts = (clone $offers)->whereNotNull('negotiator_id')
            ->selectRaw('negotiator_id as staff_id, count(*) as total')->groupBy('negotiator_id')->pluck('total', 'staff_id');
        $viewingCounts = (clone $viewings)->whereNotNull('staff_id')
            ->selectRaw('staff_id, count(*) as total')->groupBy('staff_id')->pluck('total', 'staff_id');
        $valuationCounts = (clone $valuations)->whereNotNull('assigned_to')
            ->selectRaw('assigned_to as staff_id, count(*) as total')->groupBy('assigned_to')->pluck('total', 'staff_id');

        $staffPerformance = $members->mapWithKeys(fn ($member) => [
            $member->name.' #'.$member->id => (int) ($propertyCounts[$member->id] ?? 0)
                + (int) ($offerCounts[$member->id] ?? 0)
                + (int) ($viewingCounts[$member->id] ?? 0)
                + (int) ($valuationCounts[$member->id] ?? 0),
        ])->all();

        $branches = Branch::where('team_id', $teamId)
            ->when($branchId, fn (Builder $query) => $query->whereKey($branchId))
            ->orderBy('name')
            ->get(['id', 'name']);
        $branchPerformance = $branches->mapWithKeys(function (Branch $branch) use (
            $members,
            $propertyCounts,
            $offerCounts,
            $viewingCounts,
            $valuationCounts,
        ) {
            $total = $members->where('branch_id', $branch->id)->sum(fn ($member) => (int) ($propertyCounts[$member->id] ?? 0)
                + (int) ($offerCounts[$member->id] ?? 0)
                + (int) ($viewingCounts[$member->id] ?? 0)
                + (int) ($valuationCounts[$member->id] ?? 0));

            return [$branch->name.' #'.$branch->id => $total];
        })->all();

        $campaigns = $this->filtered(EmailCampaign::where('team_id', $teamId), $filters);
        $portalIntegrations = PortalIntegration::where('team_id', $teamId)
            ->when($branchId, fn (Builder $query) => $query->where('branch_id', $branchId));
        $integrationIds = (clone $portalIntegrations)->pluck('id');
        $portalListings = $this->filtered(PortalListing::where('team_id', $teamId), $filters)
            ->when($branchId, fn (Builder $query) => $query->whereIn('portal_integration_id', $integrationIds));
        $portalRuns = $this->filtered(PortalSyncRun::where('team_id', $teamId), $filters)
            ->when($branchId, fn (Builder $query) => $query->whereIn('portal_integration_id', $integrationIds));

        return [
            'instructions' => $this->grouped(clone $properties, 'status'),
            'offers' => $this->grouped(clone $offers, 'status'),
            'viewings' => $this->grouped(clone $viewings, 'status'),
            'valuations' => $this->grouped(clone $valuations, 'status'),
            'staff_performance' => $staffPerformance,
            'branch_performance' => $branchPerformance,
            'marketing_performance' => [
                'campaigns' => (clone $campaigns)->count(),
                'recipients' => (int) (clone $campaigns)->sum('recipients_count'),
                'delivered' => (int) (clone $campaigns)->sum('delivered_count'),
                'opened' => (int) (clone $campaigns)->sum('opened_count'),
                'clicked' => (int) (clone $campaigns)->sum('clicked_count'),
            ],
            'portal_statistics' => [
                'integrations' => (clone $portalIntegrations)->count(),
                'active_integrations' => (clone $portalIntegrations)->where('active', true)->count(),
                'listings' => (clone $portalListings)->count(),
                'published_listings' => (clone $portalListings)->where('status', 'published')->count(),
                'sync_runs' => (clone $portalRuns)->count(),
                'failed_items' => (int) (clone $portalRuns)->sum('failed'),
            ],
        ];
    }

    private function performanceFilterRules(Request $request): array
    {
        return [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where('team_id', $this->teamId($request)),
            ],
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
