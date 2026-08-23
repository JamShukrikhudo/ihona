<div>
    <label for="media-document-search">Search media and documents</label>
    <input id="media-document-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($documents as $document)
            <li>{{ $document->title ?: $document->path }} ({{ $document->kind }})</li>
        @empty
            <li>No media or documents found.</li>
        @endforelse
    </ul>
    {{ $documents->links() }}
</div>
