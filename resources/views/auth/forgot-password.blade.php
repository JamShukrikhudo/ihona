@extends('layouts.app')

@section('content')
    <x-auth-panel :title="__('Reset your password')"
                  :lede="__('Give us the address you signed up with and we will email a link that lets you set a new password.')">
        <form method="POST" action="{{ route('password.email') }}" class="grid gap-5">
            @csrf

            <x-ui.field id="email" :label="__('Email')">
                <x-ui.control id="email" type="email" name="email" :value="old('email')"
                              autocomplete="username" required autofocus
                              :invalid="$errors->has('email')" />
            </x-ui.field>

            <div class="flex items-center justify-end">
                <x-ui.button type="submit">{{ __('Email me a link') }}</x-ui.button>
            </div>
        </form>

        <x-slot name="footer">
            {{ __('Remembered it?') }}
            <a href="{{ route('login') }}"
               class="text-draft-700 underline underline-offset-2 hover:no-underline">
                {{ __('Sign in') }}
            </a>
        </x-slot>
    </x-auth-panel>
@endsection
