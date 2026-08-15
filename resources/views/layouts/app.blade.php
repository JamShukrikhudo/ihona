<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ app(\App\Settings\GeneralSettings::class)->site_name }}</title>

    {{-- Stamp a stored theme choice before anything paints. With no stored
         choice the document stays unstamped and `color-scheme: light dark`
         follows the operating system on its own. --}}
    <script>
        try {
            var t = localStorage.getItem('theme');
            if (t === 'light' || t === 'dark') document.documentElement.dataset.theme = t;
        } catch (e) {}
    </script>

    @if(config('googletagmanager.id'))
        @include('googletagmanager::head')
    @endif

    {{-- app.js stays in the head: Leaflet is imported here and the inline map
         script in the property-map component runs before the bottom bundle. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @yield('styles')
    @stack('styles')
</head>
<body class="font-sans antialiased">
    @if(config('googletagmanager.id'))
        @include('googletagmanager::body')
    @endif

    <div class="min-h-screen bg-sheet-100 flex flex-col">
        @include('components.home-navbar')

        <main class="grow">
            @yield('content'){{ $slot ?? '' }}
        </main>

        @include('components.footer')
    </div>

    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')

    <!-- Scripts -->
    @vite('resources/js/app.js')
    @livewireScripts

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');

                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    menu.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!dropdown.contains(e.target)) {
                        menu.classList.add('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>
