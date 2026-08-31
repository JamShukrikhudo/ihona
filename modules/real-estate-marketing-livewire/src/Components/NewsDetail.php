<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MarketingLivewire\Components;
use Liberu\RealEstate\Marketing\Models\NewsArticle;
use Livewire\Component;
final class NewsDetail extends Component {
    public string $slug;
    public function mount(string $slug): void { $this->slug = $slug; }
    public function render(): mixed { $teamId = auth()->user()?->current_team_id; abort_unless($teamId !== null, 403); $article = NewsArticle::query()->published()->visibleToTeam($teamId)->where('slug', $this->slug)->firstOrFail(); $related = NewsArticle::query()->published()->visibleToTeam($teamId)->where('id', '!=', $article->getKey())->latest('published_at')->limit(3)->get(); return view('real-estate-marketing-livewire::news-detail', compact('article', 'related')); }
}
