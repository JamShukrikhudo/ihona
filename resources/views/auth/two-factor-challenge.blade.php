@extends('layouts.app')

@section('content')
    <div x-data="{ recovery: false }">
        <x-auth-panel :title="__('One more step')">
            <p class="text-body-s text-ink-500" x-show="! recovery">
                {{ __('Open your authenticator app and enter the six-digit code it shows for this account.') }}
            </p>

            <p class="text-body-s text-ink-500" x-cloak x-show="recovery">
                {{ __('Enter one of the recovery codes you saved when you set two-factor up. Each one works once.') }}
            </p>

            <form method="POST" action="{{ route('two-factor.login') }}" class="mt-5 grid gap-5">
                @csrf

                <div x-show="! recovery">
                    <x-ui.field id="code" :label="__('Authentication code')">
                        {{-- One-time-code autocomplete and a numeric keypad: on a
                             phone this is typed from a second app, and a full
                             keyboard for six digits is a small cruelty. --}}
                        <x-ui.control id="code" type="text" name="code" inputmode="numeric"
                                      autocomplete="one-time-code" autofocus x-ref="code"
                                      class="font-mono tracking-[0.3em]"
                                      :invalid="$errors->has('code')" />
                    </x-ui.field>
                </div>

                <div x-cloak x-show="recovery">
                    <x-ui.field id="recovery_code" :label="__('Recovery code')">
                        <x-ui.control id="recovery_code" type="text" name="recovery_code"
                                      autocomplete="one-time-code" x-ref="recovery_code"
                                      class="font-mono"
                                      :invalid="$errors->has('recovery_code')" />
                    </x-ui.field>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <button type="button"
                            class="text-body-s text-draft-700 underline underline-offset-2 hover:no-underline"
                            x-show="! recovery"
                            x-on:click="recovery = true; $nextTick(() => $refs.recovery_code.focus())">
                        {{ __('Use a recovery code instead') }}
                    </button>

                    <button type="button"
                            class="text-body-s text-draft-700 underline underline-offset-2 hover:no-underline"
                            x-cloak
                            x-show="recovery"
                            x-on:click="recovery = false; $nextTick(() => $refs.code.focus())">
                        {{ __('Use the app code instead') }}
                    </button>

                    <x-ui.button type="submit">{{ __('Sign in') }}</x-ui.button>
                </div>
            </form>
        </x-auth-panel>
    </div>
@endsection
