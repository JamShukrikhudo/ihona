<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Parties\Application\SubmitPartyReview;
use Liberu\RealEstate\Parties\Models\Party;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PartyReviewForm extends Component
{
    public int|string $partyId;
    public string $partyRole = '';

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 1;

    #[Validate('required|string|min:10|max:1000')]
    public string $comment = '';

    public ?string $message = null;

    public function mount(int|string $partyId): void
    {
        $this->partyId = $partyId;
        $this->partyRole = (string) $this->party()->type->value;
    }

    public function submitReview(SubmitPartyReview $submitPartyReview): void
    {
        $this->validate();
        $user = Auth::user();
        $teamId = $user?->current_team_id;

        if (! $user || ! $teamId) {
            throw ValidationException::withMessages(['review' => 'Select a team before submitting a review.']);
        }

        $review = $submitPartyReview->handle($teamId, $user->getAuthIdentifier(), $this->partyId, [
            'rating' => $this->rating,
            'comment' => $this->comment,
            'ip_address' => request()->ip(),
        ]);

        $this->reset('comment');
        $this->rating = 1;
        $this->message = 'Your review was submitted for moderation.';
        $this->dispatch('reviewAdded', reviewId: $review->getKey());
    }

    public function render(): View
    {
        return view('real-estate-parties-livewire::party-review-form', ['party' => $this->party()]);
    }

    private function party(): Party
    {
        $teamId = Auth::user()?->current_team_id;
        abort_unless($teamId, 403);

        return Party::query()->forTeam($teamId)->whereKey($this->partyId)->firstOrFail();
    }
}
