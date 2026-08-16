@props([
    'prefix',
    'heading',
    'intro',
    'titlePlaceholder',
    'commentPlaceholder',
    'rating' => 5,
])

{{--
    The property review and the neighbourhood review are the same form. They
    were two copies, and the copies had already drifted — one submitted in
    indigo, the other in the old primary ramp, and both hardcoded id="title",
    so on a page carrying both the second label pointed at the first input.
    `prefix` is what keeps them apart.
--}}
<section {{ $attributes->class('rounded-sheet border border-sheet-300 bg-sheet-100 p-4 sm:p-5') }}
         aria-labelledby="{{ $prefix }}-heading">
    <h3 id="{{ $prefix }}-heading" class="font-display text-h5 font-bold tracking-tight text-ink-900">
        {{ $heading }}
    </h3>
    <p class="mt-1.5 text-body-s text-ink-700">{{ $intro }}</p>

    @if (session()->has('message'))
        <p class="mt-4 rounded-sheet border border-verdigris-600 bg-verdigris-100 px-4 py-3 text-body-s text-verdigris-700"
           role="status">
            {{ session('message') }}
        </p>
    @endif

    @error('general')
        <p class="mt-4 flex items-center gap-1.5 rounded-sheet border border-fault-600 bg-fault-100 px-4 py-3 text-body-s text-fault-700"
           role="alert">
            <x-ui.icon name="alert" class="size-4 shrink-0" />
            {{ $message }}
        </p>
    @enderror

    <form wire:submit.prevent="submitReview" class="mt-4 flex flex-col gap-4">
        <x-ui.field :id="$prefix.'-title'" :label="__('Review title')" :error="$errors->first('title')">
            <x-ui.control
                :id="$prefix.'-title'"
                type="text"
                wire:model="title"
                :placeholder="$titlePlaceholder"
                :invalid="$errors->has('title')"
            />
        </x-ui.field>

        {{-- Each star carries its own name: a row of glyphs is unreadable to
             anything that is not looking at it. --}}
        <x-ui.field :label="__('Overall rating')" :error="$errors->first('rating')">
            <div class="flex items-center gap-1" role="group" aria-label="{{ __('Overall rating') }}">
                @for ($i = 1; $i <= 5; $i++)
                    <button type="button"
                            wire:click="$set('rating', {{ $i }})"
                            aria-pressed="{{ $i <= $rating ? 'true' : 'false' }}"
                            aria-label="{{ __(':n out of 5', ['n' => $i]) }}"
                            class="rounded-tag p-0.5 transition-colors duration-[160ms] {{ $i <= $rating ? 'text-survey-500' : 'text-sheet-400 hover:text-survey-300' }}">
                        <svg class="size-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                        </svg>
                    </button>
                @endfor
                <span class="ml-1.5 font-mono text-caption tabular-nums text-ink-500">
                    {{ __(':rating out of 5', ['rating' => $rating]) }}
                </span>
            </div>
        </x-ui.field>

        <x-ui.field :id="$prefix.'-comment'" :label="__('Your review')" :error="$errors->first('comment')">
            <x-ui.control
                as="textarea"
                :id="$prefix.'-comment'"
                rows="4"
                wire:model="comment"
                :placeholder="$commentPlaceholder"
                :invalid="$errors->has('comment')"
            />
        </x-ui.field>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="font-mono text-caption text-ink-400">
                {{ __('Moderated before it appears publicly.') }}
            </p>
            {{-- Secondary: "Book a viewing" is this page's one primary. --}}
            <x-ui.button variant="secondary" type="submit">{{ __('Submit the review') }}</x-ui.button>
        </div>
    </form>
</section>
