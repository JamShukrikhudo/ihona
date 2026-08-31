<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MarketingLivewire\Components;
use Liberu\RealEstate\Marketing\Models\NewsArticle;
use Livewire\Attributes\Validate;
use Livewire\Component;
class NewsArticleList extends Component {
    #[Validate('nullable|string|max:255')]
    public string $search = '';
    public function render(): mixed { $teamId = auth()->user()?->current_team_id; abort_unless($teamId !== null, 403); $articles = NewsArticle::query()->published()->visibleToTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('title', 'like', '%'.$this->search.'%'))->latest('published_at')->paginate(25); return view('real-estate-marketing-livewire::news-article-list', ['articles' => $articles]); }
}
