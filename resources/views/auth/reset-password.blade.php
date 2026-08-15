@extends('layouts.app')

@section('content')
    <x-auth-panel :title="__('Set a new password')"
                  :lede="__('Pick something you have not used elsewhere. The link that brought you here stops working once this is done.')">
        <form method="POST" action="{{ route('password.update') }}" class="grid gap-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <x-ui.field id="email" :label="__('Email')">
                <x-ui.control id="email" type="email" name="email" :value="old('email', $request->email)"
                              autocomplete="username" required autofocus
                              :invalid="$errors->has('email')" />
            </x-ui.field>

            <x-ui.field id="password" :label="__('New password')">
                <x-ui.control id="password" type="password" name="password"
                              autocomplete="new-password" required
                              :invalid="$errors->has('password')" />
            </x-ui.field>

            <x-ui.field id="password_confirmation" :label="__('New password again')">
                <x-ui.control id="password_confirmation" type="password" name="password_confirmation"
                              autocomplete="new-password" required
                              :invalid="$errors->has('password_confirmation')" />
            </x-ui.field>

            <div class="flex items-center justify-end">
                <x-ui.button type="submit">{{ __('Save the new password') }}</x-ui.button>
            </div>
        </form>
    </x-auth-panel>
@endsection
