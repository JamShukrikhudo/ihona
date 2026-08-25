<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Matching\Application\CalculateMatchScore;
use Liberu\RealEstate\Matching\Application\UpdateMatchProfileSection;
use Liberu\RealEstate\Matching\Domain\MatchProfileSection;
use Liberu\RealEstate\Matching\Models\MatchProfile;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class MatchProfileList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    /** @param array<string, mixed> $criteria @param array<string, mixed> $property @return array<string, mixed> */
    public function calculateScore(array $criteria, array $property, CalculateMatchScore $calculate): array
    {
        return $calculate->handle($criteria, $property);
    }

    /** @param array<string, mixed> $value */
    public function updateSection(int $profileId, string $section, array $value): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $profile = MatchProfile::query()->forTeam($teamId)->findOrFail($profileId);
        app(UpdateMatchProfileSection::class)->handle($profile, $teamId, MatchProfileSection::from($section), $value);
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $profiles = $teamId === null ? collect() : MatchProfile::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('subject', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-matching-livewire::match-profile-list', ['profiles' => $profiles]);
    }
}
