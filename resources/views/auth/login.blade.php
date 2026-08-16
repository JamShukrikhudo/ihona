@extends('layouts.app')

@section('content')
    <x-auth-panel :title="__('Sign in')"
                  :lede="__('Saved homes, saved searches and your viewings, wherever you left them.')">
        <form method="POST" action="{{ route('login') }}" class="grid gap-5">
            @csrf

            <x-ui.field id="email" :label="__('Email')">
                <x-ui.control id="email" type="email" name="email" :value="old('email')"
                              autocomplete="username" required autofocus
                              :invalid="$errors->has('email')" />
            </x-ui.field>

            <x-ui.field id="password" :label="__('Password')">
                <x-ui.control id="password" type="password" name="password"
                              autocomplete="current-password" required
                              :invalid="$errors->has('password')" />
            </x-ui.field>

            <label for="remember_me" class="flex items-center gap-2 text-body-s text-ink-700">
                <input id="remember_me" type="checkbox" name="remember"
                       class="size-4 rounded-tag border-sheet-300 bg-sheet-000 text-action">
                {{ __('Keep me signed in') }}
            </label>

            <div class="flex flex-wrap items-center justify-between gap-3">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-body-s text-draft-700 underline underline-offset-2 hover:no-underline">
                        {{ __('Forgotten your password?') }}
                    </a>
                @endif

                <x-ui.button type="submit">{{ __('Sign in') }}</x-ui.button>
            </div>
        </form>

        @if (\JoelButcher\Socialstream\Socialstream::show())
            <x-socialstream />
        @endif

        @if (Route::has('register'))
            <x-slot name="footer">
                {{ __('No account yet?') }}
                <a href="{{ route('register') }}"
                   class="text-draft-700 underline underline-offset-2 hover:no-underline">
                    {{ __('Create one') }}
                </a>
            </x-slot>
        @endif
    </x-auth-panel>
@endsection
