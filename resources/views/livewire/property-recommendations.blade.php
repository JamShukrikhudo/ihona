{{--
    This file was a truncated fragment: it opened mid-loop with no @foreach, no
    matching container, an undefined $property, and a link to a route name that
    does not exist. Rendering the component threw, so the currency it prints
    could never have been wrong — it could never have been printed.

    It renders the card component now rather than a fourth hand-rolled listing
    tile, so a recommendation looks like a result and carries the same facts.
--}}
<div>
    @if (count($recommendations))
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($recommendations as $property)
                <x-property-card :property="$property" :actions="false" />
            @endforeach
        </div>

        <div class="mt-6">
            <x-ui.button variant="secondary" wire:click="loadMore">
                {{ __('Show more') }}
            </x-ui.button>
        </div>
    @else
        <p class="text-body-s text-ink-500">
            {{ __('Nothing to recommend yet. Save a home or run a search and this fills up.') }}
        </p>
    @endif
</div>
