<?php

namespace App\Models;

use App\Services\WalkScoreService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
/**
 * Represents a property in the real estate application.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $location
 * @property float $price
 * @property int $bedrooms
 * @property int $bathrooms
 * @property float $area_sqft
 * @property int $year_built
 * @property string $property_type
 * @property string $status
 * @property DateTime $list_date
 * @property DateTime|null $sold_date
 * @property int $user_id
 * @property int $agent_id
 * @property string|null $virtual_tour_url
 * @property string|null $model_3d_url
 * @property bool $is_featured
 * @property string|null $rightmove_id
 * @property string|null $zoopla_id
 * @property string|null $onthemarket_id
 * @property DateTime|null $last_synced_at
 * @property DateTime|null $deleted_at
 * @property-read Collection|Appointment[] $appointments
 * @property-read Collection|Transaction[] $transactions
 * @property-read Collection|Review[] $reviews
 * @property-read Collection|PropertyFeature[] $features
 * @property-read Collection|Image[] $images
 * @property-read Collection|Booking[] $bookings
 */
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Property extends Model implements HasMedia
{
    use Concerns\HasDisclosureFacts, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'internal_notes',
        'property_template_id',
        'location',
        'structured_address',
        'latitude',
        'longitude',
        'walkability_score',
        'walkability_description',
        'transit_score',
        'transit_description',
        'bike_score',
        'bike_description',
        'walkability_updated_at',
        'price',
        'currency',
        'bedrooms',
        'bathrooms',
        'reception_rooms',
        'parking',
        'gardens',
        'area_sqft',
        'year_built',
        'property_type',
        'status',
        'list_date',
        'sold_date',
        'user_id',
        'team_id',
        'branch_id',
        'agent_id',
        'virtual_tour_url',
        'virtual_tour_provider',
        'live_tour_available',
        'model_3d_url',
        'is_featured',
        'rightmove_id',
        'zoopla_id',
        'onthemarket_id',
        'last_synced_at',
        'neighborhood_id',
        'property_category_id',
        'postal_code',
        'country',
        'energy_rating',
        'epc',
        'energy_score',
        'energy_rating_date',
        'insurance_policy_id',
        'insurance_coverage_amount',
        'insurance_premium',
        'insurance_expiry_date',
        'floor_plan_data',
        'floor_plan_image',
        'model_3d_url',
        'ar_tour_enabled',
        'ar_tour_settings',
        'ar_placement_guide',
        'ar_model_scale',
        'holographic_tour_url',
        'holographic_provider',
        'holographic_metadata',
        'holographic_enabled',
        'jupix_id',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'list_date' => 'date',
        'sold_date' => 'date',
        'is_featured' => 'boolean',
        'live_tour_available' => 'boolean',
        'insurance_expiry_date' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'walkability_updated_at' => 'datetime',
        'floor_plan_data' => 'array',
        'ar_tour_enabled' => 'boolean',
        'ar_tour_settings' => 'array',
        'ar_model_scale' => 'float',
        'holographic_metadata' => 'array',
        'holographic_enabled' => 'boolean',
        'structured_address' => 'array',
        'parking' => 'array',
        'gardens' => 'array',
        'epc' => 'array',
    ];

    public function auctions()
    {
        return $this->hasMany(Auction::class);
    }

    public function currentAuction()
    {
        return $this->auctions()->where('status', 'active')->first();
    }

    public function isInAuction()
    {
        return $this->currentAuction() !== null;
    }

    public function insurancePolicy()
    {
        return $this->belongsTo(InsurancePolicy::class);
    }

    public function hasActiveInsurance()
    {
        return $this->insurance_policy_id && $this->insurance_expiry_date > now();
    }

    public function template()
    {
        return $this->belongsTo(PropertyTemplate::class, 'property_template_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * British building stock predates the MySQL YEAR type by eight centuries.
     * The floor is the Conquest — old enough for anything habitable, late
     * enough to catch a typo — and the ceiling allows a new build sold before
     * it is finished without accepting a year that cannot be a build year.
     */
    public const EARLIEST_YEAR_BUILT = 1066;

    public static function latestYearBuilt(): int
    {
        return (int) now()->year + 2;
    }

    /**
     * One rule, so the API, the staff panel and the public form cannot drift
     * apart on what counts as a build year. They had: 1000, 1800 and 1800 with
     * three different ceilings.
     *
     * @return list<string>
     */
    public static function yearBuiltRules(): array
    {
        return ['integer', 'min:'.self::EARLIEST_YEAR_BUILT, 'max:'.self::latestYearBuilt()];
    }

    public static function yearBuiltMessage(): string
    {
        return __('Enter a build year between :from and :to.', [
            'from' => self::EARLIEST_YEAR_BUILT,
            'to' => self::latestYearBuilt(),
        ]);
    }

    public function setYearBuiltAttribute($value)
    {
        $this->attributes['year_built'] = is_string($value) ? substr($value, 0, 4) : $value;
    }

    // Relationships
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'property_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'property_id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function features()
    {
        return $this->hasMany(PropertyFeature::class, 'property_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function viewCount()
    {
        return $this->activities()->where('type', 'property_view')->count();
    }

    public function similarProperties($limit = 3)
    {
        return Property::where('id', '!=', $this->id)
            ->where('property_type', $this->property_type)
            ->whereBetween('price', [$this->price * 0.8, $this->price * 1.2])
            ->whereBetween('bedrooms', [$this->bedrooms - 1, $this->bedrooms + 1])
            ->whereBetween('bathrooms', [$this->bathrooms - 1, $this->bathrooms + 1])
            ->limit($limit)
            ->get();
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function communications()
    {
        return $this->morphMany(Communication::class, 'communicable');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function category()
    {
        return $this->belongsTo(PropertyCategory::class, 'property_category_id');
    }

    public function valuations()
    {
        return $this->hasMany(PropertyValuation::class);
    }

    public function chainLinks()
    {
        return $this->hasMany(ChainLink::class);
    }

    public function complianceItems()
    {
        return $this->hasMany(ComplianceItem::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function vendorQuotes()
    {
        return $this->hasMany(VendorQuote::class);
    }

    public function propertyMatches()
    {
        return $this->hasMany(PropertyMatch::class);
    }

    public function marketAppraisals()
    {
        return $this->hasMany(MarketAppraisal::class);
    }

    public function histories()
    {
        return $this->hasMany(PropertyHistory::class)->orderBy('event_date', 'desc');
    }

    public function vrDesigns()
    {
        return $this->hasMany(VRDesign::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'property_id', 'user_id')
            ->withTimestamps();
    }

    public function communityEvents()
    {
        return $this->hasMany(CommunityEvent::class);
    }

    public function getNearbyCommunityEvents($radius = 10)
    {
        if (! $this->latitude || ! $this->longitude) {
            return collect([]);
        }

        // Use the nearby scope which includes distance calculation
        return CommunityEvent::public()
            ->upcoming()
            ->nearby($this->latitude, $this->longitude, $radius)
            ->get()
            ->map(function ($event) {
                // Distance is already calculated by the nearby scope
                // but we need to make it accessible as a property
                $event->distance_from_property = $event->distance ?? 0;

                return $event;
            });
    }

    public function getLatestValuation($type = 'market')
    {
        return $this->valuations()
            ->where('valuation_type', $type)
            ->where('status', 'active')
            ->latest('valuation_date')
            ->first();
    }

    public function getComplianceStatus()
    {
        $total = $this->complianceItems()->count();
        $completed = $this->complianceItems()->where('status', 'completed')->count();
        $overdue = $this->complianceItems()->where('required_by_date', '<', now())
            ->where('status', '!=', 'completed')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'overdue' => $overdue,
            'completion_rate' => $total > 0 ? ($completed / $total) * 100 : 0,
        ];
    }

    public function hasActiveWorkOrders()
    {
        return $this->workOrders()
            ->whereIn('status', ['pending', 'approved', 'scheduled', 'in_progress'])
            ->exists();
    }

    public function getLatestMarketAppraisal()
    {
        return $this->marketAppraisals()
            ->where('valid_until', '>=', now())
            ->latest('appraisal_date')
            ->first();
    }

    /**
     * Update walkability scores for this property
     *
     * @return void
     */
    public function updateWalkabilityScores()
    {
        if (! $this->latitude || ! $this->longitude) {
            return;
        }

        $walkScoreService = app(WalkScoreService::class);
        $address = $this->location.', '.$this->postal_code;

        $scores = $walkScoreService->getWalkScore($address, $this->latitude, $this->longitude);

        if ($scores) {
            $this->update([
                'walkability_score' => $scores['walk_score'],
                'walkability_description' => $scores['walk_description'],
                'transit_score' => $scores['transit_score'],
                'transit_description' => $scores['transit_description'],
                'bike_score' => $scores['bike_score'],
                'bike_description' => $scores['bike_description'],
                'walkability_updated_at' => now(),
            ]);
        }
    }

    /**
     * Check if walkability scores need updating (older than 30 days)
     *
     * @return bool
     */
    public function needsWalkabilityUpdate()
    {
        if (! $this->walkability_updated_at) {
            return true;
        }

        return $this->walkability_updated_at->lt(now()->subDays(30));
    }

    // Scopes
    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhere('location', 'like', '%'.$search.'%')
                ->orWhere('postal_code', 'like', '%'.$search.'%');
        });
    }

    public function scopePostalCode(Builder $query, $postalCode): Builder
    {
        return $query->where('postal_code', 'like', $postalCode.'%');
    }

    public function scopeNearby(Builder $query, $latitude, $longitude, $radius): Builder
    {
        return $query->selectRaw('*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    public function scopeCategory(Builder $query, $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopePriceRange(Builder $query, $min, $max): Builder
    {
        // A null bound means "no bound". whereBetween with a null max matched
        // nothing, and a max that merely defaulted low hid dear properties
        // without ever saying so.
        return $query
            ->when($min !== null && $min !== '', fn (Builder $q) => $q->where('price', '>=', $min))
            ->when($max !== null && $max !== '', fn (Builder $q) => $q->where('price', '<=', $max));
    }

    public function scopeBedrooms(Builder $query, $min, $max): Builder
    {
        // A null bound means "no bound". A default maximum applied
        // unconditionally hid the largest homes on the books.
        return $query
            ->when($min !== null && $min !== '', fn (Builder $q) => $q->where('bedrooms', '>=', $min))
            ->when($max !== null && $max !== '', fn (Builder $q) => $q->where('bedrooms', '<=', $max));
    }

    public function scopeBathrooms(Builder $query, $min, $max): Builder
    {
        // A null bound means "no bound". A default maximum applied
        // unconditionally hid the largest homes on the books.
        return $query
            ->when($min !== null && $min !== '', fn (Builder $q) => $q->where('bathrooms', '>=', $min))
            ->when($max !== null && $max !== '', fn (Builder $q) => $q->where('bathrooms', '<=', $max));
    }

    public function scopeAreaRange(Builder $query, $min, $max): Builder
    {
        return $query
            ->when($min !== null && $min !== '', fn (Builder $q) => $q->where('area_sqft', '>=', $min))
            ->when($max !== null && $max !== '', fn (Builder $q) => $q->where('area_sqft', '<=', $max));
    }

    /**
     * Matched without regard to case. The staff panel stores 'house' while the
     * factory and seeder store 'House', so an exact match returned nothing for
     * half the stock on any case-sensitive collation.
     */
    public function scopePropertyType(Builder $query, $type): Builder
    {
        // Matched without regard to case, but with whereIn rather than
        // LOWER(column): a function on the column makes any index unusable,
        // and this scope runs on the list, the count, the map and once per
        // filter in the empty-state calculation.
        $type = trim((string) $type);

        return $query->whereIn('property_type', array_unique([
            $type, strtolower($type), strtoupper($type), ucfirst(strtolower($type)),
        ]));
    }

    public function scopeHasAmenities(Builder $query, array $amenities): Builder
    {
        return $query->whereHas('features', function ($query) use ($amenities) {
            $query->whereIn('feature_name', $amenities);
        }, '=', count($amenities));
    }

    public function scopeNeedsSyncing(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->whereNull('last_synced_at')
                ->orWhere('updated_at', '>', 'last_synced_at');
        });
    }

    public function scopeEnergyRating(Builder $query, $rating): Builder
    {
        return $query->where('energy_rating', $rating);
    }

    public function scopeMinEnergyScore(Builder $query, $minScore): Builder
    {
        return $query->where('energy_score', '>=', $minScore);
    }

    public function scopeWalkabilityScore(Builder $query, $minScore): Builder
    {
        return $query->where('walkability_score', '>=', $minScore);
    }

    public function scopeTransitScore(Builder $query, $minScore): Builder
    {
        return $query->where('transit_score', '>=', $minScore);
    }

    public function scopeBikeScore(Builder $query, $minScore): Builder
    {
        return $query->where('bike_score', '>=', $minScore);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeCountry(Builder $query, $country): Builder
    {
        return $query->where('country', $country);
    }

    /**
     * The hours a viewing can be booked into. Shared, because the date picker
     * and the time picker have to agree on what a full day is — they did not,
     * and the date picker won.
     */
    public const VIEWING_SLOTS = [
        '09:00', '10:00', '11:00', '12:00', '13:00',
        '14:00', '15:00', '16:00', '17:00',
    ];

    /**
     * Days this property has at least one viewing slot left on.
     *
     * Two bugs lived here. The comparison was `in_array('Y-m-d', $plucked)`,
     * but Booking casts `date`, so pluck() returns Carbon objects whose string
     * form is 'Y-m-d H:i:s' — nothing ever matched and every day for three
     * months was reported free. Fixing that exposed the second: a day was
     * dropped wholesale on the first booking, and team-wide at that, so one
     * viewing at 09:00 closed every hour on every home the agency had.
     */
    public function availableViewingDates(): array
    {
        $from = now()->startOfDay();
        $to = now()->addMonths(3)->endOfDay();
        $taken = $this->bookedSlots($from, $to);
        $slots = count(self::VIEWING_SLOTS);
        $available = [];

        for ($date = $from->copy(); $date <= $to; $date->addDay()) {
            $day = $date->format('Y-m-d');
            $left = count(self::slotsFrom($day)) - count($taken[$day] ?? []);

            if ($left > 0) {
                $available[] = $day;
            }
        }

        return $available;
    }

    public function availableViewingSlots(string $date): array
    {
        $day = Carbon::parse($date);

        return array_values(array_diff(
            self::slotsFrom($date),
            $this->bookedSlots($day->copy()->startOfDay(), $day->copy()->endOfDay())[$day->format('Y-m-d')] ?? []
        ));
    }

    /**
     * An hour that has already passed is not a slot. Nothing compared against
     * the clock, and the date rule is only after_or_equal:today, so a visitor
     * arriving at 18:00 was offered 09:00 that morning — and the booking, the
     * calendar links and the confirmation email were all created for it.
     *
     * @return list<string>
     */
    private static function slotsFrom(string $date): array
    {
        if (! Carbon::parse($date)->isToday()) {
            return self::VIEWING_SLOTS;
        }

        return array_values(array_filter(
            self::VIEWING_SLOTS,
            fn (string $slot) => Carbon::parse($slot)->isFuture()
        ));
    }

    /**
     * Booked slots by day. Bounded by the window asked for — the whole booking
     * history was being read to answer a question about the next three months
     * — and cancelled viewings give their slot back.
     *
     * @return array<string, list<string>>
     */
    private function bookedSlots(Carbon $from, Carbon $to): array
    {
        return $this->bookings()
            ->whereBetween('date', [$from, $to])
            ->whereNot('status', 'cancelled')
            ->get(['date', 'time'])
            ->groupBy(fn (Booking $booking) => Carbon::parse($booking->date)->format('Y-m-d'))
            ->map(fn ($bookings) => $bookings
                ->map(fn (Booking $booking) => Carbon::parse($booking->time)->format('H:i'))
                ->unique()
                ->values()
                ->all())
            ->all();
    }

    /**
     * Check if property has a virtual tour
     *
     * @return bool
     */
    public function hasVirtualTour()
    {
        return $this->generateEmbedCode($this->virtual_tour_url) !== null;
    }

    /**
     * Get the embedded virtual tour HTML
     *
     * @return string|null
     */
    public function getVirtualTourEmbed()
    {
        if ($this->virtual_tour_url) {
            return $this->generateEmbedCode($this->virtual_tour_url);
        }

        return null;
    }

    /**
     * Generate embed code from URL for common virtual tour providers
     *
     * @param  string  $url
     * @return string|null
     */
    protected function generateEmbedCode($url)
    {
        // Validate URL
        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowedHosts = ['matterport.com', 'kuula.co', '3dvista.com', '3dv.st', 'seekbeak.com'];
        $isAllowed = collect($allowedHosts)->contains(
            fn (string $allowed) => $host === $allowed || str_ends_with($host, '.'.$allowed)
        );

        if (! $isAllowed || strtolower((string) parse_url($url, PHP_URL_SCHEME)) !== 'https') {
            return null;
        }

        $escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        return '<iframe width="100%" height="480" src="'.$escapedUrl.'" frameborder="0" sandbox="allow-scripts allow-same-origin allow-presentation" referrerpolicy="no-referrer" allow="xr-spatial-tracking" allowfullscreen></iframe>';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->withResponsiveImages();

        $this->addMediaCollection('videos')
            ->acceptsMimeTypes(['video/mp4', 'video/quicktime', 'application/x-empty'])
            ->singleFile();

        $this->addMediaCollection('3d_models')
            ->acceptsMimeTypes(['model/gltf-binary', 'model/gltf+json', 'application/octet-stream', 'application/x-empty'])
            ->singleFile();
    }

    /**
     * Check if property has holographic tour available
     */
    public function hasHolographicTour(): bool
    {
        return $this->holographic_enabled && ! empty($this->holographic_tour_url);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($property) {
            Cache::flush();
        });

        static::updated(function ($property) {
            Cache::flush();
        });

        static::deleted(function ($property) {
            Cache::flush();
        });
    }

    public function isHmo(): bool
    {
        return ($this->property_type ?? '') === 'HMO';
    }
}
