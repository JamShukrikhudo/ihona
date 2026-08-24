<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OffersLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Offers\Application\TransitionOffer;
use Liberu\RealEstate\Offers\Domain\OfferStatus;
use Liberu\RealEstate\Offers\Models\Offer;
use Livewire\Component;

final class OfferList extends Component
{
    public string $search = '';

    public function submitOffer(int $offerId): void
    {
        $this->transition($offerId, OfferStatus::Submitted);
    }

    public function acceptOffer(int $offerId): void
    {
        $this->transition($offerId, OfferStatus::Accepted);
    }

    public function rejectOffer(int $offerId): void
    {
        $this->transition($offerId, OfferStatus::Rejected);
    }

    private function transition(int $offerId, OfferStatus $status): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        try {
            app(TransitionOffer::class)->handle(Offer::query()->forTeam($user->current_team_id)->findOrFail($offerId), $user->current_team_id, $user->getAuthIdentifier(), $status);
            $this->dispatch('offer-updated');
        } catch (\Throwable $exception) {
            $this->addError('offer', $exception->getMessage());
        }
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $offers = $teamId === null ? collect() : Offer::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('subject', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-offers-livewire::offer-list', ['offers' => $offers]);
    }
}
