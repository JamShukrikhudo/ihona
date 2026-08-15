@extends('layouts.app')

@section('content')
    <x-auth-panel :title="__('Confirm your password')"
                  :lede="__('This part of your account changes what other people can see or do, so we ask for the password again before going in.')">
        <form method="POST" action="{{ route('password.confirm') }}" class="grid gap-5">
            @csrf

            <x-ui.field id="password" :label="__('Password')">
                <x-ui.control id="password" type="password" name="password"
                              autocomplete="current-password" required autofocus
                              :invalid="$errors->has('password')" />
            </x-ui.field>

            <div class="flex items-center justify-end">
                <x-ui.button type="submit">{{ __('Confirm') }}</x-ui.button>
            </div>
        </form>
    </x-auth-panel>
@endsection
