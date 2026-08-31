<form wire:submit="submitReview" class="space-y-4">
    <div aria-label="{{ $rating }} out of 5" role="radiogroup">
        <label for="party-review-rating">Rating</label>
        <input id="party-review-rating" type="number" min="1" max="5" wire:model="rating">
    </div>
    <label for="party-review-comment">Comment</label>
    <textarea id="party-review-comment" wire:model="comment"></textarea>
    @error('rating') <span>{{ $message }}</span> @enderror
    @error('comment') <span>{{ $message }}</span> @enderror
    <button type="submit">Submit review</button>
    @if ($message) <p role="status">{{ $message }}</p> @endif
</form>
