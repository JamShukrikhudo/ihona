@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-(--breakpoint-xl) px-4 py-band md:px-margin">
        <div class="max-w-3xl">
            <p class="font-mono text-annotation uppercase text-ink-400">{{ __('Get in touch') }}</p>
            <h1 class="mt-3 font-display text-h2 font-bold tracking-tight text-ink-900">
                {{ __('Ask us anything about a property') }}
            </h1>
            <p class="mt-4 max-w-reading text-body-l text-ink-500">
                {{ __('Questions about a listing, a viewing, or putting your own home on the market — all of it reaches the same team.') }}
            </p>

            <div class="mt-8 rounded-sheet border border-sheet-300 bg-sheet-000 p-6">
                <x-contact-form :property="$property ?? null" />
            </div>
        </div>
    </div>
@endsection
