<x-mail::message>
# {{ __('New enquiry') }}

@if ($enquiry->property)
**{{ __('About') }}:** [{{ $enquiry->property->title }}]({{ route('property.detail', $enquiry->property->id) }})
@endif

**{{ __('From') }}:** {{ $enquiry->name }}
**{{ __('Email') }}:** {{ $enquiry->email }}
@if ($enquiry->phone)
**{{ __('Phone') }}:** {{ $enquiry->phone }}
@endif
@if ($enquiry->interest)
**{{ __('Interest') }}:** {{ $enquiry->interest }}
@endif

---

{{ $enquiry->message }}

<x-mail::button :url="'mailto:'.$enquiry->email">
{{ __('Reply to :name', ['name' => $enquiry->name]) }}
</x-mail::button>
</x-mail::message>
