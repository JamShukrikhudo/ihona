{{--
    What the record holds about the setting rather than the building. The
    building's own facts are in the disclosure panel above.

    The labels here were `text-gray-500` on the page ground — 4.33:1, under AA
    at 16px — for every one of the eight headings in this block.
--}}
<section class="mt-band grid gap-4 sm:grid-cols-2" aria-labelledby="setting-heading">
    <h2 id="setting-heading" class="sr-only">{{ __('About this property') }}</h2>

    <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
        <h3 class="font-mono text-annotation uppercase text-ink-500">{{ __('Category') }}</h3>
        <p class="mt-1.5 text-body text-ink-900">
            @if ($property->category?->name)
                {{ ucfirst($property->category->name) }}
            @else
                <x-ui.not-supplied />
            @endif
        </p>

        <h3 class="mt-5 font-mono text-annotation uppercase text-ink-500">{{ __('Branch') }}</h3>
        <p class="mt-1.5 text-body text-ink-900">
            {{ $team->name ?? __('Not supplied') }}
        </p>
    </div>

    <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
        <h3 class="font-mono text-annotation uppercase text-ink-500">{{ __('Features') }}</h3>
        @if (($property->features ?? collect())->isNotEmpty())
            <ul class="mt-2 grid list-none grid-cols-1 gap-x-4 gap-y-1 p-0 text-body-s text-ink-700 sm:grid-cols-2">
                @foreach ($property->features as $feature)
                    <li class="flex items-baseline gap-2">
                        <span class="text-survey-600" aria-hidden="true">&bull;</span>{{ $feature->name }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-1.5 text-body-s text-ink-400">{{ __('None listed') }}</p>
        @endif
    </div>

    <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-5 sm:col-span-2">
        <h3 class="font-mono text-annotation uppercase text-ink-500">{{ __('Getting around') }}</h3>
        @if ($property->walkability_score)
            {{-- Three scores out of a hundred, in the same mono column the rest
                 of the record uses. They were three gradient tiles with white
                 text on them, which is the one background a contrast check
                 cannot measure. --}}
            <dl class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                @foreach ([
                    ['label' => __('Walk'), 'score' => $property->walkability_score, 'note' => $property->walkability_description],
                    ['label' => __('Transit'), 'score' => $property->transit_score, 'note' => $property->transit_description],
                    ['label' => __('Bike'), 'score' => $property->bike_score, 'note' => $property->bike_description],
                ] as $measure)
                    @if ($measure['score'])
                        <div class="rounded-sheet bg-sheet-100 p-3">
                            <dt class="font-mono text-annotation uppercase text-ink-500">{{ $measure['label'] }}</dt>
                            <dd>
                                <p class="mt-1 font-display text-h5 font-bold tabular-nums text-ink-900">
                                    {{ (int) $measure['score'] }}<span class="text-body-s font-normal text-ink-400">/100</span>
                                </p>
                                @if ($measure['note'])
                                    <p class="mt-0.5 text-caption text-ink-500">{{ $measure['note'] }}</p>
                                @endif
                            </dd>
                        </div>
                    @endif
                @endforeach
            </dl>
            @if ($property->walkability_updated_at)
                <p class="mt-3 font-mono text-micro text-ink-400">
                    {{ __('Scored :date', ['date' => $property->walkability_updated_at->format('j M Y')]) }}
                </p>
            @endif
        @else
            <p class="mt-1.5 text-body-s text-ink-400">{{ __('Not scored for this address') }}</p>
        @endif
    </div>

    <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-5 sm:col-span-2">
        <h3 class="font-mono text-annotation uppercase text-ink-500">{{ __('Neighbourhood') }}</h3>

        @if ($neighborhood)
            @if ($neighborhood->description)
                <p class="mt-2 max-w-reading text-body-s text-ink-700">{{ $neighborhood->description }}</p>
            @endif

            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 sm:grid-cols-4">
                @foreach ([
                    __('Population') => $neighborhood->population ? number_format($neighborhood->population) : null,
                    __('Median income') => $neighborhood->median_income ? $currency.number_format($neighborhood->median_income) : null,
                    __('Walk score') => $neighborhood->walk_score ? $neighborhood->walk_score.'/100' : null,
                    __('Transit score') => $neighborhood->transit_score ? $neighborhood->transit_score.'/100' : null,
                ] as $label => $value)
                    <div class="min-w-0">
                        <dt class="font-mono text-annotation uppercase text-ink-500">{{ $label }}</dt>
                        <dd class="mt-1 truncate font-mono text-body-s font-medium tabular-nums text-ink-900">
                            {{-- The median income used to print with a hard '$'
                                 whatever currency the listing was in. --}}
                            {{ $value ?? '—' }}
                        </dd>
                    </div>
                @endforeach
            </dl>

            @if (filled($neighborhood->schools) || filled($neighborhood->amenities))
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @if (filled($neighborhood->schools))
                        <div>
                            <h4 class="font-mono text-annotation uppercase text-ink-500">{{ __('Schools') }}</h4>
                            <ul class="mt-1.5 list-none space-y-1 p-0 text-body-s text-ink-700">
                                @foreach ($neighborhood->schools as $school)
                                    <li class="flex items-baseline justify-between gap-3">
                                        <span class="min-w-0 truncate">{{ $school['name'] ?? '' }}</span>
                                        <span class="shrink-0 font-mono text-caption tabular-nums text-ink-500">
                                            {{ $school['rating'] ?? '—' }}/10
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (filled($neighborhood->amenities))
                        <div>
                            <h4 class="font-mono text-annotation uppercase text-ink-500">{{ __('Amenities') }}</h4>
                            <ul class="mt-1.5 list-none space-y-1 p-0 text-body-s text-ink-700">
                                @foreach ($neighborhood->amenities as $amenity)
                                    <li>{{ $amenity }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            @if ($neighborhood->last_updated)
                <p class="mt-3 font-mono text-micro text-ink-400">
                    {{ __('Updated :date', ['date' => $neighborhood->last_updated->format('j M Y')]) }}
                </p>
            @endif

            <div class="mt-5 border-t border-sheet-300 pt-5">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h4 class="font-display text-h5 font-bold tracking-tight text-ink-900">
                        {{ __('What neighbours say') }}
                    </h4>
                    @if ($neighborhoodReviews && $neighborhoodReviews->count() > 0)
                        <p class="font-mono text-caption tabular-nums text-ink-500">
                            {{ __(':rating out of 5', ['rating' => number_format($neighborhoodAverageRating, 1)]) }}
                            <span class="text-ink-400">
                                ({{ trans_choice(':count review|:count reviews', $neighborhoodReviews->count(), ['count' => $neighborhoodReviews->count()]) }})
                            </span>
                        </p>
                    @endif
                </div>

                @if ($neighborhoodReviews && $neighborhoodReviews->count() > 0)
                    <ul class="mt-3 list-none space-y-3 p-0">
                        @foreach ($neighborhoodReviews->take(3) as $review)
                            <li class="rounded-sheet bg-sheet-100 p-4">
                                <div class="flex flex-wrap items-baseline justify-between gap-2">
                                    <p class="font-semibold text-body-s text-ink-900">{{ $review->title }}</p>
                                    <p class="font-mono text-caption tabular-nums text-ink-500">
                                        {{ __(':rating out of 5', ['rating' => $review->rating]) }}
                                    </p>
                                </div>
                                <p class="mt-1 font-mono text-micro text-ink-400">
                                    {{ $review->user->name ?? __('Anonymous') }} &middot; {{ $review->created_at->diffForHumans() }}
                                </p>
                                <p class="mt-2 text-body-s text-ink-700">{{ $review->comment }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-2 text-body-s text-ink-400">{{ __('No reviews yet.') }}</p>
                @endif

                @auth
                    <div class="mt-4">
                        @livewire('neighborhood-review-form', ['neighborhoodId' => $neighborhood->id])
                    </div>
                @else
                    <p class="mt-3 text-body-s text-ink-500">
                        {!! __('Please :login to review this neighbourhood.', [
                            'login' => '<a class="text-draft-500 underline underline-offset-2 hover:no-underline" href="'.e(url('/login')).'">'.e(__('sign in')).'</a>',
                        ]) !!}
                    </p>
                @endauth
            </div>
        @else
            <p class="mt-1.5 text-body-s text-ink-400">{{ __('No neighbourhood record for this address') }}</p>
        @endif
    </div>
</section>
