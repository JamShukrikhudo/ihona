{{--
    The property record.

    This view was 1,140 lines in one file, which is why the ticket-07 restyle
    stopped at the disclosure panel: nothing in it could be changed without
    reading all of it. Each section below is its own partial, small enough to
    review and to move onto the design system on its own.

    Partials see the component's public properties directly — $property,
    $reviews, $neighborhood and the rest — so none of them is passed a payload.
    None uses $this: a partial is rendered by Blade rather than by Livewire, so
    a component method called from one would resolve against the view.
--}}
<div>
    @php
        $currency = app(\App\Settings\GeneralSettings::class)->currencySymbol();
    @endphp

    <section class="mx-auto max-w-(--breakpoint-xl) px-4 py-8 md:px-margin md:py-12">
        <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">
            <div class="min-w-0">
                {{-- Rooms first, then the drawings of the building they are in.
                     The floor plan used to sit 900 lines below this, in its own
                     widget, which is the one thing a buyer scrolls back up
                     looking for. --}}
                <x-property-gallery :property="$property" />

                @include('livewire.property-detail.immersive')
            </div>

            <div class="mt-8 min-w-0 lg:mt-0">
                @include('livewire.property-detail.summary')
                @include('livewire.property-detail.tours')
            </div>
        </div>

        @include('livewire.property-detail.description')

        @include('livewire.property-detail.facts')
        @include('livewire.property-detail.investment')

        @if ($property->model_3d_url)
            <div class="mt-band">
                <h2 class="mb-4 font-display text-h4 font-bold tracking-tight text-ink-900">
                    {{ __('3D property model') }}
                </h2>
                <x-model-3d-viewer :modelUrl="$property->model_3d_url" :propertyTitle="$property->title" />
            </div>
        @endif

        @include('livewire.property-detail.history')
        @include('livewire.property-detail.events')

        {{-- The interactive plan: the same drawing as the gallery tile, with
             the room annotations the record holds on top of it. --}}
        <x-floor-plan-viewer :floor-plan-data="$property->floor_plan_data" />

        @php
            $video = $property->getFirstMedia('videos');
        @endphp
        @if ($video)
            <div class="mt-band">
                <h2 class="mb-4 font-display text-h4 font-bold tracking-tight text-ink-900">
                    {{ __('Property video') }}
                </h2>
                {{-- One crop ratio across the page, the gallery's included. --}}
                <div class="aspect-3/2 overflow-hidden rounded-sheet border border-sheet-300 bg-sheet-200">
                    <video class="h-full w-full"
                           controls
                           preload="none"
                           controlsList="nodownload"
                           aria-label="{{ __('Video tour of :title', ['title' => $property->title]) }}">
                        <source src="{{ $video->getUrl() }}" type="{{ $video->mime_type }}">
                        {{ __('Your browser cannot play this video.') }}
                    </video>
                </div>
            </div>
        @endif

        @if ($showInvestmentSimulation)
            <div class="mt-band rounded-sheet border border-sheet-300 bg-sheet-000 p-6">
                @livewire('investment-analysis-component', ['property' => $property])
            </div>
        @endif

        <div class="mt-band">
            @livewire('property-tax-estimator', ['property' => $property])
        </div>

        <div class="mt-band">
            @auth
                @livewire('property-review-form', ['propertyId' => $property->id])
            @else
                <p class="text-body-s text-ink-500">
                    {!! __('Please :login to leave a review.', [
                        'login' => '<a class="text-draft-500 underline underline-offset-2 hover:no-underline" href="'.e(url('/login')).'">'.e(__('sign in')).'</a>',
                    ]) !!}
                </p>
            @endauth
        </div>
    </section>

    @include('livewire.property-detail.live-tour-modal')

    {{-- The same action again at the end of the page. A visitor who has
         read to the bottom should not have to scroll back up to act, and
         the verb matches the confirmation they will be shown. --}}
    <section class="mx-auto max-w-(--breakpoint-xl) px-4 pb-band md:px-margin">
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-sheet border border-sheet-300 bg-sheet-000 p-6">
            <div>
                <p class="font-display text-h4 font-bold tracking-tight text-ink-900">
                    {{ __('Seen enough?') }}
                </p>
                <p class="mt-1 max-w-reading text-body-s text-ink-500">
                    {{ __('Viewings run seven days a week. Booking one does not commit you to anything.') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button :href="route('property.book', $property->id)">
                    {{ __('Book a viewing') }}
                </x-ui.button>
                <x-ui.button variant="secondary" :href="route('contact.show', ['property' => $property->id])">
                    {{ __('Ask a question') }}
                </x-ui.button>
            </div>
        </div>
    </section>
</div>
