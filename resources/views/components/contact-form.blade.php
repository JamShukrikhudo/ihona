@props(['property' => null])

@php
    // Every control shares one treatment, so a field looks the same wherever it
    // appears. Errors get their own border; the ring is the focus state.
    $control = 'w-full rounded-sheet border border-sheet-300 bg-sheet-000 px-3.5 py-[11px]'
        .' font-sans text-body-s text-ink-900 placeholder:text-sheet-400'
        .' transition-[border-color,box-shadow] duration-[160ms]'
        .' hover:border-ink-400 focus:border-survey-500 focus:ring-0 focus:outline-none';
    $invalid = ' border-fault-600';
@endphp

<div>
    @if (session('success'))
        <div role="status"
             class="mb-6 flex items-start gap-3 rounded-sheet border border-verdigris-600 bg-verdigris-100 px-4 py-3">
            <x-ui.icon name="certificate" class="mt-0.5 size-4 shrink-0 text-verdigris-700" />
            <p class="text-body-s text-verdigris-700">{{ session('success') }}</p>
        </div>
    @endif

    @if ($property)
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-sheet border border-sheet-300 bg-sheet-100 px-4 py-3">
            <div>
                <p class="font-mono text-annotation uppercase text-ink-400">{{ __('Asking about') }}</p>
                <p class="mt-0.5 text-body-s font-medium text-ink-900">{{ $property->title }}</p>
            </div>
            <a href="{{ route('property.detail', $property->id) }}"
               class="font-mono text-annotation uppercase text-ink-500 transition-colors duration-[160ms] hover:text-ink-900">
                {{ __('View listing') }}
            </a>
        </div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST" class="grid gap-5 sm:grid-cols-2" novalidate>
        @csrf

        @if ($property)
            <input type="hidden" name="property_id" value="{{ $property->id }}">
        @endif

        <x-ui.field id="name" :label="__('Your name')" :error="$errors->first('name')">
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   autocomplete="name"
                   @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                   class="{{ $control }}{{ $errors->has('name') ? $invalid : '' }}">
        </x-ui.field>

        <x-ui.field id="email" :label="__('Email')" :error="$errors->first('email')">
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   autocomplete="email"
                   @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                   class="{{ $control }}{{ $errors->has('email') ? $invalid : '' }}">
        </x-ui.field>

        <x-ui.field id="phone" :label="__('Phone')"
                    :hint="__('Only if you would rather we called.')"
                    :error="$errors->first('phone')">
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                   autocomplete="tel"
                   aria-describedby="phone-hint"
                   class="{{ $control }}{{ $errors->has('phone') ? $invalid : '' }}">
        </x-ui.field>

        <x-ui.field id="interest" :label="__('What is this about?')" :error="$errors->first('interest')">
            <select id="interest" name="interest"
                    class="{{ $control }}{{ $errors->has('interest') ? $invalid : '' }}">
                <option value="">{{ __('Choose one') }}</option>
                @foreach ([
                    'buying' => __('Buying a property'),
                    'selling' => __('Selling a property'),
                    'renting' => __('Renting a property'),
                    'letting' => __('Letting a property'),
                    'other' => __('Something else'),
                ] as $value => $label)
                    <option value="{{ $value }}" @selected(old('interest') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </x-ui.field>

        <x-ui.field id="message" :label="__('Your question')" class="sm:col-span-2"
                    :error="$errors->first('message')">
            <textarea id="message" name="message" rows="5"
                      placeholder="{{ __('Is the garden south facing? What is the council tax band?') }}"
                      @if ($errors->has('message')) aria-invalid="true" aria-describedby="message-error" @endif
                      class="{{ $control }}{{ $errors->has('message') ? $invalid : '' }}">{{ old('message') }}</textarea>
        </x-ui.field>

        <div class="flex flex-wrap items-center gap-4 sm:col-span-2">
            <x-ui.button type="submit">{{ __('Send message') }}</x-ui.button>
            <p class="text-caption text-ink-400">{{ __('We reply within one working day.') }}</p>
        </div>
    </form>
</div>
