@extends('layouts.app')

@section('content')
    <x-auth-panel :title="__('Create an account')"
                  :lede="__('Save homes, get told when the ones you like change, and keep your viewings in one place.')"
                  wide>
        <form method="POST" action="{{ route('register') }}" class="grid gap-5 sm:grid-cols-2">
            @csrf

            <x-ui.field id="name" :label="__('Name')" class="sm:col-span-2">
                <x-ui.control id="name" type="text" name="name" :value="old('name')"
                              autocomplete="name" required autofocus
                              :invalid="$errors->has('name')" />
            </x-ui.field>

            <x-ui.field id="email" :label="__('Email')" class="sm:col-span-2">
                <x-ui.control id="email" type="email" name="email" :value="old('email')"
                              autocomplete="email" required
                              :invalid="$errors->has('email')" />
            </x-ui.field>

            <x-ui.field id="password" :label="__('Password')">
                <x-ui.control id="password" type="password" name="password"
                              autocomplete="new-password" required
                              :invalid="$errors->has('password')" />
            </x-ui.field>

            <x-ui.field id="password_confirmation" :label="__('Password again')">
                <x-ui.control id="password_confirmation" type="password" name="password_confirmation"
                              autocomplete="new-password" required
                              :invalid="$errors->has('password_confirmation')" />
            </x-ui.field>

            {{-- The role decides which panel this person lands in after
                 registering, so it is asked in their words rather than the
                 application's. --}}
            <x-ui.field id="role" :label="__('What brings you here?')"
                        :hint="__('It sets what you see first. It can be changed later.')"
                        class="sm:col-span-2">
                <x-ui.control as="select" id="role" name="role" required :invalid="$errors->has('role')">
                    <option value="">{{ __('Choose one') }}</option>
                    @foreach ([
                        'buyer' => __('Buying a home'),
                        'seller' => __('Selling a home'),
                        'tenant' => __('Renting a home'),
                        'landlord' => __('Letting a property'),
                        'contractor' => __('Working on properties'),
                    ] as $value => $label)
                        <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                    @endforeach
                </x-ui.control>
            </x-ui.field>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <x-ui.field id="terms" class="sm:col-span-2">
                    <label for="terms" class="flex items-start gap-2 text-body-s text-ink-700">
                        <input id="terms" type="checkbox" name="terms" required
                               class="mt-0.5 size-4 shrink-0 rounded-tag border-sheet-300 bg-sheet-000 text-action">
                        <span>
                            {!! __('I agree to the :terms and :privacy.', [
                                'terms' => '<a class="text-draft-700 underline underline-offset-2 hover:no-underline" href="'.e(route('termsandconditions')).'">'.e(__('terms of service')).'</a>',
                                'privacy' => '<a class="text-draft-700 underline underline-offset-2 hover:no-underline" href="'.e(route('privacypolicy')).'">'.e(__('privacy policy')).'</a>',
                            ]) !!}
                        </span>
                    </label>
                </x-ui.field>
            @endif

            <div class="flex items-center justify-end sm:col-span-2">
                <x-ui.button type="submit">{{ __('Create account') }}</x-ui.button>
            </div>
        </form>

        {{-- Same tag, same crash as the login page. See the note there. --}}
        @if (\JoelButcher\Socialstream\Socialstream::show())
            <x-socialstream />
        @endif

        <x-slot name="footer">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}"
               class="text-draft-700 underline underline-offset-2 hover:no-underline">
                {{ __('Sign in') }}
            </a>
        </x-slot>
    </x-auth-panel>
@endsection
