<section aria-label="Review {{ $property->title ?: $property->address }}">
    <h2>Share your experience</h2>

    @if ($message)
        <p role="status">{{ $message }}</p>
    @endif

    <form wire:submit="submitReview">
        <fieldset>
            <legend>Rating</legend>

            @for ($value = 1; $value <= 5; $value++)
                <button
                    type="button"
                    wire:click="$set('rating', {{ $value }})"
                    aria-label="{{ $value }} out of 5"
                    aria-pressed="{{ $rating === $value ? 'true' : 'false' }}"
                >★</button>
            @endfor
        </fieldset>

        @error('rating') <p>{{ $message }}</p> @enderror

        <label for="property-review-title">Title</label>
        <input id="property-review-title" type="text" wire:model="title">
        @error('title') <p>{{ $message }}</p> @enderror

        <label for="property-review-comment">Comment</label>
        <textarea id="property-review-comment" wire:model="comment"></textarea>
        @error('comment') <p>{{ $message }}</p> @enderror
        @error('review') <p>{{ $message }}</p> @enderror

        <button type="submit">Submit review</button>
    </form>
</section>
