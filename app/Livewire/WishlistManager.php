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
            session()->flash('success', 'Property removed from wishlist successfully');
            $this->dispatch('favoriteRemoved');
        }
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
