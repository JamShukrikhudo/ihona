<?php

namespace App\Livewire;

use Log;
use Exception;
use Livewire\Component;
use App\Models\Property;
use App\Models\Favorite;
use App\Models\PropertyFeature;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Services\PropertyFeatureService;

class PropertyList extends Component
{
    use WithPagination;

    public $search = '';
    // Every maximum is unset by default. These used to be real bounds applied
    // unconditionally — 1,000,000 / 10 beds / 10 baths / 10,000 sq ft — so an
    // untouched search silently dropped the largest and dearest homes on the
    // books, which are the ones an agency most wants seen.
    public $minPrice = null;
    public $maxPrice = null;
    public $minBedrooms = null;
    public $maxBedrooms = null;
    public $minBathrooms = null;
    public $maxBathrooms = null;
    public $minArea = null;
    public $maxArea = null;
    public $propertyType = '';
    public $selectedAmenities = [];
    public $yearBuilt = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $postalCode = '';
    public $latitude = null;
    public $longitude = null;
    public $radius = 10; // Default radius in km
    public $energyRating = '';
    public $minEnergyScore = 0;
    public $minWalkabilityScore = 0;
    public $minTransitScore = 0;
    public $minBikeScore = 0;
    public $featuredOnly = false;
    public $country = '';

    protected $listeners = ['applyAdvancedFilters', 'favoriteAdded' => '$refresh', 'favoriteRemoved' => '$refresh'];

    public function mount()
    {
        $this->resetPage();
    }
    
    // Each except-value must equal the property default above, or the filter
    // is either applied while it looks unset in the URL, or dropped from the
    // URL while it is actually narrowing the results.
    protected $queryString = [
        'search' => ['except' => ''],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'minBedrooms' => ['except' => null],
        'maxBedrooms' => ['except' => null],
        'minBathrooms' => ['except' => null],
        'maxBathrooms' => ['except' => null],
        'minArea' => ['except' => null],
        'maxArea' => ['except' => null],
        'propertyType' => ['except' => ''],
        'selectedAmenities' => ['except' => []],
        'energyRating' => ['except' => ''],
        'minEnergyScore' => ['except' => 0],
        'minWalkabilityScore' => ['except' => 0],
        'minTransitScore' => ['except' => 0],
        'minBikeScore' => ['except' => 0],
        'featuredOnly' => ['except' => false],
        'country' => ['except' => ''],
    ];

    public function applyAdvancedFilters($filters)
    {
        $this->search = $filters['search'];
        $this->minPrice = $filters['minPrice'];
        $this->maxPrice = $filters['maxPrice'];
        $this->minBedrooms = $filters['minBedrooms'];
        $this->maxBedrooms = $filters['maxBedrooms'];
        $this->minBathrooms = $filters['minBathrooms'];
        $this->maxBathrooms = $filters['maxBathrooms'];
        $this->minArea = $filters['minArea'];
        $this->maxArea = $filters['maxArea'];
        $this->propertyType = $filters['propertyType'];
        $this->selectedAmenities = $filters['selectedAmenities'];

        $this->resetPage();
    }

    public function getPropertiesProperty()
    {
        // Not cached. A LengthAwarePaginator carries the path and query state
        // of whichever request built it, and holding one for fifteen minutes
        // meant a newly published or re-priced listing was invisible for that
        // long. The query itself is a single indexed select.
        $properties = (function () {
            try {
                $query = Property::query()
                    ->search($this->search)
                    ->priceRange($this->minPrice, $this->maxPrice)
                    ->bedrooms($this->minBedrooms, $this->maxBedrooms)
                    ->bathrooms($this->minBathrooms, $this->maxBathrooms)
                    ->areaRange($this->minArea, $this->maxArea);

                if ($this->propertyType) {
                    $query->propertyType($this->propertyType);
                }

                if ($this->selectedAmenities) {
                    $query->hasAmenities($this->selectedAmenities);
                }

                if ($this->energyRating) {
                    $query->energyRating($this->energyRating);
                }

                if ($this->minEnergyScore > 0) {
                    $query->minEnergyScore($this->minEnergyScore);
                }

                if ($this->minWalkabilityScore > 0) {
                    $query->walkabilityScore($this->minWalkabilityScore);
                }

                if ($this->minTransitScore > 0) {
                    $query->transitScore($this->minTransitScore);
                }

                if ($this->minBikeScore > 0) {
                    $query->bikeScore($this->minBikeScore);
                }

                if ($this->featuredOnly) {
                    $query->featured();
                }

                if ($this->country) {
                    $query->country($this->country);
                }

                // 'media' is Spatie's relation, which the card reads for its
                // photograph; 'images' is a separate hasMany and does not
                // cover it.
                $query->with('features', 'images', 'media');

                $properties = $query->paginate(12);

                Log::info('Properties query executed', [
                    'total' => $properties->total(),
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                    'per_page' => $properties->perPage(),
                    'count' => $properties->count(),
                ]);

                return $properties;
            } catch (Exception $e) {
                session()->flash('error', 'An error occurred while fetching properties. Please try again.');
                if (app()->environment('local')) {
                    session()->flash('error_details', $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
                }
                return Property::paginate(0);
            }
        })();

        $this->dispatch('propertiesUpdated', $properties->items());
        return $properties;
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->dispatch('filtersChanged', $this->getFilters());
    }

    public function applyFilters($filters)
    {
        $this->fill($filters);
        $this->resetPage();
    }

    private function getFilters()
    {
        return [
            'search' => $this->search,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'minBedrooms' => $this->minBedrooms,
            'maxBedrooms' => $this->maxBedrooms,
            'minBathrooms' => $this->minBathrooms,
            'maxBathrooms' => $this->maxBathrooms,
            'minArea' => $this->minArea,
            'maxArea' => $this->maxArea,
            'propertyType' => $this->propertyType,
            'selectedAmenities' => $this->selectedAmenities,
            'energyRating' => $this->energyRating,
            'minEnergyScore' => $this->minEnergyScore,
            'minWalkabilityScore' => $this->minWalkabilityScore,
            'minTransitScore' => $this->minTransitScore,
            'minBikeScore' => $this->minBikeScore,
            'featuredOnly' => $this->featuredOnly,
            'country' => $this->country,
        ];
    }

    
    
    protected $propertyFeatureService;
    
    public function boot(PropertyFeatureService $propertyFeatureService)
    {
        $this->propertyFeatureService = $propertyFeatureService;
    }
    
    public function render()
    {
        return view('livewire.property-list', [
            'properties' => $this->getPropertiesProperty(),
            'amenities' => $this->propertyFeatureService->getFeatures(),
        ])->layout('layouts.app');
    }
    
    public function viewProperty($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        Activity::create([
            'user_id' => auth()->id(),
            'type' => 'property_view',
            'description' => "Viewed property: {$property->title}",
            'property_id' => $propertyId,
        ]);
    
        $this->dispatch('updateRecommendations');
    }

    public function toggleFavorite($propertyId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)
            ->where('property_id', $propertyId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            session()->flash('message', 'Property removed from wishlist');
            $this->dispatch('favoriteRemoved');
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'property_id' => $propertyId,
                'team_id' => $user->currentTeam?->id,
            ]);
            session()->flash('message', 'Property added to wishlist');
            $this->dispatch('favoriteAdded');
        }
    }

    public function isFavorited($propertyId)
    {
        if (!Auth::check()) {
            return false;
        }
        
        return Favorite::where('user_id', Auth::id())
            ->where('property_id', $propertyId)
            ->exists();
    }
}
