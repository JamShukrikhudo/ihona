<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class WishlistManager extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';

    /** Confirmation for the last removal. Cleared by the next interaction. */
    public ?string $removed = null;

    protected $listeners = ['favoriteAdded' => '$refresh', 'favoriteRemoved' => '$refresh'];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
    ];

    public function updatingSearch()
    {
        $this->removed = null;
        $this->resetPage();
    }

    public function removeFavorite($propertyId)
    {
        $user = Auth::user();
        
        $favorite = Favorite::where('user_id', $user->id)
            ->where('property_id', $propertyId)
            ->first();

        if ($favorite) {
            $favorite->delete();

            // Held on the component, not flashed: a Livewire action does not
            // redirect, so a flashed message survived to the next full page
            // load and could surface somewhere unrelated.
            $this->removed = __('Removed from your shortlist.');

            // Removing the last item on a page would otherwise leave the
            // reader on an empty page that still exists.
            $this->resetPage();

            $this->dispatch('favoriteRemoved');
        }
    }

    /**
     * Each column has a sensible direction of its own: addresses read A to Z,
     * prices cheapest first, and "recently saved" newest first. The select
     * writes only sortBy, so without this the direction kept its 'desc'
     * default and sorting by address ran Z to A.
     */
    public function updatedSortBy(): void
    {
        $this->removed = null;
        $this->resetPage();
    }

    /**
     * Derived, not set by the hook. A hook only fires on a client-side update,
     * so /wishlist?sortBy=title mounted with the 'desc' default and still
     * sorted Z to A — the exact bug the hook was added to fix, reachable by
     * anyone sharing a link.
     */
    private function directionFor(string $sort): string
    {
        return $sort === 'created_at' ? 'desc' : 'asc';
    }


    public function render()
    {
        $user = Auth::user();
        
        // 'media' is Spatie's relation, which the card reads for its photograph.
        $query = $user->favoriteProperties()
            ->with(['images', 'media', 'neighborhood', 'features', 'category']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('postal_code', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        if ($this->sortBy === 'price') {
            $query->orderBy('price', $this->directionFor('price'));
        } elseif ($this->sortBy === 'title') {
            $query->orderBy('title', $this->directionFor('title'));
        } else {
            // Sort by when it was saved. favoriteProperties() is a belongsToMany
            // through the same table, so joining it again made every favorites
            // column ambiguous — which meant the default sort, and therefore the
            // page's default view, threw.
            $query->orderByPivot('created_at', $this->directionFor('created_at'));
        }

        $favorites = $query->paginate(12);

        return view('livewire.wishlist-manager', [
            'favorites' => $favorites,
            'totalFavorites' => $user->favorites()->count(),
        ]);
    }
}
