<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
        ];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, Membership::class)
            ->withPivot(['role', 'permissions', 'branch_id', 'department_id', 'job_title', 'phone', 'bio', 'is_public'])
            ->withTimestamps()
            ->as('membership');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function onTheMarketSettings(): HasMany
    {
        return $this->hasMany(OnTheMarketSettings::class);
    }

    public function rightMoveSettings(): HasMany
    {
        return $this->hasMany(RightMoveSettings::class);
    }

    public function zooplaSettings(): HasMany
    {
        return $this->hasMany(ZooplaSettings::class);
    }

    public function emailCampaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }

    public function energyConsumptions(): HasMany
    {
        return $this->hasMany(EnergyConsumption::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function contractors(): HasMany
    {
        return $this->hasMany(Contractor::class);
    }

    public function digitalSignatures(): HasMany
    {
        return $this->hasMany(DigitalSignature::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function documentTemplates(): HasMany
    {
        return $this->hasMany(DocumentTemplate::class);
    }

    public function keyLocations(): HasMany
    {
        return $this->hasMany(KeyLocation::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function propertyFeatures(): HasMany
    {
        return $this->hasMany(PropertyFeature::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function siteSettings(): HasMany
    {
        return $this->hasMany(SiteSettings::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function agencyTasks(): HasMany
    {
        return $this->hasMany(AgencyTask::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function organisationProfile(): HasOne
    {
        return $this->hasOne(OrganisationProfile::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function memberships()
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
