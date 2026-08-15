<div class="mx-auto max-w-(--breakpoint-xl) px-4 py-band md:px-margin">
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="font-mono text-annotation uppercase text-ink-400">{{ __('Saved') }}</p>
            <h1 class="mt-3 font-display text-h2 font-bold tracking-tight text-ink-900">
                {{ __('Your shortlist') }}
            </h1>
        </div>

        <p class="font-mono text-body-s font-medium tabular-nums text-ink-900" aria-live="polite">
            {{ trans_choice(':count home saved|:count homes saved', $totalFavorites, ['count' => number_format($totalFavorites)]) }}
        </p>
    </header>

    @if ($totalFavorites)
        <div class="mt-6 flex flex-wrap items-center gap-2">
            <label for="wishlist-search" class="sr-only">{{ __('Search your shortlist') }}</label>
            <div class="flex flex-1 basis-64 items-center gap-2 rounded-sheet border border-sheet-300 bg-sheet-000 px-3">
                <x-ui.icon name="search" class="size-4 shrink-0 text-ink-400" />
                <input id="wishlist-search" type="search" wire:model.live.debounce.300ms="search"
                       placeholder="{{ __('Address or postcode') }}"
                       class="w-full border-0 bg-transparent p-0 py-2.5 font-sans text-body-s text-ink-900 placeholder:text-sheet-400 focus:ring-0 focus:outline-none" />
            </div>

            <label for="wishlist-sort" class="sr-only">{{ __('Sort by') }}</label>
            <select id="wishlist-sort" wire:model.live="sortBy"
                    class="basis-48 rounded-sheet border border-sheet-300 bg-sheet-000 px-3 py-2.5 font-sans text-body-s text-ink-900">
                <option value="created_at">{{ __('Recently saved') }}</option>
                <option value="price">{{ __('Price') }}</option>
                <option value="title">{{ __('Address') }}</option>
            </select>
        </div>
    @endif

    @if (count($favorites))
        <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($favorites as $property)
                <div>
                    <x-property-card :property="$property" />

                    {{-- Removing is the action this page is for, so it is stated
                         rather than hidden behind a bookmark glyph. --}}
                    <button type="button" wire:click="removeFavorite({{ $property->id }})"
                            class="mt-2 font-mono text-annotation uppercase text-ink-400 transition-colors duration-[160ms] hover:text-fault-600"
                            aria-label="{{ __('Remove :title from your shortlist', ['title' => $property->title]) }}">
                        {{ __('Remove from shortlist') }}
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @elseif ($totalFavorites)
        <div class="mt-6 rounded-sheet border border-dashed border-sheet-300 bg-sheet-000 p-10 text-center">
            <p class="font-display text-h4 font-bold tracking-tight text-ink-900">
                {{ __('No saved homes match that search') }}
            </p>
            <p class="mx-auto mt-2 max-w-reading text-body-s text-ink-500">
                {{ __('Clearing the search brings the rest of your shortlist back.') }}
            </p>
        </div>
    @else
        {{-- An empty screen is an invitation to act. --}}
        <div class="mt-6 rounded-sheet border border-dashed border-sheet-300 bg-sheet-000 p-10 text-center">
            <p class="font-display text-h4 font-bold tracking-tight text-ink-900">
                {{ __('Nothing saved yet') }}
            </p>
            <p class="mx-auto mt-2 max-w-reading text-body-s text-ink-500">
                {{ __('The bookmark on any listing card saves it here, so you can line up a few and compare them properly.') }}
            </p>
            <div class="mt-4">
                <x-ui.button size="sm" :href="route('property.list')">{{ __('Browse homes') }}</x-ui.button>
            </div>
        </div>
    @endif
</div>
