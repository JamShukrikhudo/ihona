<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingLivewire\Components;

use Liberu\RealEstate\PortalsReporting\Application\RecordPortalMetric;
use Liberu\RealEstate\PortalsReporting\Application\TransitionPortalReport;
use Liberu\RealEstate\PortalsReporting\Domain\PortalMetric;
use Liberu\RealEstate\PortalsReporting\Domain\PortalReportStatus;
use Liberu\RealEstate\PortalsReporting\Models\PortalReport;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class PortalReportList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function recordMetric(int $reportId, string $metric, float|int|string $value): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $report = PortalReport::query()->forTeam($teamId)->findOrFail($reportId);
        app(RecordPortalMetric::class)->handle($report, $teamId, PortalMetric::from($metric), $value);
    }

    public function transition(int $reportId, string $status): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $report = PortalReport::query()->forTeam($teamId)->findOrFail($reportId);
        app(TransitionPortalReport::class)->handle($report, $teamId, PortalReportStatus::from($status));
    }

    public function render(): mixed
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $reports = PortalReport::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where(function ($query): void {
            $query->where('portal', 'like', '%'.$this->search.'%')->orWhere('report_type', 'like', '%'.$this->search.'%');
        }))->latest()->paginate(25);

        return view('real-estate-portals-reporting-livewire::portal-report-list', ['reports' => $reports]);
    }
}
