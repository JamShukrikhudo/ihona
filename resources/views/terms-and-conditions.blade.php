@extends('layouts.app')

@section('content')
    <div class="re-container re-section">
        <article class="max-w-reading">
            <p class="re-eyebrow">{{ __('Legal') }}</p>
            <h1>{{ __('Terms and Conditions') }}</h1>
            <p class="re-lede">{{ __('These terms explain the basic rules for using this property platform.') }}</p>

            <h2>{{ __('Using the platform') }}</h2>
            <p>{{ __('You agree to use the platform lawfully, respectfully and only for purposes connected with property discovery, communication and management.') }}</p>

            <h2>{{ __('Listings and information') }}</h2>
            <p>{{ __('We work to keep information useful and current, but listings and estimates may change. Verify important details independently before making a financial or legal decision.') }}</p>

            <h2>{{ __('Accounts and communication') }}</h2>
            <p>{{ __('Keep your account details secure and ensure that information you submit is accurate. Do not use the platform to send unlawful, misleading or abusive material.') }}</p>

            <h2>{{ __('Changes and questions') }}</h2>
            <p>{{ __('We may update these terms as the service develops. If you have a question about them, please use our <a href="'.route('contact.show').'">contact page</a>.') }}</p>
        </article>
    </div>
@endsection
