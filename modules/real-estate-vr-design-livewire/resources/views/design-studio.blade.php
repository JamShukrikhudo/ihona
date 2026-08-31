<section aria-label="VR property design studio">
    <h2>{{ $property->title ?: $property->address }}</h2>
    @if ($message)<p role="status">{{ $message }}</p>@endif
    <form wire:submit="saveDesign">
        <label for="vr-design-name">Design name</label>
        <input id="vr-design-name" type="text" wire:model="designName">
        <label for="vr-design-description">Description</label>
        <textarea id="vr-design-description" wire:model="designDescription"></textarea>
        <label for="vr-design-style">Style</label>
        <select id="vr-design-style" wire:model="designStyle">
            <option value="">Choose a style</option>
            @foreach ($styles as $key => $style)<option value="{{ $key }}">{{ $style['name'] }}</option>@endforeach
        </select>
        <button type="submit">Create design</button>
    </form>
    <ul aria-label="Saved VR designs">
        @forelse ($designs as $design)
            <li wire:key="vr-design-{{ $design->getKey() }}">{{ $design->name }} <button type="button" wire:click="$set('selectedDesignId', {{ $design->getKey() }})">Select</button></li>
        @empty
            <li>No designs created yet.</li>
        @endforelse
    </ul>
</section>
