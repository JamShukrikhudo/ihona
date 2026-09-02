@extends('layouts.app')

@section('content')
    <div class="re-container re-section">
        <article class="max-w-reading">
            <p class="re-eyebrow">{{ __('About us') }}</p>
            <h1>{{ __('A clearer way to move through property') }}</h1>
            <p class="re-lede">{{ __('We bring listings, conversations and the practical work of a property journey into one calm, transparent place.') }}</p>

            <h2>{{ __('Our mission') }}</h2>
            <p>{{ __('Buying, selling and renting property should feel informed rather than overwhelming. Our platform helps people discover the right options, ask better questions and keep the next step visible.') }}</p>

            <h2>{{ __('What we value') }}</h2>
            <ul>
                <li>{{ __('Useful information over empty noise') }}</li>
                <li>{{ __('Straightforward communication') }}</li>
                <li>{{ __('Tools that respect people’s time') }}</li>
                <li>{{ __('A more accessible property experience') }}</li>
            </ul>

            <h2>{{ __('Our commitment') }}</h2>
            <p>{{ __('We keep improving the details that make property decisions easier: accurate listings, practical calculators, accessible communication and a dependable record of what happens next.') }}</p>
        </article>
    </div>
@endsection
