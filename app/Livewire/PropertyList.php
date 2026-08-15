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
    public $minPrice = '';
    public $maxPrice = '';
    public $minBedrooms = '';
    public $maxBedrooms = '';
    public $minBathrooms = '';
    public $maxBathrooms = '';
    public $minArea = '';
    public $maxArea = '';
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
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'minBedrooms' => ['except' => ''],
        'maxBedrooms' => ['except' => ''],
        'minBathrooms' => ['except' => ''],
        'maxBathrooms' => ['except' => ''],
        'minArea' => ['except' => ''],
        'maxArea' => ['except' => ''],
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

    /**
     * One place the filters are applied, so a count taken with a filter lifted
     * cannot drift from the list the visitor is looking at.
     */
    private function buildQuery(): \Illuminate\Database\Eloquent\Builder
    {
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

        return $query;
    }

    public function resultCount(): int
    {
        return $this->buildQuery()->count();
    }

    /**
     * The map shows what the filters returned, not everything on the books.
     * Showing all of it beside a narrowed list invites the reader to think the
     * pins and the cards are the same set.
     */
    public function mappableResults(): \Illuminate\Support\Collection
    {
        return $this->buildQuery()
            ->select(['id', 'title', 'price', 'currency', 'latitude', 'longitude'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->limit(500)
            ->get();
    }

    public function getPropertiesProperty()
    {
        // Not cached. A LengthAwarePaginator carries the path and query state
        // of whichever request built it, and holding one for fifteen minutes
        // meant a newly published or re-priced listing was invisible for that
        // long. The query itself is a single indexed select.
        try {
            // 'media' is Spatie's relation, which the card reads for its
            // photograph; 'images' is a separate hasMany and does not cover it.
            $properties = $this->buildQuery()
                ->with('features', 'images', 'media')
                ->paginate(12);
        } catch (Exception $e) {
            session()->flash('error', __('Something went wrong finding those homes. Try the search again.'));

            if (app()->environment('local')) {
                session()->flash('error_details', $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
            }

            // paginate(0) does not mean "no rows": Builder::paginate falls back
            // to the model's per-page, so this used to show fifteen arbitrary
            // unfiltered listings under an error banner as if they matched.
            $properties = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);
        }

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
        // wire:ignore keeps the map's DOM out of Livewire's hands, so a filter
        // change cannot re-render its pins — they are pushed instead.
        //
        // Dispatched from here rather than from the properties getter: the view
        // only reads that getter when there are results, so an empty result set
        // never fired it and left the pre-filter pins beside "No homes match
        // these filters" — the one moment the map is most obviously wrong.
        $mapped = rescue(fn () => $this->mappableResults(), collect(), report: true);

        // Shaped by the same component that renders the server pass, or the
        // live update formats prices differently from the first paint.
        $points = \App\View\Components\PropertyMap::points($mapped);

        $this->dispatch(
            'property-map-updated',
            properties: $points->all(),
            label: trans_choice(
                ':count property mapped|:count properties mapped',
                $mapped->count(),
                ['count' => $mapped->count()]
            ),
        );

        // The map points are passed rather than recomputed in the view: the
        // blade called mappableResults() again, so every debounced keystroke
        // paid for two 500-row selects.
        return view('livewire.property-list', ['mapPoints' => $points])
            ->layout('layouts.app');
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

    /**
     * What is currently narrowing the results, in the reader's words.
     *
     * A filter the visitor cannot see is a filter they cannot argue with — and
     * this component has hidden stock behind quiet defaults twice already.
     *
     * @return array<string, string>
     */
    public function appliedFilters(): array
    {
        $applied = [];

        if (filled($this->search)) {
            $applied['search'] = __('“:term”', ['term' => $this->search]);
        }

        if (filled($this->propertyType)) {
            $applied['propertyType'] = ucfirst($this->propertyType);
        }

        if (filled($this->minBedrooms)) {
            $applied['minBedrooms'] = __(':count+ bedrooms', ['count' => $this->minBedrooms]);
        }

        if (filled($this->minBathrooms)) {
            $applied['minBathrooms'] = __(':count+ bathrooms', ['count' => $this->minBathrooms]);
        }

        if (filled($this->minPrice)) {
            $applied['minPrice'] = __('Over :amount', ['amount' => $this->money($this->minPrice)]);
        }

        if (filled($this->maxPrice)) {
            $applied['maxPrice'] = __('Under :amount', ['amount' => $this->money($this->maxPrice)]);
        }

        if (filled($this->minArea)) {
            $applied['minArea'] = __('Over :area sq ft', ['area' => number_format((float) $this->minArea)]);
        }

        if (filled($this->maxArea)) {
            $applied['maxArea'] = __('Under :area sq ft', ['area' => number_format((float) $this->maxArea)]);
        }

        if (filled($this->energyRating)) {
            $applied['energyRating'] = __('EPC :band', ['band' => strtoupper($this->energyRating)]);
        }

        if (filled($this->maxBedrooms)) {
            $applied['maxBedrooms'] = __('Up to :count bedrooms', ['count' => $this->maxBedrooms]);
        }

        if (filled($this->maxBathrooms)) {
            $applied['maxBathrooms'] = __('Up to :count bathrooms', ['count' => $this->maxBathrooms]);
        }

        if (filled($this->selectedAmenities)) {
            $applied['selectedAmenities'] = __(':count features', ['count' => count($this->selectedAmenities)]);
        }

        if ($this->minEnergyScore > 0) {
            $applied['minEnergyScore'] = __('Energy :score+', ['score' => $this->minEnergyScore]);
        }

        if ($this->minWalkabilityScore > 0) {
            $applied['minWalkabilityScore'] = __('Walk :score+', ['score' => $this->minWalkabilityScore]);
        }

        if ($this->minTransitScore > 0) {
            $applied['minTransitScore'] = __('Transit :score+', ['score' => $this->minTransitScore]);
        }

        if ($this->minBikeScore > 0) {
            $applied['minBikeScore'] = __('Bike :score+', ['score' => $this->minBikeScore]);
        }

        if (filled($this->country)) {
            $applied['country'] = strtoupper($this->country);
        }

        if ($this->featuredOnly) {
            $applied['featuredOnly'] = __('Featured only');
        }

        return $applied;
    }

    /** Plain words for the clear control, so it reads as an action. */
    public function filterLabels(): array
    {
        return [
            'search' => __('the search term'),
            'propertyType' => __('the property type'),
            'minBedrooms' => __('the bedroom minimum'),
            'minBathrooms' => __('the bathroom minimum'),
            'minPrice' => __('the minimum price'),
            'maxPrice' => __('the maximum price'),
            'minArea' => __('the minimum area'),
            'maxArea' => __('the maximum area'),
            'energyRating' => __('the energy rating'),
            'maxBedrooms' => __('the bedroom maximum'),
            'maxBathrooms' => __('the bathroom maximum'),
            'selectedAmenities' => __('the feature filters'),
            'minEnergyScore' => __('the energy score minimum'),
            'minWalkabilityScore' => __('the walk score minimum'),
            'minTransitScore' => __('the transit score minimum'),
            'minBikeScore' => __('the bike score minimum'),
            'country' => __('the country'),
            'featuredOnly' => __('the featured filter'),
        ];
    }

    /**
     * Restores the filter's own default rather than a blanket null. The
     * queryString except-values must match those defaults, or a cleared filter
     * lingers in the URL as ?search= and the "cleared" page is not shareable.
     */
    private function defaultFor(string $filter): mixed
    {
        return match ($filter) {
            'featuredOnly' => false,
            'search', 'propertyType', 'energyRating', 'country',
            'minPrice', 'maxPrice', 'minBedrooms', 'maxBedrooms',
            'minBathrooms', 'maxBathrooms', 'minArea', 'maxArea' => '',
            'selectedAmenities' => [],
            'minEnergyScore', 'minWalkabilityScore', 'minTransitScore', 'minBikeScore' => 0,
            default => null,
        };
    }

    public function clearFilter(string $filter): void
    {
        if (! array_key_exists($filter, $this->appliedFilters())) {
            return;
        }

        $this->{$filter} = $this->defaultFor($filter);
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        foreach (array_keys($this->appliedFilters()) as $filter) {
            $this->{$filter} = $this->defaultFor($filter);
        }

        $this->resetPage();
    }

    /**
     * How many homes would come back if this one filter were lifted. An empty
     * page can then name the move and its result rather than just apologising.
     */
    public function countWithout(string $filter): int
    {
        // Reachable from the browser as a Livewire action, so it takes only a
        // name that is currently applied. Anything else would touch an
        // undeclared property, land in the dehydrated snapshot, and throw on a
        // protected one.
        if (! array_key_exists($filter, $this->appliedFilters())) {
            return 0;
        }

        $was = $this->{$filter};
        $this->{$filter} = $this->defaultFor($filter);

        try {
            return $this->buildQuery()->count();
        } finally {
            $this->{$filter} = $was;
        }
    }

    /**
     * The single filter whose removal returns the most homes — the one worth
     * suggesting when nothing matches.
     */
    /**
     * The single filter whose removal returns the most homes, with that count —
     * returned together so the empty state does not run the counts twice.
     *
     * @return array{filter: string, count: int}|null
     */
    public function mostRestrictiveFilter(): ?array
    {
        $counts = [];

        // ponytail: one COUNT per filter probed, capped. Eighteen filters are
        // bound to a live search box, so an unbounded loop here is a burst of
        // queries per debounced keystroke. Raise the cap only if a real search
        // with more than five filters set turns out to need it.
        foreach (array_slice(array_keys($this->appliedFilters()), 0, 5) as $filter) {
            $counts[$filter] = $this->countWithout($filter);
        }

        $counts = array_filter($counts);

        if ($counts === []) {
            return null;
        }

        arsort($counts);

        return ['filter' => array_key_first($counts), 'count' => reset($counts)];
    }

    private function money($amount): string
    {
        return (app(\App\Settings\GeneralSettings::class)->currencySymbol())
            .number_format((float) $amount);
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
