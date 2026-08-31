@extends('layouts.app')

@section('content')
    <div class="re-container re-section">
        <article class="max-w-reading">
            <p class="re-eyebrow">{{ __('Legal') }}</p>
            <h1>{{ __('Privacy Policy') }}</h1>
            <p class="re-lede">{{ __('A plain-language overview of how information is handled when you use this platform.') }}</p>

            <h2>{{ __('Information we collect') }}</h2>
            <p>{{ __('We collect information you provide, such as contact details, property preferences and messages, together with the technical information needed to operate and secure the service.') }}</p>

            <h2>{{ __('How we use it') }}</h2>
            <p>{{ __('We use information to provide features, respond to enquiries, improve the platform, protect accounts and meet legal obligations. We do not sell personal information.') }}</p>

            <h2>{{ __('Your choices') }}</h2>
            <p>{{ __('You can ask about the personal information we hold about you, request corrections or ask us to remove it where the law allows. Contact us to make a request.') }}</p>

            <h2>{{ __('Updates') }}</h2>
            <p>{{ __('We may update this policy when the service or legal requirements change. The current version will always be published on this page.') }}</p>
        </article>
    </div>
@endsection
