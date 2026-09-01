<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingLivewire\Components;

use Liberu\RealEstate\Marketing\Models\NewsArticle;

final class LatestNews extends NewsArticleList
{
    public function render(): mixed
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $articles = NewsArticle::query()->published()->featured()->visibleToTeam($teamId)->latest('published_at')->paginate(25);

        return view('real-estate-marketing-livewire::news-article-list', ['articles' => $articles]);
    }
}
