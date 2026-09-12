<article class="mx-auto w-full max-w-3xl space-y-4 px-4 py-6 sm:px-6">
    <h1 class="text-2xl font-bold sm:text-3xl">{{ $article->title }}</h1>
    <div class="max-w-none space-y-4 leading-relaxed">{!! $article->content !!}</div>
    <aside class="flex flex-col gap-2 border-t border-gray-200 pt-4 sm:flex-row sm:flex-wrap sm:gap-4">
        @foreach ($related as $relatedArticle)
            <a href="{{ url('/news/'.$relatedArticle->slug) }}">{{ $relatedArticle->title }}</a>
        @endforeach
    </aside>
</article>
