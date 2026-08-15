@props(['property'])

@php
    $items = $property->gallery();
@endphp

@if ($items->isEmpty())
    {{-- No photograph on the record. The elevation drawing says "this is a
         building we hold a record for", where a grey box says "broken". --}}
    <div {{ $attributes->class('overflow-hidden rounded-sheet border border-sheet-300 bg-sheet-200') }}>
        <div class="aspect-3/2">
            <x-property-elevation :seed="$property->id" />
        </div>
    </div>
@else
    {{--
        One crop ratio for every tile, plan and photograph alike. A gallery of
        mixed crops reads as a jumble of borrowed pictures; one ratio is what
        makes a set look like one property.

        Every tile is a real link to the file, so the gallery works with no
        JavaScript at all — Alpine only intercepts the click to swap the lead
        image in place.
    --}}
    <div {{ $attributes->merge(['class' => 'flex flex-col gap-3']) }}
         x-data="{ shown: 0, count: {{ $items->count() }} }"
         @keydown.left.prevent="shown = (shown - 1 + count) % count"
         @keydown.right.prevent="shown = (shown + 1) % count"
         role="group"
         aria-roledescription="{{ __('Gallery') }}"
         aria-label="{{ __('Photographs and plans of :title', ['title' => $property->title]) }}">
        <figure class="m-0 flex flex-col gap-2">
            <div class="relative overflow-hidden rounded-sheet border border-sheet-300 bg-sheet-200">
                <div class="aspect-3/2">
                    @foreach ($items as $index => $item)
                        <img src="{{ $item->url }}"
                             alt="{{ $item->alt() }}"
                             @if ($index > 0) loading="lazy" @endif
                             x-show="shown === {{ $index }}"
                             @if ($index > 0) style="display:none" @endif
                             class="h-full w-full {{ $item->isPlan() ? 'bg-sheet-000 object-contain' : 'object-cover' }}" />
                    @endforeach
                </div>

                @foreach ($items as $index => $item)
                    @if ($item->isPlan() || $item->staged)
                        <div class="absolute left-2.5 top-2.5"
                             x-show="shown === {{ $index }}"
                             @if ($index > 0) style="display:none" @endif>
                            @if ($item->staged)
                                {{-- A furnished room nobody has ever stood in.
                                     Saying so on the picture is the whole point;
                                     saying it in the small print is not. --}}
                                <x-ui.chip tone="caution">
                                    @if ($item->stagingStyle)
                                        {{ __('Virtually staged — :style', ['style' => ucfirst($item->stagingStyle)]) }}
                                    @else
                                        {{ __('Virtually staged') }}
                                    @endif
                                </x-ui.chip>
                            @else
                                <x-ui.chip tone="info">{{ ucfirst($item->kind) }}</x-ui.chip>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <figcaption class="flex min-h-5 items-baseline justify-between gap-3 font-mono text-annotation uppercase text-ink-500">
                <span class="truncate">
                    @foreach ($items as $index => $item)
                        <span x-show="shown === {{ $index }}" @if ($index > 0) style="display:none" @endif>
                            {{ $item->caption ?? ucfirst($item->kind) }}
                        </span>
                    @endforeach
                </span>
                @if ($items->count() > 1)
                    <span class="shrink-0 tabular-nums" aria-hidden="true">
                        <span x-text="shown + 1">1</span>/{{ $items->count() }}
                    </span>
                @endif
            </figcaption>
        </figure>

        @if ($items->count() > 1)
            <ul class="m-0 grid list-none grid-cols-4 gap-2 p-0 sm:grid-cols-5">
                @foreach ($items as $index => $item)
                    <li>
                        <a href="{{ $item->url }}"
                           @click.prevent="shown = {{ $index }}"
                           :aria-current="shown === {{ $index }} ? 'true' : 'false'"
                           class="block overflow-hidden rounded-sheet border border-sheet-300 transition-[border-color,opacity] duration-[160ms] hover:border-ink-400 aria-[current=true]:border-survey-500"
                           title="{{ $item->caption ?? ucfirst($item->kind) }}">
                            <span class="sr-only">{{ $item->caption ?? ucfirst($item->kind) }}</span>
                            <div class="aspect-3/2">
                                <img src="{{ $item->url }}"
                                     alt=""
                                     loading="lazy"
                                     class="h-full w-full {{ $item->isPlan() ? 'bg-sheet-000 object-contain' : 'object-cover' }}" />
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
