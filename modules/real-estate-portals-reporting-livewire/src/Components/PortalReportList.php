<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingLivewire\Components;

use Liberu\RealEstate\PortalsReporting\Models\PortalReport;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class PortalReportList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

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
