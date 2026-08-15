@php
    // Only the providers this deployment can actually complete a sign-in with.
    //
    // config/socialstream.php lists nine, including Bitbucket, GitLab and
    // Slack, and config/services.php holds credentials for none of them — so
    // the sign-in page offered nine buttons, every one of which led to
    // Socialite throwing on a missing client id. A provider with no client id
    // is not a way in, and a property audience being offered a GitLab login is
    // its own kind of answer about who built the site.
    $providers = collect(\JoelButcher\Socialstream\Socialstream::providers())
        ->filter(fn (array $provider) => filled(config("services.{$provider['id']}.client_id")))
        ->values();
@endphp

{{--
    The other ways in.

    Published from the package and then left on its defaults, so it carried
    grey-on-grey borders and Jetstream's ink scale onto every sign-in screen.
    On the design system now, and the divider is drawn here rather than by the
    page — both pages used to draw a second one above it.
--}}
@if ($providers->isNotEmpty())
    <div class="mt-6">
        <div class="flex items-center gap-4">
            <span class="h-px grow bg-sheet-300"></span>
            <span class="font-mono text-annotation uppercase text-ink-500">
                {{ config('socialstream.prompt', 'Or Login Via') }}
            </span>
            <span class="h-px grow bg-sheet-300"></span>
        </div>

        @error('socialstream')
            <p role="alert" class="mt-3 text-center text-body-s text-fault-600">{{ $message }}</p>
        @enderror

        <div class="mt-4 grid gap-3">
            @foreach ($providers as $provider)
                <a href="{{ route('oauth.redirect', $provider['id']) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-sheet border border-sheet-300 bg-sheet-000 px-4 py-2.5
                          text-body-s font-medium text-ink-900 transition-[border-color,box-shadow] duration-[160ms]
                          hover:border-ink-900 hover:shadow-lift-1">
                    <x-socialstream-icons.provider-icon :provider="$provider['id']" class="size-5" />
                    {{ $provider['buttonLabel'] }}
                </a>
            @endforeach
        </div>
    </div>
@endif
