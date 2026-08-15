@extends('layouts.app')

@section('content')
    <x-auth-panel :title="__('Check your email')"
                  :lede="__('We sent a link to the address on your account. Open it and you are in — the link is what proves the address is yours.')">
        @if (session('status') === 'verification-link-sent')
            <p role="status"
               class="mb-5 rounded-sheet border border-verdigris-600 bg-verdigris-100 px-4 py-3 text-body-s text-verdigris-700">
                {{ __('Sent again. It can take a minute to arrive.') }}
            </p>
        @endif

        {{-- Guarded: Fortify's email verification feature is off in config, so
             verification.send is not registered. --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            @if (Route::has('verification.send'))
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-ui.button type="submit">{{ __('Send it again') }}</x-ui.button>
                </form>
            @endif

            <div class="flex flex-wrap items-center gap-4">
                @if (Route::has('profile.show'))
                    <a href="{{ route('profile.show') }}"
                       class="text-body-s text-draft-700 underline underline-offset-2 hover:no-underline">
                        {{ __('Change the address') }}
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-body-s text-draft-700 underline underline-offset-2 hover:no-underline">
                        {{ __('Sign out') }}
                    </button>
                </form>
            </div>
        </div>
    </x-auth-panel>
@endsection
