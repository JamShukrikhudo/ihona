@extends('layouts.app')

@section('content')
    @php
        $services = [
            [
                'title' => __('Property Sales'),
                'points' => [
                    __('Professional property valuation'),
                    __('Marketing and advertising your property'),
                    __('Arranging and conducting viewings'),
                    __('Negotiating offers'),
                    __('Managing the sales process to completion'),
                ],
            ],
            [
                'title' => __('Property Rentals'),
                'points' => [
                    __('Tenant finding and vetting'),
                    __('Rent collection and management'),
                    __('Property maintenance coordination'),
                    __('Regular property inspections'),
                    __('Legal compliance assistance'),
                ],
            ],
            [
                'title' => __('Property Management'),
                'points' => [
                    __('Full property portfolio management'),
                    __('Financial reporting and record-keeping'),
                    __('24/7 emergency maintenance support'),
                    __('Lease renewal and negotiation'),
                    __('Property improvement recommendations'),
                ],
            ],
            [
                'title' => __('Advisory Services'),
                'points' => [
                    __('Market analysis and trends'),
                    __('Investment property consultation'),
                    __('Property development advice'),
                    __('Legal and regulatory guidance'),
                    __('Property portfolio optimization'),
                ],
            ],
        ];
    @endphp

    <div class="mx-auto max-w-(--breakpoint-xl) px-4 py-band md:px-margin">
        <header class="max-w-reading">
            <p class="font-mono text-annotation uppercase text-ink-400">{{ __('What we do') }}</p>
            <h1 class="mt-3 font-display text-h2 font-bold tracking-tight text-ink-900">
                {{ __('Sales, lettings and management') }}
            </h1>
            <p class="mt-4 text-body-l text-ink-500">
                {{ __('Four services, one team. Whichever you need, the same people handle it from the first valuation to the last set of keys.') }}
            </p>
        </header>

        <div class="mt-10 grid gap-5 sm:grid-cols-2">
            @foreach ($services as $service)
                <section class="rounded-sheet border border-sheet-300 bg-sheet-000 p-6">
                    <h2 class="font-display text-h4 font-bold tracking-tight text-ink-900">
                        {{ $service['title'] }}
                    </h2>

                    <ul class="mt-4 space-y-2">
                        @foreach ($service['points'] as $point)
                            <li class="flex items-start gap-2.5 text-body-s text-ink-700">
                                <x-ui.icon name="certificate" class="mt-0.5 size-4 shrink-0 text-verdigris-600" />
                                {{ $point }}
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>

        <div class="mt-10 flex flex-wrap items-center gap-4 rounded-sheet border border-sheet-300 bg-sheet-000 p-6">
            <div class="max-w-reading">
                <h2 class="font-display text-h4 font-bold tracking-tight text-ink-900">
                    {{ __('Not sure which you need?') }}
                </h2>
                <p class="mt-2 text-body-s text-ink-500">
                    {{ __('Tell us about the property and we will say plainly what we would do with it.') }}
                </p>
            </div>
            <x-ui.button :href="route('contact.show')" class="ml-auto">
                {{ __('Talk to us') }}
            </x-ui.button>
        </div>
    </div>
@endsection
