@props([
    'id' => null,
    'label' => null,
    'hint' => null,
    'error' => null,
])

{{--
    Wraps a control rather than reimplementing one, so an input, select,
    textarea or a Livewire control all get the same treatment.

    The hint and error ids are derived from the field id, so the control wires
    itself with aria-describedby="{id}-hint {id}-error" and aria-invalid="true".
--}}
<div {{ $attributes->class('flex flex-col') }}>
    @if ($label)
        <label @if ($id) for="{{ $id }}" @endif
               class="mb-1.5 font-mono text-annotation uppercase text-ink-500">
            {{ $label }}
        </label>
    @endif

    {{ $slot }}

    @if ($hint)
        <p @if ($id) id="{{ $id }}-hint" @endif class="mt-1.5 text-caption text-ink-400">
            {{ $hint }}
        </p>
    @endif

    @if ($error)
        {{-- Errors name the fix, in the interface's voice. No apology, no "Oops". --}}
        <p @if ($id) id="{{ $id }}-error" @endif
           class="mt-1.5 flex items-center gap-1.5 text-caption text-fault-600">
            <x-ui.icon name="alert" class="size-3.5 shrink-0" />
            {{ $error }}
        </p>
    @endif
</div>
