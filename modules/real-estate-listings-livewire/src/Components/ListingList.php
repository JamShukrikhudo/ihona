<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ListingsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Listings\Application\TransitionListing;
use Liberu\RealEstate\Listings\Application\UpdateListingSection;
use Liberu\RealEstate\Listings\Domain\ListingSection;
use Liberu\RealEstate\Listings\Domain\ListingStatus;
use Liberu\RealEstate\Listings\Models\Listing;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ListingList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public ?string $error = null;

    /** @param array<string, mixed> $value */
    public function updateSection(int $listingId, string $section, array $value): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $listing = Listing::query()->forTeam($teamId)->findOrFail($listingId);
        app(UpdateListingSection::class)->handle($listing, $teamId, ListingSection::from($section), $value);
    }

    public function markReady(int $listingId, TransitionListing $transition): void
    {
        $this->transition($listingId, ListingStatus::Ready, $transition);
    }

    public function publish(int $listingId, TransitionListing $transition): void
    {
        $this->transition($listingId, ListingStatus::Published, $transition);
    }

    public function suspend(int $listingId, TransitionListing $transition): void
    {
        $this->transition($listingId, ListingStatus::Suspended, $transition);
    }

    public function withdraw(int $listingId, TransitionListing $transition): void
    {
        $this->transition($listingId, ListingStatus::Withdrawn, $transition);
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $listings = $teamId === null ? collect() : Listing::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('title', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-listings-livewire::listing-list', ['listings' => $listings]);
    }

    private function transition(int $listingId, ListingStatus $status, TransitionListing $transition): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        try {
            $transition->handle(
                Listing::query()->forTeam($user->current_team_id)->findOrFail($listingId),
                $user->current_team_id,
                $status,
            );
            $this->error = null;
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
        }
    }
}
