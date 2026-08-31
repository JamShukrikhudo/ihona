<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Valuations\Application\CompleteValuation;
use Liberu\RealEstate\Valuations\Application\ConvertValuation;
use Liberu\RealEstate\Valuations\Application\ScheduleValuation;
use Liberu\RealEstate\Valuations\Models\Valuation;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ValuationList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public ?string $scheduledAt = null;

    public ?float $valuedAmount = null;

    public string $conversionType = '';

    public ?string $error = null;

    public function scheduleValuation(int $valuationId, ScheduleValuation $schedule): void
    {
        $this->runForCurrentTeam($valuationId, function (Valuation $valuation, int|string $teamId) use ($schedule): void {
            $this->validate(['scheduledAt' => ['required', 'date', 'after:now']]);
            $schedule->handle($valuation, $teamId, $this->scheduledAt);
        });
    }

    public function completeValuation(int $valuationId, CompleteValuation $complete): void
    {
        $this->runForCurrentTeam($valuationId, function (Valuation $valuation, int|string $teamId) use ($complete): void {
            $this->validate(['valuedAmount' => ['required', 'numeric', 'min:0']]);
            $complete->handle($valuation, $teamId, ['valued_amount' => $this->valuedAmount]);
        });
    }

    public function convertValuation(int $valuationId, ConvertValuation $convert): void
    {
        $this->runForCurrentTeam($valuationId, function (Valuation $valuation, int|string $teamId) use ($convert): void {
            $this->validate(['conversionType' => ['required', 'string', 'max:80']]);
            $convert->handle($valuation, $teamId, ['type' => $this->conversionType]);
        });
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $valuations = $teamId === null ? collect() : Valuation::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('subject', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-valuations-livewire::valuation-list', ['valuations' => $valuations]);
    }

    /** @param callable(Valuation): void $action */
    private function runForCurrentTeam(int $valuationId, callable $action): void
    {
        $teamId = auth()->user()?->current_team_id;
        if ($teamId === null) {
            $this->error = 'A team context is required.';

            return;
        }

        try {
            $action(Valuation::query()->forTeam($teamId)->findOrFail($valuationId));
            $this->error = null;
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
        }
    }
}
