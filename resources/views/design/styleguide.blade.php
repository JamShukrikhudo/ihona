@extends('layouts.app')

@section('content')
    @php
        $sections = [
            'colour' => 'Colour',
            'typography' => 'Typography',
            'buttons' => 'Buttons',
            'fields' => 'Fields',
            'chips' => 'Chips',
            'energy' => 'Energy bands',
            'icons' => 'Icons',
        ];

        $ground = [
            'sheet-000' => 'Card faces, inputs, anything lifted off the page',
            'sheet-100' => 'Page ground. Vellum, not cream — the green cast is the point',
            'sheet-200' => 'Wells, table stripes, disabled fills',
            'sheet-300' => 'Every hairline: borders, rules, dimension lines',
            'sheet-400' => 'Placeholder text only. Never body copy',
        ];

        $ink = [
            'ink-900' => 'Headings, prices, disclosure values — 15.9:1 on vellum',
            'ink-700' => 'Body copy — 12.2:1',
            'ink-500' => 'Secondary copy, field labels — 6.3:1',
            'ink-400' => 'Annotation and metadata — 4.5:1, 12px minimum',
        ];

        $working = [
            'survey-500' => 'Act: primary buttons, focus rings, the live map pin',
            'survey-600' => 'Orange as text or on hover — the only orange safe for copy',
            'verdigris-600' => 'Verified: ID checked, compliance in date, offer accepted',
            'draft-700' => 'Reference: inline links, map geometry, informational notes',
            'caution-600' => 'Expiring: gas safety due, tenancy ending, EPC over 8 years old',
            'fault-600' => 'Failed: overdue compliance, rejected payment, destructive actions',
        ];

        // Whole class names, never "text-{$step}". Tailwind scans this file as
        // text; a class assembled at runtime is invisible to it, and the three
        // steps only used here would silently render at the default size — a
        // type scale that lies about the type scale.
        $scale = [
            ['text-mega font-display font-bold', 'mega', '56–128px', '£565,000'],
            ['text-h1 font-display font-bold', 'h1', '44–80px', 'Find the house, then the facts'],
            ['text-h2 font-display font-bold', 'h2', '36–52px', 'Recently reduced'],
            ['text-h3 font-display font-bold', 'h3', '32px', 'Similar homes nearby'],
            ['text-h4 font-display font-bold', 'h4', '24px', 'Book a viewing'],
            ['text-h5 font-display font-bold', 'h5', '20px', 'Tenure & charges'],
            ['text-body-l', 'body-l', '18px', 'Intro paragraphs and pull quotes'],
            ['text-body', 'body', '16px', 'Default running copy, max 65 characters'],
            ['text-body-s', 'body-s', '14px', 'Card copy, buttons, table cells'],
            ['text-caption', 'caption', '13px', 'Hints, helper text, disclosure values'],
            ['text-micro', 'micro', '12px', 'Legal lines, footnotes'],
        ];

        $icons = [
            'bedrooms', 'bathrooms', 'floor-area', 'floor-plan', 'aspect', 'certificate',
            'epc', 'transport', 'location', 'chain', 'viewing', 'price', 'tenure',
            'tour', 'property', 'enquiry', 'alert', 'search', 'chevron-right',
        ];
    @endphp

    <div class="mx-auto max-w-(--breakpoint-xl) px-4 py-band md:px-margin">
        <header class="border-b border-sheet-300 pb-4">
            <p class="font-mono text-annotation uppercase text-ink-400">
                {{ config('app.name') }} — visual design system
            </p>
            <h1 class="mt-2 font-display text-h2 font-bold tracking-tight text-ink-900">Survey Sheet</h1>
            <p class="mt-3 max-w-reading text-body text-ink-500">
                Rendered from the same components the public site uses, so it cannot drift into a
                lie about what the site looks like. Every number on a page is a fact the record
                already holds.
            </p>
            <nav class="mt-5 flex flex-wrap gap-2" aria-label="Styleguide sections">
                @foreach ($sections as $anchor => $title)
                    <a href="#{{ $anchor }}"
                       class="rounded-tag border border-sheet-300 px-3 py-1.5 font-mono text-annotation uppercase text-ink-500 transition-colors duration-[160ms] hover:border-ink-900 hover:text-ink-900">
                        {{ $title }}
                    </a>
                @endforeach
            </nav>
        </header>

        {{-- ============================ COLOUR ============================ --}}
        <section id="colour" class="pt-band">
            <x-design.heading index="01" title="Colour" />
            <p class="max-w-reading text-body text-ink-500">
                A cool green-grey drafting ground, graphite ink, and three working colours. Survey
                orange is rationed — target 3% of pixels or less. If a screen has two orange
                buttons, one of them is wrong.
            </p>

            @foreach ([
                'Ground — drafting film' => $ground,
                'Ink — graphite, green-black' => $ink,
                'Working colour — three jobs, no moods' => $working,
            ] as $groupTitle => $swatches)
                <h3 class="mt-8 mb-3 font-mono text-annotation uppercase text-ink-400">{{ $groupTitle }}</h3>
                <div class="grid grid-cols-[repeat(auto-fill,minmax(11rem,1fr))] gap-3">
                    @foreach ($swatches as $token => $use)
                        <div class="overflow-hidden rounded-sheet border border-sheet-300 bg-sheet-000">
                            <div class="h-16 border-b border-sheet-300"
                                 style="background:var(--color-{{ $token }});"></div>
                            <div class="p-3">
                                <p class="font-mono text-micro font-medium text-ink-900">{{ $token }}</p>
                                <p class="mt-1.5 text-caption text-ink-500">{{ $use }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </section>

        {{-- ========================== TYPOGRAPHY ========================== --}}
        <section id="typography" class="pt-band">
            <x-design.heading index="02" title="Typography" />
            <p class="max-w-reading text-body text-ink-500">
                Three faces, three jobs. The mono is not decoration — in this product the numbers
                <em>are</em> the content, and they have to align in a column of forty listings.
            </p>

            <div class="mt-8 grid gap-6 md:grid-cols-3">
                <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
                    <p class="font-display text-h4 font-bold text-ink-900" style="font-stretch:116%;">Archivo</p>
                    <p class="mt-2 text-caption text-ink-500">
                        Display · width 116% · weight 700–800. Grotesque with signage DNA — it reads
                        like a board outside a property. Headings and prices only, never below 20px.
                    </p>
                </div>
                <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
                    <p class="font-sans text-h4 font-semibold text-ink-900">Instrument Sans</p>
                    <p class="mt-2 text-caption text-ink-500">
                        Body · 400–700. Warmer and narrower than the usual UI grotesque, so dense
                        listing text stays readable at 14px.
                    </p>
                </div>
                <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
                    <p class="font-mono text-h5 font-medium text-ink-900">IBM Plex Mono</p>
                    <p class="mt-2 text-caption text-ink-500">
                        Data &amp; annotation · 400–600, tabular figures. Every measurement, label,
                        eyebrow, timestamp and reference.
                    </p>
                </div>
            </div>

            <div class="mt-8 rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
                <p class="mb-3 font-mono text-annotation uppercase text-ink-400">Scale</p>
                @foreach ($scale as [$classes, $step, $note, $sample])
                    <div class="flex flex-wrap items-baseline gap-4 border-t border-dashed border-sheet-300 py-2.5">
                        <span class="w-40 shrink-0 font-mono text-micro text-ink-400">{{ $step }} · {{ $note }}</span>
                        <span class="{{ $classes }} text-ink-900">{{ $sample }}</span>
                    </div>
                @endforeach
                <div class="flex flex-wrap items-baseline gap-4 border-t border-dashed border-sheet-300 py-2.5">
                    <span class="w-40 shrink-0 font-mono text-micro text-ink-400">annotation · 11px</span>
                    <span class="font-mono text-annotation uppercase text-ink-400">Eyebrows · field labels · units</span>
                </div>
            </div>

            <div class="mt-4 rounded-sheet border border-sheet-300 bg-sheet-000 p-5 font-mono tabular-nums text-body-s text-ink-900">
                <p>£565,000 &nbsp; 1,240 sq ft &nbsp; £456/sq ft &nbsp; EPC B 84</p>
                <p class="mt-1">£1,150 pcm &nbsp; &nbsp;682 sq ft &nbsp; £202/sq ft &nbsp; EPC C 71</p>
            </div>
        </section>

        {{-- =========================== BUTTONS =========================== --}}
        <section id="buttons" class="pt-band">
            <x-design.heading index="03" title="Buttons" />
            <p class="max-w-reading text-body text-ink-500">
                One primary per view. A control says exactly what happens when it is used — "Book a
                viewing", never "Submit" — and the confirmation reuses the same verb.
            </p>

            <div class="mt-8 space-y-6 rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
                <div>
                    <p class="mb-3 font-mono text-annotation uppercase text-ink-400">Variants</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-ui.button variant="primary">Book a viewing</x-ui.button>
                        <x-ui.button variant="secondary">Save to shortlist</x-ui.button>
                        <x-ui.button variant="ghost">Share</x-ui.button>
                        <x-ui.button variant="danger">Withdraw listing</x-ui.button>
                        <x-ui.button disabled>Viewing booked</x-ui.button>
                    </div>
                </div>
                <div>
                    <p class="mb-3 font-mono text-annotation uppercase text-ink-400">
                        Sizes — 44px minimum tap target on touch
                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-ui.button size="sm">Small</x-ui.button>
                        <x-ui.button>Default</x-ui.button>
                        <x-ui.button size="lg">Large</x-ui.button>
                    </div>
                </div>
                <div>
                    <p class="mb-3 font-mono text-annotation uppercase text-ink-400">
                        With icon — leading for actions, trailing for navigation
                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-ui.button><x-ui.icon name="viewing" />Book a viewing</x-ui.button>
                        <x-ui.button variant="secondary"><x-ui.icon name="floor-plan" />View floor plan</x-ui.button>
                        <x-ui.button variant="ghost" href="/properties">
                            Browse properties<x-ui.icon name="chevron-right" />
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================ FIELDS ============================ --}}
        <section id="fields" class="pt-band">
            <x-design.heading index="04" title="Fields" />
            <p class="max-w-reading text-body text-ink-500">
                Labels are mono annotations above the field, the way a drawing labels a dimension.
                Placeholders are examples, never labels. Errors say what happened and what to do
                about it, in the interface's voice.
            </p>

            @php
                $control = 'w-full rounded-sheet border border-sheet-300 bg-sheet-000 px-3.5 py-[11px]'
                    .' font-sans text-body-s text-ink-900 placeholder:text-sheet-400'
                    .' transition-[border-color,box-shadow] duration-[160ms]'
                    .' hover:border-ink-400 focus:border-survey-500 focus:outline-none';
            @endphp

            <div class="mt-8 grid gap-5 rounded-sheet border border-sheet-300 bg-sheet-000 p-5 md:grid-cols-2">
                <x-ui.field id="sg-name" label="Full name">
                    <input id="sg-name" class="{{ $control }}" placeholder="Alex Whitmore">
                </x-ui.field>

                <x-ui.field id="sg-email" label="Email" error="Add the part after the @ so we can send the confirmation.">
                    <input id="sg-email" type="email" value="alex@" aria-invalid="true"
                           aria-describedby="sg-email-error"
                           class="{{ $control }} border-fault-600">
                </x-ui.field>

                <x-ui.field id="sg-date" label="Preferred viewing date" hint="Saturday viewings run 09:00–13:00.">
                    <input id="sg-date" type="date" aria-describedby="sg-date-hint" class="{{ $control }}">
                </x-ui.field>

                <x-ui.field id="sg-position" label="Position"
                            hint="Sellers see this. It is the single strongest signal on an offer.">
                    <select id="sg-position" aria-describedby="sg-position-hint" class="{{ $control }}">
                        <option>First-time buyer</option>
                        <option selected>Sold, nothing to sell</option>
                        <option>Cash buyer</option>
                    </select>
                </x-ui.field>

                <x-ui.field id="sg-ref" label="Reference — read only" class="md:col-span-2">
                    <input id="sg-ref" value="ENQ-2026-08-4417" disabled
                           class="{{ $control }} cursor-not-allowed bg-sheet-200 text-ink-400">
                </x-ui.field>
            </div>
        </section>

        {{-- ============================ CHIPS ============================ --}}
        <section id="chips" class="pt-band">
            <x-design.heading index="05" title="Chips" />
            <p class="max-w-reading text-body text-ink-500">
                A chip states a fact with a number — never a mood. Tone carries meaning alongside
                the words, never instead of them.
            </p>
            <div class="mt-8 flex flex-wrap items-center gap-3 rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
                <x-ui.chip tone="new">New — 2 days</x-ui.chip>
                <x-ui.chip tone="verified">Chain-free</x-ui.chip>
                <x-ui.chip tone="info">Auction</x-ui.chip>
                <x-ui.chip tone="caution">Reduced £15k</x-ui.chip>
                <x-ui.chip tone="fault">Gas safety overdue</x-ui.chip>
            </div>
        </section>

        {{-- ============================ ENERGY ============================ --}}
        <section id="energy" class="pt-band">
            <x-design.heading index="06" title="Energy bands" />
            <p class="max-w-reading text-body text-ink-500">
                These seven colours are set by the certificate itself. They sit outside the palette
                deliberately: recognising the exact green of a band B is part of why the disclosure
                strip earns trust. The letter is always rendered, so colour never carries the
                meaning alone.
            </p>
            <div class="mt-8 flex flex-wrap items-center gap-2 rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
                @foreach (['A' => 92, 'B' => 84, 'C' => 71, 'D' => 63, 'E' => 48, 'F' => 33, 'G' => 17] as $band => $score)
                    <span class="flex items-center gap-1.5">
                        <x-ui.epc-band :band="$band" :score="$score" />
                        <span class="font-mono tabular-nums text-caption text-ink-700">{{ $score }}</span>
                    </span>
                @endforeach
                <span class="ml-3 border-l border-sheet-300 pl-3">
                    <x-ui.epc-band band="" score="" />
                </span>
            </div>
        </section>

        {{-- ============================= ICONS ============================= --}}
        <section id="icons" class="pt-band pb-band">
            <x-design.heading index="07" title="Icons" />
            <p class="max-w-reading text-body text-ink-500">
                Drawn on a 24px grid, 1.5px stroke, square caps and mitred joins — the geometry of a
                technical pen, not a rounded lifestyle set. Icons never appear without a label
                except in a control the reader has already learned.
            </p>
            <div class="mt-8 grid grid-cols-[repeat(auto-fill,minmax(7rem,1fr))] gap-2">
                @foreach ($icons as $icon)
                    <div class="flex flex-col items-center gap-2.5 rounded-sheet border border-sheet-300 bg-sheet-000 p-5 transition-colors duration-[160ms] hover:border-ink-900">
                        <x-ui.icon :name="$icon" class="size-7 text-ink-900" />
                        <span class="font-mono text-[10px] text-ink-400">{{ $icon }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
