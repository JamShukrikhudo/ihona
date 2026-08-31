@extends('layouts.app')

@section('content')
    <div class="re-container re-section">
        <header class="max-w-reading">
            <p class="re-eyebrow">{{ __('What we do') }}</p>
            <h1>{{ __('Sales, lettings and management') }}</h1>
            <p class="re-lede">{{ __('Practical support and clear tools for the decisions that matter throughout a property journey.') }}</p>
        </header>

        <div class="mt-10 grid gap-5 sm:grid-cols-2">
            @foreach ([
                __('Property sales') => [__('Valuation and market context'), __('Listing preparation'), __('Viewings and enquiries'), __('Offer support')],
                __('Property rentals') => [__('Tenant applications'), __('Viewing coordination'), __('Lease administration'), __('Ongoing communication')],
                __('Property management') => [__('Portfolio oversight'), __('Maintenance coordination'), __('Inspections and records'), __('Financial visibility')],
                __('Property advice') => [__('Market research'), __('Investment calculators'), __('Comparable property context'), __('A clear next step')],
            ] as $service => $points)
                <section class="rounded-sheet border border-sheet-300 bg-sheet-000 p-6">
                    <h2>{{ $service }}</h2>
                    <ul class="mt-4 space-y-2">
                        @foreach ($points as $point)
                            <li>{{ $point }}</li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>

        <div class="mt-10 max-w-reading">
            <h2>{{ __('Need a starting point?') }}</h2>
            <p>{{ __('Tell us what you are trying to achieve and we will help you find the right next step.') }}</p>
            <a class="re-button re-button--primary" href="{{ route('contact.show') }}">{{ __('Talk to us') }}</a>
        </div>
    </div>
@endsection
