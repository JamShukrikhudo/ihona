<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Matching\Application\RankPropertyRecommendations;
use Livewire\Component;

final class PropertyRecommendations extends Component
{
    /** @var array<string, mixed> */
    public array $criteria = [];

    /** @var list<array<string, mixed>> */
    public array $candidates = [];

    /** @var list<array<string, mixed>> */
    public array $recommendations = [];

    /** @var list<int|string> */
    public array $excludedIds = [];

    public int $limit = 6;

    public ?string $error = null;

    /** @param array<string, mixed> $criteria @param list<array<string, mixed>> $candidates @param list<int|string> $excludedIds */
    public function mount(array $criteria = [], array $candidates = [], array $excludedIds = []): void
    {
        $this->criteria = $criteria;
        $this->candidates = $candidates;
        $this->excludedIds = $excludedIds;
        $this->updateRecommendations();
    }

    public function updateRecommendations(?RankPropertyRecommendations $rank = null): void
    {
        try {
            $this->recommendations = ($rank ?? app(RankPropertyRecommendations::class))->handle($this->criteria, $this->candidates, $this->limit, $this->excludedIds);
            $this->error = null;
        } catch (\Throwable $exception) {
            $this->recommendations = [];
            $this->error = $exception->getMessage();
        }
    }

    public function loadMore(): void
    {
        $this->limit = min(100, $this->limit + 6);
        $this->updateRecommendations();
    }

    public function render(): View
    {
        return view('real-estate-matching-livewire::property-recommendations');
    }
}
