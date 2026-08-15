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
    public $sortDirection = 'desc';

    /** Confirmation for the last removal, shown once on the page. */
    public ?string $removed = null;

    protected $listeners = ['favoriteAdded' => '$refresh', 'favoriteRemoved' => '$refresh'];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
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
        $this->sortDirection = $this->sortBy === 'created_at' ? 'desc' : 'asc';
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
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
            $query->orderBy('price', $this->sortDirection);
        } elseif ($this->sortBy === 'title') {
            $query->orderBy('title', $this->sortDirection);
        } else {
            // Sort by when it was saved. favoriteProperties() is a belongsToMany
            // through the same table, so joining it again made every favorites
            // column ambiguous — which meant the default sort, and therefore the
            // page's default view, threw.
            $query->orderByPivot('created_at', $this->sortDirection);
        }

        $favorites = $query->paginate(12);

        return view('livewire.wishlist-manager', [
            'favorites' => $favorites,
            'totalFavorites' => $user->favorites()->count(),
        ]);
    }
}
