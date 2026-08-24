<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ViewingsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Viewings\Application\CancelViewing;
use Liberu\RealEstate\Viewings\Application\ConfirmViewing;
use Liberu\RealEstate\Viewings\Application\MarkViewingNoShow;
use Liberu\RealEstate\Viewings\Models\Viewing;
use Livewire\Component;

final class ViewingList extends Component
{
    public string $search = '';

    public ?string $error = null;

    public function confirmViewing(int $viewingId, ConfirmViewing $confirm): void
    {
        $this->error = null;
        $this->runForCurrentTeam($viewingId, fn (Viewing $viewing, int|string $teamId): Viewing => $confirm->handle($viewing, $teamId));
    }

    public function cancelViewing(int $viewingId, CancelViewing $cancel): void
    {
        $this->error = null;
        $this->runForCurrentTeam($viewingId, fn (Viewing $viewing, int|string $teamId): Viewing => $cancel->handle($viewing, $teamId));
    }

    public function markNoShow(int $viewingId, MarkViewingNoShow $noShow): void
    {
        $this->error = null;
        $this->runForCurrentTeam($viewingId, fn (Viewing $viewing, int|string $teamId): Viewing => $noShow->handle($viewing, $teamId));
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $viewings = $teamId === null ? collect() : Viewing::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('subject', 'like', '%'.$this->search.'%'))->latest('starts_at')->paginate(20);

        return view('real-estate-viewings-livewire::viewing-list', ['viewings' => $viewings]);
    }

    /** @param callable(Viewing, int|string): Viewing $action */
    private function runForCurrentTeam(int $viewingId, callable $action): void
    {
        $teamId = auth()->user()?->current_team_id;
        if ($teamId === null) {
            $this->error = 'A team context is required.';

            return;
        }

        try {
            $action(Viewing::query()->forTeam($teamId)->findOrFail($viewingId), $teamId);
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
        }
    }
}
