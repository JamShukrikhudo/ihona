@props([
    'title' => null,
    'lede' => null,
    'wide' => false,
])

{{-- The shell every sign-in screen sits in. Errors are collected here rather
     than per field: Fortify reports a failed sign-in against `email` with a
     message about the pair. --}}
<section class="mx-auto flex w-full flex-col items-center px-4 py-10 md:px-margin md:py-16">
    <div class="w-full {{ $wide ? 'max-w-lg' : 'max-w-sm' }}">
        @if ($title)
            <h1 class="font-display text-h4 font-bold tracking-tight text-ink-900">{{ $title }}</h1>
        @endif

        @if ($lede)
            <p class="mt-2 max-w-reading text-body-s text-ink-500">{{ $lede }}</p>
        @endif

        @if (session('status'))
            <p role="status"
               class="mt-5 rounded-sheet border border-verdigris-600 bg-verdigris-100 px-4 py-3 text-body-s text-verdigris-700">
                {{ session('status') }}
            </p>
        @endif

        @if ($errors->any())
            <div role="alert"
                 class="mt-5 rounded-sheet border border-fault-600 bg-fault-100 px-4 py-3">
                <p class="flex items-center gap-2 text-body-s font-medium text-fault-700">
                    <x-ui.icon name="alert" class="size-4 shrink-0" />
                    {{ __('That did not go through') }}
                </p>
                <ul class="mt-1.5 space-y-1 text-body-s text-fault-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-5 rounded-sheet border border-sheet-300 bg-sheet-000 p-5 shadow-lift-1 sm:p-6">
            {{ $slot }}
        </div>

        @if (isset($footer))
            <div class="mt-4 text-center text-body-s text-ink-500">
                {{ $footer }}
            </div>
        @endif
    </div>
</section>
