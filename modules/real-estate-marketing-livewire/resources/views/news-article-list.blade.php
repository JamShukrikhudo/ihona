<div>
    <div wire:loading role="status">Loading news…</div>
    <input wire:model.live="search" type="search" placeholder="Search news">
    @forelse ($articles as $article)
        <article wire:key="news-{{ $article->id }}"><h2>{{ $article->title }}</h2><p>{{ $article->excerpt }}</p></article>
    @empty
        <p>No news articles found.</p>
    @endforelse
    {{ $articles->links() }}
</div>
