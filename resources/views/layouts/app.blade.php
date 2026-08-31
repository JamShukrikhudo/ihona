<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title="{{ $title ?? config('app.name') }}</title>
    @vite('resources/js/app.js')
    @livewireStyles
</head>
<body>
    <a class="skip-link" href="#main-content">{{ __('Skip to content') }}</a>
    <main id="main-content" tabindex="-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>
    @livewireScripts
    @stack('scripts')
</body>
</html>
