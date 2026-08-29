<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionLivewire\Components;

use Liberu\RealEstate\SalesProgression\Application\TransitionSalesProgression;
use Liberu\RealEstate\SalesProgression\Application\UpdateSalesProgressionSection;
use Liberu\RealEstate\SalesProgression\Domain\SalesProgressionSection;
use Liberu\RealEstate\SalesProgression\Domain\SalesProgressionStatus;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class SalesProgressionList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    /** @param array<string, mixed> $value */
    public function updateSection(int $progressionId, string $section, array $value): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $progression = SalesProgression::query()->forTeam($teamId)->findOrFail($progressionId);
        app(UpdateSalesProgressionSection::class)->handle($progression, $teamId, SalesProgressionSection::from($section), $value);
    }

    public function transition(int $progressionId, string $status): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $progression = SalesProgression::query()->forTeam($teamId)->findOrFail($progressionId);
        app(TransitionSalesProgression::class)->handle($progression, $teamId, SalesProgressionStatus::from($status));
    }

    public function render(): mixed
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $progressions = SalesProgression::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('subject', 'like', '%'.$this->search.'%'))->latest()->paginate(25);

        return view('real-estate-sales-progression-livewire::sales-progression-list', ['progressions' => $progressions]);
    }
}
