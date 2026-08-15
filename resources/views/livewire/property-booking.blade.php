@php
    $control = 'w-full rounded-sheet border border-sheet-300 bg-sheet-000 px-3.5 py-[11px]'
        .' font-sans text-body-s text-ink-900 placeholder:text-sheet-400'
        .' transition-[border-color,box-shadow] duration-[160ms]'
        .' hover:border-ink-400 focus:border-survey-500 focus:ring-0 focus:outline-none';
    $invalid = ' border-fault-600';
@endphp

<div class="mx-auto max-w-2xl px-4 py-band">
    @if ($bookingConfirmed)
        {{-- The verb the button used, reused. --}}
        <div class="rounded-sheet border border-verdigris-600 bg-verdigris-100 p-6" role="status">
            <h1 class="font-display text-h3 font-bold tracking-tight text-verdigris-700">
                {{ __('Viewing booked') }}
            </h1>
            <p class="mt-2 text-body-s text-verdigris-700">{{ $confirmation }}</p>

            @if ($googleCalendarUrl || $outlookCalendarUrl || ($confirmedBookingId && auth()->check()))
                <div class="mt-4 flex flex-wrap gap-3">
                    @if ($googleCalendarUrl)
                        <x-ui.button size="sm" variant="secondary" :href="$googleCalendarUrl">
                            {{ __('Add to Google Calendar') }}
                        </x-ui.button>
                    @endif
                    @if ($outlookCalendarUrl)
                        <x-ui.button size="sm" variant="secondary" :href="$outlookCalendarUrl">
                            {{ __('Add to Outlook') }}
                        </x-ui.button>
                    @endif
                    {{-- Behind auth: offering it to a guest sends them to a login screen. --}}
                    @if ($confirmedBookingId && auth()->check() && Route::has('booking.ics'))
                        <x-ui.button size="sm" variant="secondary" :href="route('booking.ics', $confirmedBookingId)">
                            {{ __('Add to Apple Calendar') }}
                        </x-ui.button>
                    @endif
                </div>
            @endif
        </div>
    @else
        <header>
            <p class="font-mono text-annotation uppercase text-ink-400">{{ __('Arrange a visit') }}</p>
            <h1 class="mt-3 font-display text-h2 font-bold tracking-tight text-ink-900">
                {{ __('Book a viewing') }}
            </h1>
            <p class="mt-4 max-w-reading text-body-l text-ink-500">
                {{ __('Pick a time that suits you. Booking one does not commit you to anything.') }}
            </p>
        </header>

        @if ($failure)
            <p role="alert"
               class="mt-6 rounded-sheet border border-fault-600 bg-fault-100 px-4 py-3 text-body-s text-fault-700">
                {{ $failure }}
            </p>
        @endif

        <form wire:submit.prevent="bookViewing" class="mt-8 grid gap-5 sm:grid-cols-2" novalidate>
            <x-ui.field id="selectedDate" :label="__('Date')" :error="$errors->first('selectedDate')">
                <select id="selectedDate" wire:model.live="selectedDate"
                        @if ($errors->has('selectedDate')) aria-invalid="true" aria-describedby="selectedDate-error" @endif
                        class="{{ $control }}{{ $errors->has('selectedDate') ? $invalid : '' }}">
                    <option value="">{{ __('Choose a date') }}</option>
                    @foreach ($availableDates as $date)
                        <option value="{{ $date }}">
                            {{ \Illuminate\Support\Carbon::parse($date)->format('l j F Y') }}
                        </option>
                    @endforeach
                </select>
            </x-ui.field>

            <x-ui.field id="selectedTime" :label="__('Time')"
                        :hint="$selectedDate ? null : __('Choose a date first.')"
                        :error="$errors->first('selectedTime')">
                <select id="selectedTime" wire:model="selectedTime"
                        @disabled(! $selectedDate)
                        @if ($errors->has('selectedTime')) aria-invalid="true" aria-describedby="selectedTime-error" @endif
                        class="{{ $control }}{{ $errors->has('selectedTime') ? $invalid : '' }} disabled:cursor-not-allowed disabled:bg-sheet-200">
                    <option value="">{{ __('Choose a time') }}</option>
                    @foreach ($availableTimeSlots as $slot)
                        <option value="{{ $slot }}">{{ $slot }}</option>
                    @endforeach
                </select>
            </x-ui.field>

            <x-ui.field id="userName" :label="__('Your name')" :error="$errors->first('userName')">
                <input type="text" id="userName" wire:model="userName" autocomplete="name"
                       @if ($errors->has('userName')) aria-invalid="true" aria-describedby="userName-error" @endif
                       class="{{ $control }}{{ $errors->has('userName') ? $invalid : '' }}">
            </x-ui.field>

            <x-ui.field id="userContact" :label="__('Phone')"
                        :hint="__('In case the agent needs you on the day.')"
                        :error="$errors->first('userContact')">
                <input type="tel" id="userContact" wire:model="userContact" autocomplete="tel"
                       aria-describedby="userContact-hint"
                       @if ($errors->has('userContact')) aria-invalid="true" @endif
                       class="{{ $control }}{{ $errors->has('userContact') ? $invalid : '' }}">
            </x-ui.field>

            <x-ui.field id="userEmail" :label="__('Email')" class="sm:col-span-2"
                        :hint="__('We send the confirmation here.')"
                        :error="$errors->first('userEmail')">
                <input type="email" id="userEmail" wire:model="userEmail" autocomplete="email"
                       aria-describedby="userEmail-hint"
                       @if ($errors->has('userEmail')) aria-invalid="true" @endif
                       class="{{ $control }}{{ $errors->has('userEmail') ? $invalid : '' }}">
            </x-ui.field>

            <x-ui.field id="notes" :label="__('Anything the agent should know')" class="sm:col-span-2"
                        :error="$errors->first('notes')">
                <textarea id="notes" wire:model="notes" rows="3"
                          placeholder="{{ __('Parking, access, or a question you want answered on the day.') }}"
                          @if ($errors->has('notes')) aria-invalid="true" aria-describedby="notes-error" @endif
                          class="{{ $control }}{{ $errors->has('notes') ? $invalid : '' }}"></textarea>
            </x-ui.field>

            <div class="flex flex-wrap items-center gap-4 sm:col-span-2">
                <x-ui.button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="bookViewing">{{ __('Book a viewing') }}</span>
                    <span wire:loading wire:target="bookViewing">{{ __('Booking…') }}</span>
                </x-ui.button>
                <p class="text-caption text-ink-400">{{ __('Viewings run seven days a week.') }}</p>
            </div>
        </form>
    @endif
</div>
