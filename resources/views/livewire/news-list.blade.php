<div class="mx-auto max-w-(--breakpoint-xl) px-4 py-band md:px-margin">
    <header class="max-w-reading">
        <p class="font-mono text-annotation uppercase text-ink-400">{{ __('Property news') }}</p>
        <h1 class="mt-3 font-display text-h2 font-bold tracking-tight text-ink-900">
            {{ __('What is happening to the market') }}
        </h1>
        <p class="mt-4 text-body-l text-ink-500">
            {{ __('Rates, rules and local movement — what changed, and what it means for a sale or a tenancy.') }}
        </p>
    </header>

    <div class="mt-8 flex flex-wrap items-center gap-2">
        <label for="news-search" class="sr-only">{{ __('Search stories') }}</label>
        <div class="flex flex-1 basis-64 items-center gap-2 rounded-sheet border border-sheet-300 bg-sheet-000 px-3">
            <x-ui.icon name="search" class="size-4 shrink-0 text-ink-400" />
            <input id="news-search" type="search" wire:model.live.debounce.300ms="search"
                   placeholder="{{ __('Search stories') }}"
                   class="w-full border-0 bg-transparent p-0 py-2.5 font-sans text-body-s text-ink-900 placeholder:text-sheet-400 focus:ring-0 focus:outline-none" />
        </div>

        <label class="inline-flex cursor-pointer items-center gap-2 rounded-sheet border border-sheet-300 bg-sheet-000 px-3 py-2.5 text-body-s text-ink-700">
            <input type="checkbox" wire:model.live="featuredOnly"
                   class="rounded-tag border-sheet-300 text-action focus:ring-survey-500" />
            {{ __('Featured only') }}
        </label>
    </div>

    @if (count($news))
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($news as $article)
                {{-- Date and category as mono annotations: a story's age is a
                     fact a reader weighs before they read a word of it. --}}
                <article class="group relative flex flex-col rounded-sheet border border-sheet-300 bg-sheet-000 p-5 transition-[box-shadow,border-color] duration-[280ms] ease-set hover:border-ink-400 hover:shadow-lift-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <time class="font-mono text-annotation uppercase text-ink-400"
                              datetime="{{ $article->published_at?->toDateString() }}">
                            {{ $article->published_at?->format('j M Y') ?? __('Undated') }}
                        </time>

                        @if ($article->is_featured)
                            <x-ui.chip tone="new">{{ __('Featured') }}</x-ui.chip>
                        @endif
                    </div>

                    <h2 class="mt-3 font-display text-h5 font-bold tracking-tight text-ink-900">
                        <a href="{{ route('news.detail', $article->slug) }}" class="after:absolute after:inset-0">
                            {{ $article->title }}
                        </a>
                    </h2>

                    <p class="mt-2 text-body-s text-ink-500">
                        {{ Str::limit($article->excerpt ?: strip_tags($article->content), 150) }}
                    </p>

                    @if ($article->author)
                        <p class="mt-4 font-mono text-annotation uppercase text-ink-400">
                            {{ __('By :name', ['name' => $article->author->name]) }}
                        </p>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $news->links() }}
        </div>
    @else
        <div class="mt-8 rounded-sheet border border-dashed border-sheet-300 bg-sheet-000 p-10 text-center">
            <p class="font-display text-h4 font-bold tracking-tight text-ink-900">
                {{ $search || $featuredOnly ? __('No stories match that') : __('No stories yet') }}
            </p>
            <p class="mx-auto mt-2 max-w-reading text-body-s text-ink-500">
                {{ $search || $featuredOnly
                    ? __('Clearing the search brings the rest back.')
                    : __('Market notes will appear here as they are written. In the meantime, the homes are the news.') }}
            </p>
            <div class="mt-4">
                <x-ui.button size="sm" :href="route('property.list')">{{ __('Browse homes') }}</x-ui.button>
            </div>
        </div>
    @endif
</div>
