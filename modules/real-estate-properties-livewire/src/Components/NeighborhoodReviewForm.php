<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Application\SubmitNeighborhoodReview;
use Liberu\RealEstate\Properties\Models\Neighborhood;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class NeighborhoodReviewForm extends Component
{
    public int|string $neighborhoodId;

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 5;

    #[Validate('required|string|min:3|max:100')]
    public string $title = '';

    #[Validate('required|string|min:10|max:1000')]
    public string $comment = '';

    public ?string $message = null;

    public function mount(int|string $neighborhoodId): void
    {
        $this->neighborhoodId = $neighborhoodId;
        $this->neighborhood();
    }

    public function submitReview(SubmitNeighborhoodReview $submitNeighborhoodReview): void
    {
        $this->validate();
        $user = Auth::user();
        $teamId = $user?->current_team_id;

        if (! $user || ! $teamId) {
            throw ValidationException::withMessages(['review' => 'Select a team before submitting a review.']);
        }

        $review = $submitNeighborhoodReview->handle($teamId, $user->getAuthIdentifier(), $this->neighborhoodId, [
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'ip_address' => request()->ip(),
        ]);

        $this->reset(['title', 'comment']);
        $this->rating = 5;
        $this->message = 'Your review was submitted for moderation.';
        $this->dispatch('reviewAdded', reviewId: $review->getKey());
    }

    public function render(): View
    {
        return view('real-estate-properties-livewire::neighborhood-review-form', ['neighborhood' => $this->neighborhood()]);
    }

    private function neighborhood(): Neighborhood
    {
        $teamId = Auth::user()?->current_team_id;

        abort_unless($teamId, 403);

        return Neighborhood::query()->forTeam($teamId)->whereKey($this->neighborhoodId)->firstOrFail();
    }
}
