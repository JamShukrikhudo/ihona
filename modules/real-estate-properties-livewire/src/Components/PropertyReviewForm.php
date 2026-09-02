<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Application\SubmitPropertyReview;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class PropertyReviewForm extends Component
{
    public int|string $propertyId;

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 5;

    #[Validate('required|string|min:3|max:100')]
    public string $title = '';

    #[Validate('required|string|min:10|max:1000')]
    public string $comment = '';

    public ?string $message = null;

    public function mount(int|string $propertyId): void
    {
        $this->propertyId = $propertyId;
        $this->property();
    }

    public function submitReview(SubmitPropertyReview $submitPropertyReview): void
    {
        $this->validate();
        $user = Auth::user();
        $teamId = $user?->current_team_id;

        if (! $user || ! $teamId) {
            throw ValidationException::withMessages(['review' => 'Select a team before submitting a review.']);
        }

        $review = $submitPropertyReview->handle($teamId, $user->getAuthIdentifier(), $this->propertyId, [
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'ip_address' => request()->ip(),
        ]);

        $this->reset(['title', 'comment']);
        $this->rating = 5;
        $this->message = 'Your review was submitted for moderation.';
        $this->dispatch('property-review-submitted', reviewId: $review->getKey());
    }

    public function render(): View
    {
        return view('real-estate-properties-livewire::property-review-form', [
            'property' => $this->property(),
        ]);
    }

    private function property(): Property
    {
        $user = Auth::user();
        $teamId = $user?->current_team_id;

        abort_unless($teamId, 403);

        return Property::query()->forTeam($teamId)->whereKey($this->propertyId)->firstOrFail();
    }
}
