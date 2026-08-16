@if (filled($property->description))
    <div class="mt-8 max-w-reading">
        <p class="whitespace-pre-line text-body text-ink-700">{{ $property->description }}</p>

        @if ($property->description_generated_at)
            {{-- Sentences a model produced read exactly like sentences an agent
                 walked round the house to write. Which one it was changes how
                 much weight they carry. --}}
            <x-ui.model-note class="mt-3"
                             :label="__('Written by a model')"
                             :dated="$property->description_generated_at" />
        @endif
    </div>
@endif
