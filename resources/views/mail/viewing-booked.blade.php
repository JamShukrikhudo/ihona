<x-mail::message>
# {{ __('Viewing booked') }}

{{ __('Thank you :name — your viewing is confirmed.', ['name' => $booking->name]) }}

@if ($booking->property)
**{{ __('Property') }}:** {{ $booking->property->title }}
@endif
**{{ __('Date') }}:** {{ $booking->date?->format('l j F Y') }}
**{{ __('Time') }}:** {{ $booking->time?->format('H:i') }}

@if ($booking->notes)
**{{ __('Your note') }}:** {{ $booking->notes }}
@endif

{{ __('If you need to change or cancel it, reply to this email or call us and we will sort it out.') }}

@if ($booking->property)
<x-mail::button :url="route('property.detail', $booking->property->id)">
{{ __('View the listing') }}
</x-mail::button>
@endif
</x-mail::message>
