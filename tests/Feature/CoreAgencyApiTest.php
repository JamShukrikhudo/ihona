<?php

namespace Tests\Feature;

use App\Models\AgencyTask;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Buyer;
use App\Models\Company;
use App\Models\ComplianceItem;
use App\Models\Contact;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\EmailCampaign;
use App\Models\Neighborhood;
use App\Models\Offer;
use App\Models\OrganisationProfile;
use App\Models\PortalIntegration;
use App\Models\PortalListing;
use App\Models\PortalSyncRun;
use App\Models\Property;
use App\Models\PropertyFeature;
use App\Models\ServiceIntegration;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\NewPropertyMatches;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CoreAgencyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_api_requires_authentication(): void
    {
        $this->getJson('/api/v1/contacts')->assertUnauthorized();
    }

    public function test_contact_crud_is_scoped_to_the_current_team(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $otherTeam = Team::factory()->create();
        $otherContact = Contact::create([
            'team_id' => $otherTeam->id,
            'type' => 'buyer',
            'first_name' => 'Hidden',
        ]);

        $response = $this->postJson('/api/v1/contacts', [
            'type' => 'buyer',
            'first_name' => 'Amina',
            'last_name' => 'Khan',
            'emails' => ['amina@example.test'],
            'tags' => ['first-time-buyer'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.first_name', 'Amina');

        $contactId = $response->json('data.id');

        $this->getJson('/api/v1/contacts?search=Amina')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $contactId);

        $this->patchJson("/api/v1/contacts/$contactId", ['status' => 'inactive'])
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->getJson("/api/v1/contacts/{$otherContact->id}")->assertNotFound();
        $this->deleteJson("/api/v1/contacts/$contactId")->assertNoContent();
        $this->assertSoftDeleted('contacts', ['id' => $contactId]);
    }

    public function test_contacts_support_directional_team_scoped_relationships(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $landlord = Contact::create([
            'team_id' => $team->id,
            'type' => 'landlord',
            'first_name' => 'Rowan',
            'last_name' => 'Owner',
        ]);
        $tenant = Contact::create([
            'team_id' => $team->id,
            'type' => 'tenant',
            'first_name' => 'Alex',
            'last_name' => 'Renter',
        ]);

        $response = $this->postJson("/api/v1/contacts/{$landlord->id}/relationships", [
            'related_contact_id' => $tenant->id,
            'relationship' => 'tenant',
            'inverse_relationship' => 'landlord',
            'notes' => 'Current tenancy at 12 Garden Street.',
        ])->assertCreated()
            ->assertJsonPath('data.contact_id', $landlord->id)
            ->assertJsonPath('data.related_contact.id', $tenant->id)
            ->assertJsonPath('data.relationship', 'tenant')
            ->assertJsonPath('data.created_by.id', $user->id);
        $relationshipId = $response->json('data.id');

        $this->getJson("/api/v1/contacts/{$tenant->id}/relationships")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.related_contact.id', $landlord->id)
            ->assertJsonPath('data.0.relationship', 'landlord')
            ->assertJsonPath('data.0.inverse_relationship', 'tenant');

        $this->patchJson("/api/v1/contacts/{$tenant->id}/relationships/$relationshipId", [
            'relationship' => 'former_landlord',
            'inverse_relationship' => 'former_tenant',
            'notes' => 'Tenancy has ended.',
        ])->assertOk()
            ->assertJsonPath('data.relationship', 'former_landlord')
            ->assertJsonPath('data.inverse_relationship', 'former_tenant');

        $this->postJson("/api/v1/contacts/{$tenant->id}/relationships", [
            'related_contact_id' => $landlord->id,
            'relationship' => 'former_landlord',
            'inverse_relationship' => 'former_tenant',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('related_contact_id');

        $otherContact = Contact::create([
            'team_id' => Team::factory()->create()->id,
            'type' => 'solicitor',
            'first_name' => 'Hidden',
        ]);
        $this->postJson("/api/v1/contacts/{$landlord->id}/relationships", [
            'related_contact_id' => $otherContact->id,
            'relationship' => 'solicitor',
            'inverse_relationship' => 'client',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('related_contact_id');

        $this->deleteJson("/api/v1/contacts/{$landlord->id}/relationships/$relationshipId")
            ->assertNoContent();
        $this->assertDatabaseMissing('contact_relationships', ['id' => $relationshipId]);
    }

    public function test_offer_relations_must_belong_to_the_current_team(): void
    {
        [, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create();
        $contact = Contact::create([
            'team_id' => $team->id,
            'type' => 'buyer',
            'first_name' => 'Sam',
        ]);

        $this->postJson('/api/v1/offers', [
            'property_id' => $property->id,
            'contact_id' => $contact->id,
            'amount' => 325000,
            'currency' => 'GBP',
            'offered_at' => now()->toIso8601String(),
        ])->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.amount', '325000.00');

        $otherTeam = Team::factory()->create();
        $otherProperty = Property::factory()->for($otherTeam)->create();

        $this->postJson('/api/v1/offers', [
            'property_id' => $otherProperty->id,
            'contact_id' => $contact->id,
            'amount' => 100000,
            'offered_at' => now()->toIso8601String(),
        ])->assertUnprocessable()->assertJsonValidationErrors('property_id');
    }

    public function test_offer_negotiations_create_an_immutable_team_scoped_timeline(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create();
        $contact = Contact::create([
            'team_id' => $team->id,
            'type' => 'buyer',
            'first_name' => 'Casey',
        ]);

        $response = $this->postJson('/api/v1/offers', [
            'property_id' => $property->id,
            'contact_id' => $contact->id,
            'amount' => 400000,
            'currency' => 'GBP',
            'conditions' => 'Subject to survey',
            'offered_at' => now()->toIso8601String(),
            'negotiation_note' => 'Opening offer received by phone.',
        ])->assertCreated();
        $offerId = $response->json('data.id');

        $this->patchJson("/api/v1/offers/$offerId", [
            'amount' => 415000,
            'conditions' => 'Subject to survey and finance',
            'negotiation_note' => 'Buyer increased after vendor response.',
        ])->assertOk()
            ->assertJsonPath('data.amount', '415000.00');

        $this->patchJson("/api/v1/offers/$offerId", [
            'status' => 'accepted',
            'responded_at' => now()->toIso8601String(),
            'negotiation_note' => 'Vendor accepted the revised offer.',
        ])->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        $this->getJson("/api/v1/offers/$offerId/timeline")
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.event_type', 'created')
            ->assertJsonPath('data.0.actor_id', $user->id)
            ->assertJsonPath('data.1.previous_amount', '400000.00')
            ->assertJsonPath('data.1.amount', '415000.00')
            ->assertJsonPath('data.2.event_type', 'status_changed')
            ->assertJsonPath('data.2.previous_status', 'pending')
            ->assertJsonPath('data.2.status', 'accepted');

        $otherTeam = Team::factory()->create();
        $otherProperty = Property::factory()->for($otherTeam)->create();
        $otherContact = Contact::create([
            'team_id' => $otherTeam->id,
            'type' => 'buyer',
            'first_name' => 'Hidden',
        ]);
        $otherOffer = Offer::create([
            'team_id' => $otherTeam->id,
            'property_id' => $otherProperty->id,
            'contact_id' => $otherContact->id,
            'amount' => 100000,
            'offered_at' => now(),
        ]);

        $this->getJson("/api/v1/offers/{$otherOffer->id}/timeline")
            ->assertNotFound();
    }

    public function test_task_creator_and_team_are_derived_from_authentication(): void
    {
        [$user, $team] = $this->actingAsTeamMember();

        $this->postJson('/api/v1/tasks', [
            'title' => 'Issue memorandum of sale',
            'priority' => 'high',
            'checklist' => [
                ['label' => 'Verify solicitor', 'completed' => false],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.created_by', $user->id);
    }

    public function test_current_team_must_be_an_owned_or_joined_team(): void
    {
        $user = User::factory()->create();
        $unrelatedTeam = Team::factory()->create();
        $user->forceFill(['current_team_id' => $unrelatedTeam->id])->save();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/contacts')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('team');
    }

    public function test_setup_applies_primary_country_defaults_to_the_organisation(): void
    {
        [, $team] = $this->actingAsTeamMember();

        $this->getJson('/api/v1/setup/status')
            ->assertOk()
            ->assertJsonPath('data.complete', false);

        $this->putJson('/api/v1/setup', [
            'agency_name' => 'North Star Estates',
            'email' => 'hello@north-star.test',
            'address' => [
                'line_1' => '10 Market Street',
                'city' => 'Manchester',
                'postal_code' => 'M1 1AA',
            ],
            'operating_countries' => ['GB', 'IE'],
            'primary_country' => 'GB',
            'branding' => ['primary_color' => '#1f4b99'],
        ])->assertCreated()
            ->assertJsonPath('data.currency', 'GBP')
            ->assertJsonPath('data.timezone', 'Europe/London')
            ->assertJsonPath('data.measurement_system', 'metric')
            ->assertJsonPath('data.address.postal_code', 'M1 1AA')
            ->assertJsonPath('data.regional_settings.phone_format', '+44 #### ######')
            ->assertJsonPath('data.regional_settings.tax_defaults.vat_rate', 20)
            ->assertJsonPath('data.regional_settings.legal_terms.seller', 'vendor')
            ->assertJsonPath('data.regional_settings.portals.0', 'rightmove');

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'North Star Estates',
        ]);

        $this->getJson('/api/v1/setup/status')
            ->assertOk()
            ->assertJsonPath('data.complete', true);
    }

    public function test_setup_rejects_a_primary_country_outside_operating_countries(): void
    {
        $this->actingAsTeamMember();

        $this->putJson('/api/v1/setup', [
            'agency_name' => 'Example Estates',
            'address' => [
                'line_1' => '1 Main Street',
                'city' => 'Dublin',
                'postal_code' => 'D01 F5P2',
            ],
            'operating_countries' => ['IE'],
            'primary_country' => 'GB',
        ])->assertUnprocessable()->assertJsonValidationErrors('primary_country');
    }

    public function test_setup_validates_the_primary_country_postal_format(): void
    {
        $this->actingAsTeamMember();

        $this->putJson('/api/v1/setup', [
            'agency_name' => 'Atlantic Realty',
            'address' => [
                'line_1' => '1 Main Street',
                'city' => 'Boston',
                'postal_code' => 'SW1A 1AA',
            ],
            'operating_countries' => ['US'],
            'primary_country' => 'US',
        ])->assertUnprocessable()->assertJsonValidationErrors('address.postal_code');

        $this->getJson('/api/v1/setup/options')
            ->assertOk()
            ->assertJsonPath('data.US.address_format.3', 'state')
            ->assertJsonPath('data.US.tax_defaults.property_tax', 'local')
            ->assertJsonPath('data.US.legal_terms.lawyer', 'attorney');
    }

    public function test_regular_team_member_cannot_change_organisation_setup(): void
    {
        $team = Team::factory()->create();
        $member = User::factory()->create();
        $team->users()->attach($member, ['role' => 'member']);
        $member->forceFill(['current_team_id' => $team->id])->save();
        Sanctum::actingAs($member);

        $this->putJson('/api/v1/setup', [
            'agency_name' => 'Unauthorised Rename',
            'address' => [
                'line_1' => '1 Main Street',
                'city' => 'London',
                'postal_code' => 'SW1A 1AA',
            ],
            'operating_countries' => ['GB'],
            'primary_country' => 'GB',
        ])->assertForbidden();
    }

    public function test_global_search_returns_grouped_team_scoped_results(): void
    {
        [, $team] = $this->actingAsTeamMember();
        Property::factory()->for($team)->create([
            'title' => 'Orchard House',
            'location' => 'York',
        ]);
        Contact::create([
            'team_id' => $team->id,
            'type' => 'vendor',
            'first_name' => 'Orchard',
            'last_name' => 'Owner',
        ]);
        Company::create([
            'team_id' => $team->id,
            'name' => 'Orchard Developments',
            'type' => 'developer',
        ]);
        Appointment::create([
            'team_id' => $team->id,
            'name' => 'Orchard House viewing',
            'contact' => 'Amina Viewer',
            'appointment_date' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $otherTeam = Team::factory()->create();
        Company::create([
            'team_id' => $otherTeam->id,
            'name' => 'Orchard Hidden Company',
        ]);

        $this->getJson('/api/v1/search?q=Orchard&types[]=properties&types[]=contacts&types[]=companies&types[]=viewings')
            ->assertOk()
            ->assertJsonPath('meta.total', 4)
            ->assertJsonCount(1, 'data.properties')
            ->assertJsonCount(1, 'data.contacts')
            ->assertJsonCount(1, 'data.companies')
            ->assertJsonCount(1, 'data.viewings')
            ->assertJsonPath('data.viewings.0.title', 'Orchard House viewing')
            ->assertJsonPath('data.companies.0.title', 'Orchard Developments');
    }

    public function test_inspection_records_condition_damage_and_signatures(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create();

        $this->postJson('/api/v1/inspections', [
            'property_id' => $property->id,
            'type' => 'check_in',
            'scheduled_at' => now()->addDay()->toIso8601String(),
            'areas' => [['name' => 'Kitchen', 'condition' => 'good']],
            'damage_reports' => [[
                'area' => 'Kitchen',
                'description' => 'Small mark on worktop',
                'severity' => 'minor',
            ]],
            'signatures' => [[
                'role' => 'inspector',
                'name' => $user->name,
                'signed_at' => now()->toIso8601String(),
                'signature' => 'signed-reference',
            ]],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.status', 'scheduled')
            ->assertJsonPath('data.areas.0.name', 'Kitchen');
    }

    public function test_communication_history_can_be_attached_to_a_contact(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $contact = Contact::create([
            'team_id' => $team->id,
            'type' => 'buyer',
            'first_name' => 'Taylor',
        ]);

        $this->postJson('/api/v1/communications', [
            'related_type' => 'contact',
            'related_id' => $contact->id,
            'channel' => 'phone',
            'direction' => 'outbound',
            'body' => 'Discussed viewing availability.',
            'occurred_at' => now()->toIso8601String(),
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.created_by', $user->id)
            ->assertJsonPath('data.communicable_id', $contact->id);

        $this->assertCount(1, $contact->communications()->get());
    }

    public function test_property_crud_is_available_through_the_versioned_api(): void
    {
        [, $team] = $this->actingAsTeamMember();

        $response = $this->postJson('/api/v1/properties', [
            'title' => 'Harbour View',
            'description' => 'A modern waterfront apartment.',
            'internal_notes' => 'Vendor prefers accompanied viewings.',
            'location' => 'Bristol',
            'structured_address' => [
                'line_1' => '10 Harbour Way',
                'city' => 'Bristol',
                'postal_code' => 'BS1 5UH',
                'country' => 'GB',
            ],
            'price' => 450000,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'reception_rooms' => 1,
            'parking' => [
                'spaces' => 1,
                'types' => ['allocated', 'underground'],
            ],
            'gardens' => [
                'communal' => true,
                'orientation' => 'south_west',
            ],
            'area_sqft' => 950,
            'year_built' => 2022,
            'property_type' => 'apartment',
            'epc' => [
                'rating' => 'B',
                'score' => 84,
                'assessment_date' => '2026-01-01',
                'expiry_date' => '2036-01-01',
                'certificate_reference' => 'EPC-100',
            ],
            'feature_names' => ['balcony', 'concierge'],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.structured_address.line_1', '10 Harbour Way')
            ->assertJsonPath('data.reception_rooms', 1)
            ->assertJsonPath('data.parking.types.1', 'underground')
            ->assertJsonPath('data.gardens.communal', true)
            ->assertJsonPath('data.epc.rating', 'B')
            ->assertJsonPath('data.feature_names.0', 'balcony')
            ->assertJsonPath('data.feature_names.1', 'concierge');

        $propertyId = $response->json('data.id');

        $this->patchJson("/api/v1/properties/$propertyId", [
            'status' => 'available',
            'feature_names' => ['waterfront'],
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'available')
            ->assertJsonCount(1, 'data.feature_names')
            ->assertJsonPath('data.feature_names.0', 'waterfront');
        $this->assertDatabaseMissing('property_features', [
            'property_id' => $propertyId,
            'feature_name' => 'balcony',
        ]);
        $this->assertDatabaseHas('property_features', [
            'team_id' => $team->id,
            'property_id' => $propertyId,
            'feature_name' => 'waterfront',
        ]);

        $this->getJson("/api/v1/properties/$propertyId")
            ->assertOk()
            ->assertJsonPath('data.internal_notes', 'Vendor prefers accompanied viewings.')
            ->assertJsonPath('data.feature_names.0', 'waterfront');

        $this->deleteJson("/api/v1/properties/$propertyId")->assertNoContent();
        $this->assertSoftDeleted('properties', ['id' => $propertyId]);
    }

    public function test_valuation_and_viewing_workflows_are_tenant_scoped(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create();
        $comparable = Property::factory()->for($team)->create();
        $scheduledAt = now()->addDays(2)->setTime(10, 30);

        $valuationResponse = $this->postJson('/api/v1/valuations', [
            'property_id' => $property->id,
            'valuation_type' => 'market',
            'estimated_value' => 425000,
            'valuation_date' => $scheduledAt->toDateString(),
            'scheduled_at' => $scheduledAt->toIso8601String(),
            'follow_up_at' => $scheduledAt->copy()->addDay()->toIso8601String(),
            'confidence_level' => 85,
            'comparable_properties' => [[
                'property_id' => $comparable->id,
                'value' => 410000,
                'currency' => 'GBP',
                'notes' => 'Similar floor area and condition.',
            ]],
        ])->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.assigned_to', $user->id)
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.comparable_properties.0.property_id', $comparable->id);
        $valuationId = $valuationResponse->json('data.id');

        $this->getJson('/api/v1/calendar?'.http_build_query([
            'start' => $scheduledAt->copy()->startOfDay()->toIso8601String(),
            'end' => $scheduledAt->copy()->endOfDay()->toIso8601String(),
            'types' => ['valuation'],
            'staff_id' => $user->id,
        ]))->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', "valuation:$valuationId")
            ->assertJsonPath('data.0.all_day', false);

        $this->patchJson("/api/v1/valuations/$valuationId", [
            'status' => 'completed',
            'completed_at' => $scheduledAt->copy()->addHour()->toIso8601String(),
            'outcome' => 'instruction_won',
            'outcome_notes' => 'Vendor instructed the agency.',
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.outcome', 'instruction_won');

        $this->postJson('/api/v1/viewings', [
            'property_id' => $property->id,
            'appointment_date' => now()->addDay()->toIso8601String(),
            'name' => 'Prospective buyer',
        ])->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.status', 'scheduled');

        $otherProperty = Property::factory()->create();
        $this->postJson('/api/v1/valuations', [
            'property_id' => $property->id,
            'valuation_type' => 'market',
            'valuation_date' => now()->toDateString(),
            'comparable_properties' => [[
                'property_id' => $otherProperty->id,
                'value' => 100000,
            ]],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('comparable_properties.0.property_id');

        $this->postJson('/api/v1/viewings', [
            'property_id' => $otherProperty->id,
            'appointment_date' => now()->addDay()->toIso8601String(),
        ])->assertUnprocessable()->assertJsonValidationErrors('property_id');
    }

    public function test_public_website_api_only_exposes_publishable_agency_properties(): void
    {
        $team = Team::factory()->create();
        $published = Property::factory()->for($team)->create([
            'title' => 'Published Home',
            'status' => 'available',
            'is_featured' => true,
        ]);
        Property::factory()->for($team)->create([
            'title' => 'Internal Draft',
            'status' => 'draft',
        ]);
        Property::factory()->create([
            'title' => 'Another Agency Home',
            'status' => 'available',
        ]);

        $this->getJson("/api/v1/public/agencies/{$team->id}/properties?featured=1")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $published->id);

        $this->getJson("/api/v1/public/agencies/{$team->id}/properties/{$published->id}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Published Home');
    }

    public function test_dashboard_and_pipeline_reports_are_team_scoped(): void
    {
        [, $team] = $this->actingAsTeamMember();
        Property::factory()->for($team)->create(['status' => 'available']);
        Property::factory()->for($team)->create(['status' => 'sold']);
        Property::factory()->create(['status' => 'available']);

        $this->getJson('/api/v1/reports/dashboard')
            ->assertOk()
            ->assertJsonPath('data.properties', 2)
            ->assertJsonPath('data.available_properties', 1);

        $this->getJson('/api/v1/reports/pipeline')
            ->assertOk()
            ->assertJsonPath('data.properties.available', 1)
            ->assertJsonPath('data.properties.sold', 1);
    }

    public function test_performance_reports_cover_staff_branches_marketing_and_portals(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $branch = Branch::create(['team_id' => $team->id, 'name' => 'Central']);
        $team->users()->updateExistingPivot($user->id, ['branch_id' => $branch->id]);
        $contact = Contact::create([
            'team_id' => $team->id,
            'type' => 'buyer',
            'first_name' => 'Report',
        ]);
        $property = Property::factory()->for($team)->create([
            'agent_id' => $user->id,
            'status' => 'available',
        ]);
        Offer::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'contact_id' => $contact->id,
            'negotiator_id' => $user->id,
            'amount' => 420000,
            'status' => 'accepted',
            'offered_at' => now(),
        ]);
        Appointment::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'staff_id' => $user->id,
            'status' => 'completed',
            'appointment_date' => now(),
        ]);
        EmailCampaign::create([
            'team_id' => $team->id,
            'created_by' => $user->id,
            'name' => 'Launch',
            'subject' => 'New instruction',
            'content' => 'A new property is available.',
            'status' => 'sent',
            'recipients_count' => 20,
            'delivered_count' => 18,
            'opened_count' => 12,
            'clicked_count' => 4,
        ]);
        $integration = PortalIntegration::create([
            'team_id' => $team->id,
            'branch_id' => $branch->id,
            'provider' => 'rightmove',
            'country' => 'GB',
            'channel' => 'sales',
            'active' => true,
        ]);
        PortalListing::create([
            'team_id' => $team->id,
            'portal_integration_id' => $integration->id,
            'property_id' => $property->id,
            'status' => 'published',
        ]);
        PortalSyncRun::create([
            'team_id' => $team->id,
            'portal_integration_id' => $integration->id,
            'requested_by' => $user->id,
            'status' => 'completed',
            'processed' => 2,
            'succeeded' => 1,
            'failed' => 1,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $performance = $this->getJson("/api/v1/reports/performance?branch_id={$branch->id}")
            ->assertOk()
            ->assertJsonPath('data.instructions.available', 1)
            ->assertJsonPath('data.offers.accepted', 1)
            ->assertJsonPath('data.viewings.completed', 1)
            ->assertJsonPath('data.marketing_performance.delivered', 18)
            ->assertJsonPath('data.portal_statistics.published_listings', 1)
            ->assertJsonPath('data.portal_statistics.failed_items', 1);
        $this->assertSame(3, $performance->json('data.staff_performance')["{$user->name} #{$user->id}"]);
        $this->assertSame(3, $performance->json('data.branch_performance')["Central #{$branch->id}"]);

        $otherBranch = Branch::create([
            'team_id' => Team::factory()->create()->id,
            'name' => 'Hidden',
        ]);
        $this->getJson("/api/v1/reports/performance?branch_id={$otherBranch->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors('branch_id');

        $saved = $this->postJson('/api/v1/saved-reports', [
            'name' => 'Central performance',
            'type' => 'performance',
            'filters' => ['branch_id' => $branch->id],
            'chart_type' => 'bar',
        ])->assertCreated();
        $this->getJson("/api/v1/saved-reports/{$saved->json('data.id')}/run")
            ->assertOk()
            ->assertJsonPath('data.portal_statistics.listings', 1)
            ->assertJsonPath('meta.chart.0.label', 'instructions');
    }

    public function test_saved_reports_export_chart_data_and_dashboard_layouts_are_customisable(): void
    {
        [, $team] = $this->actingAsTeamMember();
        Property::factory()->for($team)->create(['status' => 'available']);
        Property::factory()->for($team)->create(['status' => 'sold']);

        $report = $this->postJson('/api/v1/saved-reports', [
            'name' => 'Monthly property pipeline',
            'type' => 'pipeline',
            'filters' => [
                'from' => now()->subDay()->toDateString(),
                'to' => now()->addDay()->toDateString(),
            ],
            'chart_type' => 'bar',
            'is_shared' => true,
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id);
        $reportId = $report->json('data.id');

        $this->getJson("/api/v1/saved-reports/$reportId/run")
            ->assertOk()
            ->assertJsonPath('data.properties.available', 1)
            ->assertJsonPath('data.properties.sold', 1)
            ->assertJsonPath('meta.report.chart_type', 'bar')
            ->assertJsonPath('meta.chart.0.label', 'properties');
        $this->get("/api/v1/saved-reports/$reportId/export")
            ->assertOk()
            ->assertDownload("report-$reportId.csv")
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $first = $this->postJson('/api/v1/dashboard-layouts', [
            'name' => 'Negotiator dashboard',
            'is_default' => true,
            'widgets' => [
                ['type' => 'kpis', 'position' => ['x' => 0, 'y' => 0]],
                ['type' => 'tasks', 'settings' => ['limit' => 10]],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.is_default', true)
            ->assertJsonPath('data.widgets.1.type', 'tasks');
        $firstId = $first->json('data.id');

        $this->postJson('/api/v1/dashboard-layouts', [
            'name' => 'Branch dashboard',
            'is_default' => true,
            'widgets' => [['type' => 'branch_statistics']],
        ])->assertCreated();

        $this->getJson('/api/v1/dashboard-layouts')
            ->assertOk()
            ->assertJsonCount(2, 'data');
        $this->assertDatabaseHas('dashboard_layouts', [
            'id' => $firstId,
            'is_default' => false,
        ]);
    }

    public function test_buyers_can_be_matched_to_available_properties_and_notified(): void
    {
        Notification::fake();
        [$user, $team] = $this->actingAsTeamMember();

        $buyerResponse = $this->postJson('/api/v1/buyers', [
            'name' => 'Jordan Buyer',
            'email' => 'jordan@example.test',
            'user_id' => $user->id,
            'search_criteria' => [
                'min_price' => 200000,
                'max_price' => 400000,
                'min_bedrooms' => 2,
                'property_type' => 'house',
                'location' => 'York',
            ],
        ])->assertCreated()->assertJsonPath('data.team_id', $team->id);

        $buyer = Buyer::findOrFail($buyerResponse->json('data.id'));
        $property = Property::factory()->for($team)->create([
            'status' => 'available',
            'price' => 300000,
            'bedrooms' => 3,
            'property_type' => 'house',
            'location' => 'York city centre',
        ]);
        Property::factory()->create([
            'status' => 'available',
            'price' => 300000,
            'bedrooms' => 3,
            'property_type' => 'house',
            'location' => 'York',
        ]);

        $generate = $this->postJson("/api/v1/buyers/{$buyer->id}/generate-matches")
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.property_id', $property->id);

        $matchId = $generate->json('data.0.id');
        $this->patchJson("/api/v1/property-matches/$matchId", [
            'status' => 'interested',
            'buyer_interest_level' => 5,
        ])->assertOk()
            ->assertJsonPath('data.status', 'interested')
            ->assertJsonPath('data.buyer_interest_level', 5);

        Notification::assertSentTo($user, NewPropertyMatches::class);
    }

    public function test_tenants_match_to_let_properties_by_radius_features_schools_and_transport(): void
    {
        Notification::fake();
        [$user, $team] = $this->actingAsTeamMember();

        $tenantResponse = $this->postJson('/api/v1/tenants', [
            'name' => 'Taylor Tenant',
            'email' => 'taylor.tenant@example.test',
            'user_id' => $user->id,
            'search_criteria' => [
                'max_price' => 2500,
                'min_bedrooms' => 2,
                'property_type' => 'apartment',
                'availability' => ['to_let'],
                'latitude' => 51.5007,
                'longitude' => -0.1246,
                'radius_km' => 5,
                'required_features' => ['balcony'],
                'required_schools' => ['Oak Academy'],
                'min_transit_score' => 70,
            ],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.search_criteria.radius_km', 5);
        $tenantId = $tenantResponse->json('data.id');

        $neighborhood = Neighborhood::create([
            'name' => 'Westminster',
            'description' => 'Central neighbourhood',
            'schools' => [['name' => 'Oak Academy']],
            'amenities' => ['underground'],
            'crime_rate' => 'low',
            'transit_score' => 90,
        ]);
        $property = Property::factory()->for($team)->create([
            'status' => 'to_let',
            'price' => 2200,
            'bedrooms' => 2,
            'property_type' => 'apartment',
            'location' => 'Westminster',
            'latitude' => 51.501,
            'longitude' => -0.125,
            'transit_score' => 88,
            'neighborhood_id' => $neighborhood->id,
        ]);
        PropertyFeature::create([
            'property_id' => $property->id,
            'feature_name' => 'balcony',
        ]);
        $farProperty = Property::factory()->for($team)->create([
            'status' => 'to_let',
            'price' => 2100,
            'bedrooms' => 2,
            'property_type' => 'apartment',
            'location' => 'Manchester',
            'latitude' => 53.4808,
            'longitude' => -2.2426,
            'transit_score' => 90,
            'neighborhood_id' => $neighborhood->id,
        ]);
        PropertyFeature::create([
            'property_id' => $farProperty->id,
            'feature_name' => 'balcony',
        ]);

        $generate = $this->postJson("/api/v1/tenants/$tenantId/generate-matches")
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.tenant_id', $tenantId)
            ->assertJsonPath('data.0.buyer_id', null)
            ->assertJsonPath('data.0.property_id', $property->id)
            ->assertJsonPath('data.0.applicant_type', 'tenant')
            ->assertJsonPath('data.0.availability', 'to_let')
            ->assertJsonPath('data.0.school_match', '100.00')
            ->assertJsonPath('data.0.transport_match', '100.00')
            ->assertJsonPath('data.0.distance_km', fn ($distance) => (float) $distance < 1);

        $matchId = $generate->json('data.0.id');
        $this->patchJson("/api/v1/property-matches/$matchId", [
            'status' => 'interested',
            'viewed_by_applicant' => true,
            'applicant_interest_level' => 4,
        ])->assertOk()
            ->assertJsonPath('data.applicant_interest_level', 4)
            ->assertJsonPath('data.viewed_by_applicant', true);
        $this->getJson("/api/v1/property-matches?applicant_type=tenant&tenant_id=$tenantId")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchId);

        $otherTenant = Tenant::create([
            'team_id' => Team::factory()->create()->id,
            'name' => 'Hidden tenant',
            'email' => 'hidden.tenant@example.test',
        ]);
        $this->postJson("/api/v1/tenants/{$otherTenant->id}/generate-matches")->assertNotFound();

        Notification::assertSentTo($user, NewPropertyMatches::class);
    }

    public function test_tenancy_agreements_support_deposits_notices_and_renewals(): void
    {
        [, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create();

        $tenantResponse = $this->postJson('/api/v1/tenants', [
            'name' => 'Morgan Tenant',
            'email' => 'morgan.tenant@example.test',
            'phone' => '+44 7700 900123',
        ])->assertCreated()->assertJsonPath('data.team_id', $team->id);

        $agreementResponse = $this->postJson('/api/v1/tenancy-agreements', [
            'tenant_id' => $tenantResponse->json('data.id'),
            'property_id' => $property->id,
            'start_date' => '2026-08-01',
            'end_date' => '2027-07-31',
            'monthly_rent' => 1750,
            'security_deposit' => 2000,
            'deposit_scheme' => 'DPS',
            'deposit_reference' => 'DPS-12345',
            'payment_frequency' => 'monthly',
            'status' => 'active',
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.deposit_reference', 'DPS-12345');

        $agreementId = $agreementResponse->json('data.id');
        $this->postJson("/api/v1/tenancy-agreements/$agreementId/notice", [
            'notice_type' => 'tenant',
            'notice_served_at' => '2027-05-01',
            'notice_expires_at' => '2027-07-31',
            'end_reason' => 'Tenant is relocating.',
        ])->assertOk()->assertJsonPath('data.status', 'notice_served');

        $this->postJson("/api/v1/tenancy-agreements/$agreementId/renew", [
            'start_date' => '2027-07-31',
            'end_date' => '2028-07-30',
            'monthly_rent' => 1825,
        ])->assertCreated()
            ->assertJsonPath('data.renewal_of_id', $agreementId)
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.monthly_rent', '1825.00');
    }

    public function test_automation_rules_create_tasks_and_deliver_notifications_across_configured_channels(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        Mail::fake();

        $this->putJson('/api/v1/notifications/preferences', [
            'channels' => ['in_app', 'email', 'sms', 'push'],
            'phone' => '+44 7700 900123',
            'push_tokens' => ['private-device-token'],
            'event_preferences' => ['offer.accepted' => true],
        ])->assertOk()
            ->assertJsonPath('data.push_token_count', 1)
            ->assertJsonMissing(['private-device-token']);

        ServiceIntegration::create([
            'team_id' => $team->id,
            'category' => 'sms',
            'provider' => 'twilio',
            'name' => 'Agency SMS',
            'credentials' => ['secret' => 'never-return-this'],
            'active' => true,
            'is_default' => true,
        ]);
        ServiceIntegration::create([
            'team_id' => $team->id,
            'category' => 'push',
            'provider' => 'firebase',
            'name' => 'Agency Push',
            'credentials' => ['secret' => 'never-return-this-either'],
            'active' => true,
            'is_default' => true,
        ]);

        $automation = $this->postJson('/api/v1/automations', [
            'name' => 'Follow up accepted offers',
            'trigger' => 'offer.accepted',
            'conditions' => [[
                'field' => 'offer.status',
                'operator' => 'equals',
                'value' => 'accepted',
            ]],
            'actions' => [
                [
                    'type' => 'create_task',
                    'title' => 'Issue memorandum of sale',
                    'priority' => 'high',
                    'assigned_to' => $user->id,
                    'due_in_days' => 1,
                ],
                [
                    'type' => 'notify_user',
                    'user_id' => $user->id,
                    'title' => 'Offer accepted',
                    'body' => 'Sales progression can now begin.',
                    'channels' => ['in_app', 'email', 'sms', 'push'],
                ],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id);

        $automationId = $automation->json('data.id');
        $this->postJson("/api/v1/automations/$automationId/run", [
            'context' => ['offer' => ['id' => 42, 'status' => 'accepted']],
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonCount(2, 'data.results')
            ->assertJsonCount(4, 'data.results.1.deliveries');

        $this->assertDatabaseHas('agency_tasks', [
            'team_id' => $team->id,
            'title' => 'Issue memorandum of sale',
        ]);
        $this->getJson('/api/v1/automation-runs')
            ->assertOk()
            ->assertJsonPath('data.0.automation_rule_id', $automationId);
        $notifications = $this->getJson('/api/v1/notifications?unread=1')
            ->assertOk()
            ->assertJsonPath('meta.unread_count', 1)
            ->assertJsonPath('data.0.data.type', 'offer.accepted');
        $notificationId = $notifications->json('data.0.id');
        $this->patchJson("/api/v1/notifications/$notificationId/read")
            ->assertOk()
            ->assertJsonPath('data.read_at', fn ($value) => $value !== null);

        $this->getJson('/api/v1/notifications/preferences')
            ->assertOk()
            ->assertJsonPath('data.channels.3', 'push')
            ->assertJsonPath('data.push_token_count', 1)
            ->assertJsonMissing(['private-device-token']);
        $this->getJson('/api/v1/notifications/deliveries?event_type=offer.accepted')
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonFragment(['channel' => 'email', 'status' => 'sent'])
            ->assertJsonFragment(['channel' => 'sms', 'status' => 'queued', 'provider' => 'twilio'])
            ->assertJsonFragment(['channel' => 'push', 'status' => 'queued', 'provider' => 'firebase'])
            ->assertJsonMissing(['never-return-this'])
            ->assertJsonMissing(['private-device-token']);

        $this->assertDatabaseHas('notification_deliveries', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'event_type' => 'offer.accepted',
            'channel' => 'in_app',
            'status' => 'sent',
        ]);
    }

    public function test_automation_assigns_staff_publishes_exports_and_schedules_reminders(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create([
            'status' => 'draft',
            'agent_id' => null,
        ]);
        $integration = PortalIntegration::create([
            'team_id' => $team->id,
            'provider' => 'rightmove',
            'country' => 'GB',
            'channel' => 'sales',
            'credentials' => ['api_key' => 'secret'],
            'active' => true,
        ]);

        $automation = $this->postJson('/api/v1/automations', [
            'name' => 'Launch instructed property',
            'trigger' => 'instruction.accepted',
            'actions' => [
                [
                    'type' => 'assign_staff',
                    'assigned_to' => $user->id,
                ],
                [
                    'type' => 'publish_listing',
                    'property_status' => 'available',
                ],
                [
                    'type' => 'export_portal',
                    'portal_integration_id' => $integration->id,
                ],
                [
                    'type' => 'schedule_reminder',
                    'title' => 'Check portal publication',
                    'description' => 'Confirm the listing was accepted.',
                    'assigned_to' => $user->id,
                    'due_in_hours' => 24,
                ],
            ],
        ])->assertCreated();

        $this->postJson('/api/v1/automations/'.$automation->json('data.id').'/run', [
            'context' => ['property_id' => $property->id],
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonCount(4, 'data.results')
            ->assertJsonPath('data.results.0.assigned_to', $user->id)
            ->assertJsonPath('data.results.1.status', 'available')
            ->assertJsonPath('data.results.2.portal_integration_id', $integration->id)
            ->assertJsonPath('data.results.2.status', 'pending')
            ->assertJsonPath('data.results.3.assigned_to', $user->id);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'team_id' => $team->id,
            'agent_id' => $user->id,
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('portal_listings', [
            'team_id' => $team->id,
            'portal_integration_id' => $integration->id,
            'property_id' => $property->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('calendar_entries', [
            'team_id' => $team->id,
            'property_id' => $property->id,
            'organiser_id' => $user->id,
            'type' => 'reminder',
            'title' => 'Check portal publication',
        ]);

        $otherIntegration = PortalIntegration::create([
            'team_id' => Team::factory()->create()->id,
            'provider' => 'zoopla',
            'country' => 'GB',
            'channel' => 'sales',
            'active' => true,
        ]);
        $invalidAutomation = $this->postJson('/api/v1/automations', [
            'name' => 'Blocked cross-team export',
            'trigger' => 'listing.updated',
            'actions' => [[
                'type' => 'export_portal',
            ]],
        ])->assertCreated();
        $this->postJson('/api/v1/automations/'.$invalidAutomation->json('data.id').'/run', [
            'context' => [
                'property_id' => $property->id,
                'portal_integration_id' => $otherIntegration->id,
            ],
        ])->assertUnprocessable()
            ->assertJsonPath('data.status', 'failed');
        $this->assertDatabaseMissing('portal_listings', [
            'portal_integration_id' => $otherIntegration->id,
            'property_id' => $property->id,
        ]);
    }

    public function test_team_permissions_are_granular_and_api_tokens_are_self_managed(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();
        $team->users()->attach($owner, ['role' => 'admin']);
        $team->users()->attach($member, ['role' => 'member']);
        $owner->forceFill(['current_team_id' => $team->id])->save();
        $member->forceFill(['current_team_id' => $team->id])->save();

        Sanctum::actingAs($member);
        $this->getJson('/api/v1/contacts')->assertOk();
        $this->postJson('/api/v1/contacts', [
            'type' => 'buyer',
            'first_name' => 'Blocked',
        ])->assertForbidden()->assertJsonPath('permission', 'contacts.create');

        $token = $this->postJson('/api/v1/api-tokens', [
            'name' => 'Mobile app',
            'abilities' => ['properties.view', 'contacts.view'],
            'expires_at' => now()->addMonth()->toIso8601String(),
        ])->assertCreated()
            ->assertJsonPath('data.name', 'Mobile app')
            ->assertJsonPath('data.abilities.0', 'properties.view');
        $this->assertNotEmpty($token->json('data.token'));

        Sanctum::actingAs($owner);
        $this->putJson("/api/v1/permissions/members/{$member->id}", [
            'role' => 'member',
            'permissions' => ['contacts.view', 'contacts.create'],
        ])->assertOk()
            ->assertJsonPath('data.permissions.1', 'contacts.create');

        Sanctum::actingAs($member->fresh());
        $this->postJson('/api/v1/contacts', [
            'type' => 'buyer',
            'first_name' => 'Allowed',
        ])->assertCreated()->assertJsonPath('data.first_name', 'Allowed');
        $this->deleteJson('/api/v1/contacts/999999')
            ->assertForbidden()
            ->assertJsonPath('permission', 'contacts.delete');
    }

    public function test_role_hierarchy_prevents_privilege_escalation_and_audits_changes(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $admin = User::factory()->create();
        $editor = User::factory()->create();
        $member = User::factory()->create();
        $team->users()->attach($owner, ['role' => 'admin']);
        $team->users()->attach($admin, ['role' => 'admin']);
        $team->users()->attach($editor, [
            'role' => 'editor',
            'permissions' => ['staff.edit'],
        ]);
        $team->users()->attach($member, ['role' => 'member']);

        foreach ([$owner, $admin, $editor, $member] as $user) {
            $user->forceFill(['current_team_id' => $team->id])->save();
        }

        Sanctum::actingAs($editor);
        $this->patchJson("/api/v1/staff/{$member->id}", [
            'role' => 'admin',
        ])->assertForbidden();

        Sanctum::actingAs($admin);
        $this->putJson("/api/v1/permissions/members/{$member->id}", [
            'role' => 'admin',
        ])->assertForbidden();

        Sanctum::actingAs($owner);
        $this->putJson("/api/v1/permissions/members/{$member->id}", [
            'role' => 'manager',
            'permissions' => null,
        ])->assertOk()
            ->assertJsonPath('data.role', 'manager')
            ->assertJsonPath('data.effective_permissions', fn ($permissions) => in_array('contacts.create', $permissions, true))
            ->assertJsonPath('data.effective_permissions', fn ($permissions) => ! in_array('permissions.edit', $permissions, true));

        $this->getJson('/api/v1/permissions/audits')
            ->assertOk()
            ->assertJsonPath('data.0.actor_id', $owner->id)
            ->assertJsonPath('data.0.subject_id', $member->id)
            ->assertJsonPath('data.0.old_role', 'member')
            ->assertJsonPath('data.0.new_role', 'manager');

        Sanctum::actingAs($member->fresh());
        $this->getJson('/api/v1/permissions/members')->assertForbidden();
        $this->getJson('/api/v1/staff')->assertOk();
        $this->postJson('/api/v1/staff', [
            'email' => User::factory()->create()->email,
            'role' => 'member',
        ])->assertForbidden();
    }

    public function test_portal_integrations_publish_and_audit_team_scoped_listing_exports(): void
    {
        [, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create([
            'status' => 'available',
            'title' => 'Portal Ready Home',
            'postal_code' => 'YO1 1AA',
        ]);

        $integration = $this->postJson('/api/v1/portal-integrations', [
            'provider' => 'rightmove',
            'country' => 'GB',
            'channel' => 'sales',
            'sync_frequency' => 'daily',
            'credentials' => ['api_key' => 'secret-value'],
            'settings' => ['feed_type' => 'incremental'],
        ])->assertCreated()
            ->assertJsonMissingPath('data.credentials')
            ->assertJsonPath('data.provider', 'rightmove');

        $integrationId = $integration->json('data.id');
        $this->putJson("/api/v1/portal-integrations/$integrationId/properties/{$property->id}")
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending');

        $this->postJson("/api/v1/portal-integrations/$integrationId/sync")
            ->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.processed', 1)
            ->assertJsonPath('data.succeeded', 1);

        $this->assertDatabaseHas('portal_listings', [
            'team_id' => $team->id,
            'property_id' => $property->id,
            'status' => 'published',
        ]);
        $this->getJson("/api/v1/portal-sync-runs?portal_integration_id=$integrationId")
            ->assertOk()
            ->assertJsonPath('data.0.status', 'completed');

        $this->deleteJson("/api/v1/portal-integrations/$integrationId/properties/{$property->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'withdrawn');

        $otherProperty = Property::factory()->create();
        $this->putJson("/api/v1/portal-integrations/$integrationId/properties/{$otherProperty->id}")
            ->assertNotFound();
    }

    public function test_accounting_integrations_reference_external_finance_without_bookkeeping(): void
    {
        [, $team] = $this->actingAsTeamMember();
        $contact = Contact::create([
            'team_id' => $team->id,
            'type' => 'landlord',
            'first_name' => 'Alex',
            'last_name' => 'Landlord',
        ]);

        $integration = $this->postJson('/api/v1/accounting-integrations', [
            'provider' => 'xero',
            'name' => 'Agency Xero',
            'credentials' => ['tenant_id' => 'secret-tenant'],
            'settings' => ['sync_customers' => true],
        ])->assertCreated()
            ->assertJsonMissingPath('data.credentials')
            ->assertJsonPath('data.provider', 'xero');

        $integrationId = $integration->json('data.id');
        $link = $this->postJson('/api/v1/accounting-links', [
            'accounting_integration_id' => $integrationId,
            'link_type' => 'invoice',
            'linkable_type' => 'contact',
            'linkable_id' => $contact->id,
            'external_id' => 'XERO-CONTACT-1',
            'invoice_reference' => 'INV-1001',
            'payment_status' => 'overdue',
            'amount' => 1250,
            'currency' => 'GBP',
            'due_date' => now()->subDay()->toDateString(),
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.invoice_reference', 'INV-1001');

        $this->getJson('/api/v1/accounting-summary')
            ->assertOk()
            ->assertJsonPath('data.overdue_count', 1)
            ->assertJsonPath('data.outstanding_amount', 1250)
            ->assertJsonPath('data.currency_totals.GBP', 1250);

        $this->postJson("/api/v1/accounting-integrations/$integrationId/sync")
            ->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.processed', 1);
        $this->getJson("/api/v1/accounting-sync-runs?integration_id=$integrationId")
            ->assertOk()
            ->assertJsonPath('data.0.succeeded', 1);

        $otherContact = Contact::create([
            'team_id' => Team::factory()->create()->id,
            'type' => 'landlord',
            'first_name' => 'Hidden',
        ]);
        $this->postJson('/api/v1/accounting-links', [
            'accounting_integration_id' => $integrationId,
            'link_type' => 'customer',
            'linkable_type' => 'contact',
            'linkable_id' => $otherContact->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('linkable_id');

        $this->deleteJson('/api/v1/accounting-links/'.$link->json('data.id'))->assertNoContent();
    }

    public function test_unified_calendar_and_task_collaboration_are_team_scoped(): void
    {
        Storage::fake('local');
        [$user, $team] = $this->actingAsTeamMember();
        $dueAt = now()->addDays(2)->setSecond(0);

        $task = $this->postJson('/api/v1/tasks', [
            'title' => 'Prepare viewing pack',
            'due_at' => $dueAt->toIso8601String(),
            'checklist' => [
                ['label' => 'Print brochure', 'completed' => false],
                ['label' => 'Confirm keys', 'completed' => false],
            ],
        ])->assertCreated();
        $taskId = $task->json('data.id');

        $comment = $this->postJson("/api/v1/tasks/$taskId/comments", [
            'body' => 'Brochure is ready for review.',
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id);
        $this->getJson("/api/v1/tasks/$taskId/comments")
            ->assertOk()
            ->assertJsonPath('data.0.body', 'Brochure is ready for review.');

        $attachment = $this->postJson("/api/v1/tasks/$taskId/attachments", [
            'file' => UploadedFile::fake()->create('brochure.pdf', 120, 'application/pdf'),
        ])->assertCreated()
            ->assertJsonPath('data.name', 'brochure.pdf');
        Storage::disk('local')->assertExists($attachment->json('data.path'));

        $this->patchJson("/api/v1/tasks/$taskId/checklist/0", ['completed' => true])
            ->assertOk()
            ->assertJsonPath('data.checklist.0.completed', true)
            ->assertJsonPath('data.checklist.1.completed', false);

        $this->getJson('/api/v1/calendar?types[]=task&start='.now()->toDateString().'&end='.now()->addWeek()->toDateString())
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', "task:$taskId")
            ->assertJsonPath('data.0.title', 'Prepare viewing pack');

        $meetingStarts = now()->addDays(3)->setSecond(0);
        $meeting = $this->postJson('/api/v1/calendar-entries', [
            'type' => 'meeting',
            'title' => 'Vendor marketing review',
            'location' => 'Main branch',
            'starts_at' => $meetingStarts->toIso8601String(),
            'ends_at' => $meetingStarts->copy()->addHour()->toIso8601String(),
            'reminder_at' => $meetingStarts->copy()->subHour()->toIso8601String(),
            'attendee_user_ids' => [$user->id],
            'recurrence' => [
                'frequency' => 'weekly',
                'interval' => 1,
                'weekdays' => [(int) $meetingStarts->isoWeekday()],
                'count' => 4,
            ],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.organiser_id', $user->id)
            ->assertJsonPath('data.recurrence.frequency', 'weekly');
        $meetingId = $meeting->json('data.id');

        $reminder = $this->postJson('/api/v1/calendar-entries', [
            'type' => 'reminder',
            'title' => 'Call landlord',
            'starts_at' => now()->addDays(4)->setSecond(0)->toIso8601String(),
            'all_day' => true,
        ])->assertCreated();
        $reminderId = $reminder->json('data.id');

        $this->getJson('/api/v1/calendar?'.http_build_query([
            'types' => ['meeting', 'reminder'],
            'staff_id' => $user->id,
            'start' => now()->toDateString(),
            'end' => now()->addWeek()->toDateString(),
        ]))->assertOk()
            ->assertJsonPath('meta.total', 2)
            ->assertJsonFragment(['id' => "meeting:$meetingId", 'title' => 'Vendor marketing review'])
            ->assertJsonFragment(['id' => "reminder:$reminderId", 'all_day' => true]);

        $this->patchJson("/api/v1/calendar-entries/$meetingId", ['status' => 'completed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'completed');
        $this->getJson('/api/v1/calendar-entries?filter[type]=meeting&filter[status]=completed')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $meetingId);

        $otherContact = Contact::create([
            'team_id' => Team::factory()->create()->id,
            'type' => 'vendor',
            'first_name' => 'Hidden',
        ]);
        $this->postJson('/api/v1/calendar-entries', [
            'type' => 'meeting',
            'title' => 'Invalid cross-team meeting',
            'starts_at' => now()->addDay()->toIso8601String(),
            'contact_ids' => [$otherContact->id],
        ])->assertUnprocessable()->assertJsonValidationErrors('contact_ids.0');

        $otherTask = AgencyTask::create([
            'team_id' => Team::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
            'title' => 'Hidden task',
            'due_at' => $dueAt,
        ]);
        $calendarResponse = $this->getJson('/api/v1/calendar?types[]=task&start='.now()->toDateString().'&end='.now()->addWeek()->toDateString())
            ->assertOk();

        $this->assertNotContains(
            $otherTask->id,
            collect($calendarResponse->json('data'))->pluck('record_id')->all(),
        );

        $this->deleteJson("/api/v1/tasks/$taskId/attachments/".$attachment->json('data.id'))->assertNoContent();
        Storage::disk('local')->assertMissing($attachment->json('data.path'));
        $this->deleteJson("/api/v1/tasks/$taskId/comments/".$comment->json('data.id'))->assertNoContent();
        $this->deleteJson("/api/v1/calendar-entries/$reminderId")->assertNoContent();
    }

    public function test_document_versions_and_signatures_are_private_and_auditable(): void
    {
        Storage::fake('local');
        [$user, $team] = $this->actingAsTeamMember();

        $document = $this->postJson('/api/v1/documents', [
            'title' => 'Tenancy agreement',
            'is_signable' => true,
        ])->assertCreated();
        $documentId = $document->json('data.id');

        $first = $this->postJson("/api/v1/documents/$documentId/versions", [
            'file' => UploadedFile::fake()->createWithContent('agreement-v1.pdf', 'first version'),
            'notes' => 'Initial draft',
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.version', 1)
            ->assertJsonPath('data.file_name', 'agreement-v1.pdf');
        Storage::disk('local')->assertExists($first->json('data.file_path'));

        $second = $this->postJson("/api/v1/documents/$documentId/versions", [
            'file' => UploadedFile::fake()->createWithContent('agreement-v2.pdf', 'second version'),
        ])->assertCreated()
            ->assertJsonPath('data.version', 2);

        $versionId = $second->json('data.id');
        $this->getJson("/api/v1/documents/$documentId/versions")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.version', 2);
        $this->get("/api/v1/documents/$documentId/versions/$versionId/download")
            ->assertOk()
            ->assertDownload('agreement-v2.pdf');

        $this->postJson("/api/v1/documents/$documentId/signatures", [
            'document_version_id' => $versionId,
            'signature_data' => 'signed-by-user',
        ])->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.document_version_id', $versionId);

        $this->getJson("/api/v1/documents/$documentId/signatures")
            ->assertOk()
            ->assertJsonPath('data.0.version.version', 2)
            ->assertJsonPath('data.0.ip_address', '127.0.0.1');

        $otherDocument = Document::create([
            'team_id' => Team::factory()->create()->id,
            'title' => 'Private agreement',
        ]);
        $this->getJson("/api/v1/documents/{$otherDocument->id}/versions")->assertNotFound();
        $this->postJson("/api/v1/documents/{$otherDocument->id}/signatures", [
            'signature_data' => 'invalid',
        ])->assertNotFound();
    }

    public function test_documents_link_to_crm_records_with_team_categories_and_restricted_access(): void
    {
        [$owner, $team] = $this->actingAsTeamMember();
        $member = User::factory()->create();
        $team->users()->attach($member, ['role' => 'member']);
        $member->forceFill(['current_team_id' => $team->id])->save();
        $contact = Contact::create([
            'team_id' => $team->id,
            'type' => 'vendor',
            'first_name' => 'Morgan',
            'last_name' => 'Vendor',
        ]);

        $category = $this->postJson('/api/v1/document-categories', [
            'name' => 'Identity',
            'description' => 'Identity and verification records',
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id);
        $categoryId = $category->json('data.id');

        $restricted = $this->postJson('/api/v1/documents', [
            'title' => 'Vendor identity record',
            'related_type' => 'contact',
            'related_id' => $contact->id,
            'category_ids' => [$categoryId],
            'visibility' => 'restricted',
            'allowed_roles' => ['member'],
        ])->assertCreated()
            ->assertJsonPath('data.documentable_type', Contact::class)
            ->assertJsonPath('data.documentable_id', $contact->id)
            ->assertJsonPath('data.categories.0.name', 'Identity');
        $restrictedId = $restricted->json('data.id');

        $private = $this->postJson('/api/v1/documents', [
            'title' => 'Owner private notes',
            'visibility' => 'private',
        ])->assertCreated();
        $privateId = $private->json('data.id');

        $otherCategory = DocumentCategory::create([
            'team_id' => Team::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'name' => 'Hidden category',
        ]);
        $this->postJson('/api/v1/documents', [
            'title' => 'Invalid category document',
            'category_ids' => [$otherCategory->id],
        ])->assertUnprocessable()->assertJsonValidationErrors('category_ids.0');

        Sanctum::actingAs($member);
        $this->getJson("/api/v1/documents/$restrictedId")
            ->assertOk()
            ->assertJsonPath('data.title', 'Vendor identity record');
        $this->getJson("/api/v1/documents/$restrictedId/versions")
            ->assertOk()
            ->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/documents/$privateId")->assertNotFound();
        $this->getJson("/api/v1/documents/$privateId/versions")->assertNotFound();
        $this->getJson('/api/v1/documents')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $restrictedId);

        Sanctum::actingAs($owner);
        $this->deleteJson("/api/v1/document-categories/$categoryId")->assertNoContent();
    }

    public function test_property_marketing_assets_and_email_campaigns_are_team_scoped(): void
    {
        [, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create([
            'title' => 'Riverside Apartment',
            'location' => 'York',
            'price' => 425000,
            'bedrooms' => 2,
            'bathrooms' => 2,
        ]);

        $this->get("/api/v1/marketing-assets/properties/{$property->id}/brochure")
            ->assertOk()
            ->assertHeader('content-type', 'text/html; charset=UTF-8')
            ->assertSee('Riverside Apartment');
        $this->get("/api/v1/marketing-assets/properties/{$property->id}/window-card")
            ->assertOk()
            ->assertSee('£425,000', false);
        $this->getJson("/api/v1/marketing-assets/properties/{$property->id}/qr-code?size=300")
            ->assertOk()
            ->assertJsonPath('data.property_id', $property->id)
            ->assertJsonPath('data.size', 300);
        $this->getJson("/api/v1/marketing-assets/properties/{$property->id}/social")
            ->assertOk()
            ->assertJsonPath('data.headline', 'Riverside Apartment')
            ->assertJsonPath('data.caption', 'Riverside Apartment in York. 2 bedrooms, 2 bathrooms.');

        Contact::create([
            'team_id' => $team->id,
            'type' => 'buyer',
            'first_name' => 'Priya',
            'emails' => ['priya@example.test'],
            'tags' => ['york'],
        ]);
        Contact::create([
            'team_id' => $team->id,
            'type' => 'vendor',
            'first_name' => 'Excluded',
            'emails' => ['excluded@example.test'],
        ]);

        $campaign = $this->postJson('/api/v1/email-campaigns', [
            'name' => 'York buyers',
            'subject' => 'New Riverside apartment',
            'content' => '<p>A new property matches your search.</p>',
            'audience_filters' => ['types' => ['buyer'], 'tags' => ['york']],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.status', 'draft');
        $campaignId = $campaign->json('data.id');

        $this->getJson("/api/v1/email-campaigns/$campaignId/preview")
            ->assertOk()
            ->assertJsonPath('meta.recipients_count', 1)
            ->assertJsonPath('data.0.first_name', 'Priya');
        $this->postJson("/api/v1/email-campaigns/$campaignId/schedule", [
            'scheduled_at' => now()->addHour()->toIso8601String(),
        ])->assertOk()
            ->assertJsonPath('data.status', 'scheduled')
            ->assertJsonPath('data.recipients_count', 1);
        $this->getJson("/api/v1/email-campaigns/$campaignId/metrics")
            ->assertOk()
            ->assertJsonPath('data.delivery_rate', 0);

        $otherProperty = Property::factory()->for(Team::factory()->create())->create();
        $this->getJson("/api/v1/marketing-assets/properties/{$otherProperty->id}/social")->assertNotFound();
    }

    public function test_property_media_is_private_ordered_and_team_scoped(): void
    {
        Storage::fake('local');
        [, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create();

        $photo = $this->postJson("/api/v1/properties/{$property->id}/media", [
            'file' => UploadedFile::fake()->image('front.webp'),
            'type' => 'image',
            'title' => 'Front elevation',
            'alt_text' => 'Front of the property',
            'is_primary' => true,
            'watermark' => true,
            'metadata' => ['photographer' => 'Agency Studio'],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.type', 'image')
            ->assertJsonPath('data.is_primary', true)
            ->assertJsonPath('data.sort_order', 1)
            ->assertJsonPath('data.metadata.photographer', 'Agency Studio')
            ->assertJsonPath('data.metadata.processing.optimized', true)
            ->assertJsonPath('data.metadata.processing.watermark_applied', true);

        $floorplan = $this->postJson("/api/v1/properties/{$property->id}/media", [
            'file' => UploadedFile::fake()->create('floorplan.pdf', 120, 'application/pdf'),
            'type' => 'floorplan',
            'is_public' => false,
        ])->assertCreated()
            ->assertJsonPath('data.sort_order', 2)
            ->assertJsonPath('data.is_public', false)
            ->assertJsonPath('data.metadata.processing.optimized', false)
            ->assertJsonPath('data.metadata.processing.watermark_applied', false);

        $photoId = $photo->json('data.image_id');
        $floorplanId = $floorplan->json('data.image_id');
        Storage::disk('local')->assertExists($photo->json('data.file_path'));

        $this->putJson("/api/v1/properties/{$property->id}/media/reorder", [
            'media' => [
                ['id' => $floorplanId, 'sort_order' => 0],
                ['id' => $photoId, 'sort_order' => 1],
            ],
        ])->assertOk()
            ->assertJsonPath('data.0.image_id', $floorplanId);

        $this->get("/api/v1/properties/{$property->id}/media/{$floorplanId}/download")
            ->assertOk()
            ->assertDownload('floorplan.pdf');

        $otherProperty = Property::factory()->for(Team::factory()->create())->create();
        $this->getJson("/api/v1/properties/{$otherProperty->id}/media")->assertNotFound();

        $this->deleteJson("/api/v1/properties/{$property->id}/media/{$photoId}")->assertNoContent();
        Storage::disk('local')->assertMissing($photo->json('data.file_path'));
    }

    public function test_property_images_are_resized_and_visibly_watermarked(): void
    {
        Storage::fake('local');
        [, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create();
        OrganisationProfile::create([
            'team_id' => $team->id,
            'agency_name' => 'North Star Estates',
            'operating_countries' => ['GB'],
            'primary_country' => 'GB',
            'currency' => 'GBP',
            'locale' => 'en-GB',
            'language' => 'en',
            'timezone' => 'Europe/London',
            'date_format' => 'd/m/Y',
            'measurement_system' => 'metric',
            'area_unit' => 'sq_m',
        ]);

        $source = UploadedFile::fake()->image('source.jpg', 3000, 2000);
        $original = file_get_contents($source->getRealPath());
        $originalChecksum = hash('sha256', $original);

        $plain = $this->postJson("/api/v1/properties/{$property->id}/media", [
            'file' => UploadedFile::fake()->createWithContent('plain.jpg', $original),
            'type' => 'image',
            'metadata' => ['credit' => 'In-house'],
        ])->assertCreated()
            ->assertJsonPath('data.metadata.credit', 'In-house')
            ->assertJsonPath('data.metadata.processing.original_width', 3000)
            ->assertJsonPath('data.metadata.processing.original_height', 2000)
            ->assertJsonPath('data.metadata.processing.width', 2560)
            ->assertJsonPath('data.metadata.processing.height', 1707)
            ->assertJsonPath('data.metadata.processing.watermark_applied', false);

        $watermarked = $this->postJson("/api/v1/properties/{$property->id}/media", [
            'file' => UploadedFile::fake()->createWithContent('watermarked.jpg', $original),
            'type' => 'image',
            'watermark' => true,
        ])->assertCreated()
            ->assertJsonPath('data.metadata.processing.width', 2560)
            ->assertJsonPath('data.metadata.processing.height', 1707)
            ->assertJsonPath('data.metadata.processing.watermark_applied', true);

        $plainBytes = Storage::disk('local')->get($plain->json('data.file_path'));
        $watermarkedBytes = Storage::disk('local')->get($watermarked->json('data.file_path'));

        $this->assertNotSame($originalChecksum, hash('sha256', $plainBytes));
        $this->assertNotSame(hash('sha256', $plainBytes), hash('sha256', $watermarkedBytes));
        $this->assertSame(
            hash('sha256', $watermarkedBytes),
            $watermarked->json('data.metadata.processing.checksum_sha256'),
        );
        $this->assertSame(strlen($watermarkedBytes), $watermarked->json('data.file_size'));

        $this->patchJson("/api/v1/properties/{$property->id}/media/{$watermarked->json('data.image_id')}", [
            'watermark' => false,
        ])->assertUnprocessable()->assertJsonValidationErrors('watermark');

        $this->postJson("/api/v1/properties/{$property->id}/media", [
            'file' => UploadedFile::fake()->create('watermarked.pdf', 10, 'application/pdf'),
            'type' => 'floorplan',
            'watermark' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors('watermark');
    }

    public function test_staff_profiles_and_departments_are_organisation_scoped(): void
    {
        [, $team] = $this->actingAsTeamMember();
        $branch = $this->postJson('/api/v1/branches', [
            'name' => 'City Office',
        ])->assertCreated();

        $department = $this->postJson('/api/v1/departments', [
            'name' => 'Residential Sales',
            'description' => 'Residential sales and progression',
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id);

        $staffUser = User::factory()->create(['email' => 'negotiator@example.test']);
        $staff = $this->postJson('/api/v1/staff', [
            'email' => $staffUser->email,
            'role' => 'editor',
            'branch_id' => $branch->json('data.id'),
            'department_id' => $department->json('data.id'),
            'job_title' => 'Senior Negotiator',
            'phone' => '+44 20 7946 0018',
            'bio' => 'Specialist in city apartments.',
            'is_public' => true,
        ])->assertCreated()
            ->assertJsonPath('data.id', $staffUser->id)
            ->assertJsonPath('data.role', 'editor')
            ->assertJsonPath('data.branch.name', 'City Office')
            ->assertJsonPath('data.department.name', 'Residential Sales');

        $this->getJson('/api/v1/staff?department_id='.$department->json('data.id'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.job_title', 'Senior Negotiator');

        $this->patchJson("/api/v1/staff/{$staffUser->id}", [
            'job_title' => 'Sales Manager',
            'is_public' => false,
        ])->assertOk()
            ->assertJsonPath('data.job_title', 'Sales Manager')
            ->assertJsonPath('data.is_public', false);

        $otherBranch = Branch::create([
            'team_id' => Team::factory()->create()->id,
            'name' => 'Other Agency',
        ]);
        $this->patchJson("/api/v1/staff/{$staffUser->id}", [
            'branch_id' => $otherBranch->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('branch_id');

        $this->deleteJson("/api/v1/staff/{$staffUser->id}")->assertNoContent();
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $staffUser->id,
        ]);
    }

    public function test_operational_service_integrations_are_secure_and_team_scoped(): void
    {
        [, $team] = $this->actingAsTeamMember();

        $this->getJson('/api/v1/service-integrations/options')
            ->assertOk()
            ->assertJsonFragment([
                'category' => 'calendar',
                'providers' => ['google_calendar', 'microsoft_outlook'],
            ]);

        $primary = $this->postJson('/api/v1/service-integrations', [
            'category' => 'email',
            'provider' => 'smtp',
            'name' => 'Agency SMTP',
            'credentials' => [
                'username' => 'mailer@example.test',
                'password' => 'secret-password',
            ],
            'settings' => ['host' => 'smtp.example.test', 'port' => 587],
            'is_default' => true,
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.provider', 'smtp')
            ->assertJsonPath('data.is_default', true)
            ->assertJsonMissingPath('data.credentials');

        $secondary = $this->postJson('/api/v1/service-integrations', [
            'category' => 'email',
            'provider' => 'gmail',
            'name' => 'Gmail',
            'is_default' => true,
        ])->assertCreated();

        $this->assertDatabaseHas('service_integrations', [
            'id' => $primary->json('data.id'),
            'is_default' => false,
        ]);

        $this->postJson('/api/v1/service-integrations', [
            'category' => 'maps',
            'provider' => 'twilio',
            'name' => 'Invalid pairing',
        ])->assertUnprocessable()->assertJsonValidationErrors('provider');

        $this->postJson("/api/v1/service-integrations/{$secondary->json('data.id')}/check")
            ->assertOk()
            ->assertJsonPath('data.last_check_status', 'configured')
            ->assertJsonMissingPath('data.credentials');

        $otherTeamIntegration = ServiceIntegration::create([
            'team_id' => Team::factory()->create()->id,
            'category' => 'maps',
            'provider' => 'openstreetmap',
            'name' => 'Other agency map',
        ]);
        $this->getJson("/api/v1/service-integrations/{$otherTeamIntegration->id}")->assertNotFound();
    }

    public function test_property_compliance_documents_are_private_verified_and_team_scoped(): void
    {
        Storage::fake('local');
        [$user, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create();

        $item = $this->postJson('/api/v1/compliance-items', [
            'property_id' => $property->id,
            'compliance_type' => 'gas_safety',
            'title' => 'Annual gas safety certificate',
            'required_by_date' => now()->addWeek()->toDateString(),
            'certificate_expiry' => now()->addYear()->toDateString(),
            'renewal_required' => true,
            'priority_level' => 3,
            'risk_level' => 4,
            'assigned_to' => $user->id,
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.status', 'pending');
        $itemId = $item->json('data.id');

        $document = $this->postJson("/api/v1/compliance-items/$itemId/documents", [
            'file' => UploadedFile::fake()->create('gas-certificate.pdf', 100, 'application/pdf'),
            'document_type' => 'certificate',
            'title' => 'Gas Safe certificate',
            'expiry_date' => now()->addYear()->toDateString(),
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.is_verified', false)
            ->assertJsonMissingPath('data.file_path');
        $documentId = $document->json('data.id');

        $this->get("/api/v1/compliance-items/$itemId/documents/$documentId/download")
            ->assertOk()
            ->assertDownload('gas-certificate.pdf');
        $this->patchJson("/api/v1/compliance-items/$itemId/documents/$documentId/verify", [
            'verified' => true,
            'notes' => 'Registration checked.',
        ])->assertOk()
            ->assertJsonPath('data.is_verified', true)
            ->assertJsonPath('data.verified_by', $user->id);

        $this->patchJson("/api/v1/compliance-items/$itemId", [
            'status' => 'completed',
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.completed_date', now()->startOfDay()->toISOString());

        $otherTeam = Team::factory()->create();
        $otherItem = ComplianceItem::create([
            'team_id' => $otherTeam->id,
            'property_id' => Property::factory()->for($otherTeam)->create()->id,
            'compliance_type' => 'epc',
            'title' => 'Other agency EPC',
            'required_by_date' => now()->toDateString(),
        ]);
        $this->getJson("/api/v1/compliance-items/{$otherItem->id}")->assertNotFound();
        $this->getJson("/api/v1/compliance-items/{$otherItem->id}/documents")->assertNotFound();
    }

    public function test_contractor_quotes_and_work_orders_cover_the_maintenance_job_lifecycle(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create();
        $tenant = Tenant::create([
            'team_id' => $team->id,
            'name' => 'Jordan Tenant',
            'email' => 'jordan.tenant@example.test',
            'phone' => '+44 7700 900234',
        ]);

        $contractor = $this->postJson('/api/v1/contractors', [
            'company_name' => 'North Star Plumbing',
            'contact_person' => 'Alex Plumber',
            'email' => 'jobs@north-star-plumbing.test',
            'vendor_type' => 'plumber',
            'specializations' => ['boilers', 'leaks'],
            'insurance_valid_until' => now()->addYear()->toDateString(),
            'preferred_vendor' => true,
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.added_by', $user->id)
            ->assertJsonMissingPath('data.bank_details');
        $contractorId = $contractor->json('data.id');

        $maintenance = $this->postJson('/api/v1/maintenance', [
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'vendor_id' => $contractorId,
            'title' => 'Boiler pressure failure',
            'description' => 'Boiler loses pressure overnight.',
            'requested_date' => now()->toDateString(),
            'priority' => 'high',
        ])->assertCreated();
        $maintenanceId = $maintenance->json('data.id');

        $quote = $this->postJson('/api/v1/contractor-quotes', [
            'vendor_id' => $contractorId,
            'property_id' => $property->id,
            'maintenance_request_id' => $maintenanceId,
            'work_description' => 'Replace pressure relief valve.',
            'quote_amount' => 360,
            'labor_cost' => 180,
            'materials_cost' => 180,
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addMonth()->toDateString(),
            'estimated_duration' => 3,
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.status', 'pending');
        $quoteId = $quote->json('data.id');

        $this->postJson("/api/v1/contractor-quotes/$quoteId/decision", [
            'decision' => 'accepted',
        ])->assertOk()
            ->assertJsonPath('data.status', 'accepted')
            ->assertJsonPath('data.approved_by', $user->id);

        $order = $this->postJson('/api/v1/work-orders', [
            'property_id' => $property->id,
            'maintenance_request_id' => $maintenanceId,
            'vendor_id' => $contractorId,
            'title' => 'Repair boiler pressure valve',
            'description' => 'Complete the accepted quoted work.',
            'work_type' => 'repair',
            'priority' => 3,
            'estimated_cost' => 360,
            'estimated_hours' => 3,
            'assigned_to' => $user->id,
        ])->assertCreated()
            ->assertJsonPath('data.created_by', $user->id);
        $orderId = $order->json('data.id');

        $this->postJson("/api/v1/work-orders/$orderId/updates", [
            'update_type' => 'status_change',
            'status_change' => 'in_progress',
            'description' => 'Engineer has arrived.',
            'progress_percentage' => 10,
        ])->assertCreated();
        $this->postJson("/api/v1/work-orders/$orderId/updates", [
            'update_type' => 'completion',
            'status_change' => 'completed',
            'description' => 'Valve replaced and boiler tested.',
            'progress_percentage' => 100,
            'time_spent' => 2.5,
        ])->assertCreated();

        $this->getJson("/api/v1/work-orders/$orderId")
            ->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.started_date', fn ($date) => $date !== null)
            ->assertJsonPath('data.completed_date', fn ($date) => $date !== null);
        $this->getJson("/api/v1/work-orders/$orderId/updates")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.progress_percentage', 100);

        $otherVendor = Vendor::create([
            'team_id' => Team::factory()->create()->id,
            'added_by' => User::factory()->create()->id,
            'company_name' => 'Other Agency Contractor',
            'vendor_type' => 'electrician',
        ]);
        $this->postJson('/api/v1/work-orders', [
            'property_id' => $property->id,
            'vendor_id' => $otherVendor->id,
            'title' => 'Invalid cross-team job',
            'description' => 'Must be rejected.',
            'work_type' => 'repair',
        ])->assertUnprocessable()->assertJsonValidationErrors('vendor_id');
    }

    public function test_sales_progression_tracks_memorandum_milestones_exchange_and_completion(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create(['status' => 'available']);

        $response = $this->postJson('/api/v1/sales-progressions', [
            'property_id' => $property->id,
            'agent_id' => $user->id,
            'sale_price' => 425000,
            'buyer_solicitor_name' => 'Buyer Legal LLP',
            'seller_solicitor_name' => 'Seller Legal LLP',
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.stage', 'offer_accepted')
            ->assertJsonCount(20, 'data.checklist_items');
        $progressionId = $response->json('data.id');

        $this->patchJson("/api/v1/sales-progressions/$progressionId", [
            'stage' => 'completed',
        ])->assertUnprocessable()->assertJsonValidationErrors('stage');

        $this->postJson("/api/v1/sales-progressions/$progressionId/memorandum", [
            'issued_at' => now()->toIso8601String(),
            'reference' => 'MOS-2026-0042',
            'recipients' => ['buyer-lawyer@example.test', 'seller-lawyer@example.test'],
        ])->assertCreated()
            ->assertJsonPath('data.event_type', 'memorandum_issued')
            ->assertJsonPath('data.recorded_by', $user->id)
            ->assertJsonPath('data.metadata.reference', 'MOS-2026-0042');

        $this->patchJson("/api/v1/sales-progressions/$progressionId/checklist/mortgage_offer", [
            'completed' => true,
            'notes' => 'Formal mortgage offer received.',
        ])->assertOk()
            ->assertJsonPath('completion_percentage', 10);

        $this->patchJson("/api/v1/sales-progressions/$progressionId/stage", [
            'stage' => 'exchanged',
        ])->assertUnprocessable()->assertJsonValidationErrors('effective_date');

        $exchangeDate = now()->addWeek()->toDateString();
        $this->patchJson("/api/v1/sales-progressions/$progressionId/stage", [
            'stage' => 'exchanged',
            'effective_date' => $exchangeDate,
        ])->assertOk()
            ->assertJsonPath('data.stage', 'exchanged');
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => 'exchanged',
        ]);

        $completionDate = now()->addMonth()->toDateString();
        $this->patchJson("/api/v1/sales-progressions/$progressionId/stage", [
            'stage' => 'completed',
            'effective_date' => $completionDate,
            'notes' => 'Completion confirmed and keys released.',
        ])->assertOk()
            ->assertJsonPath('data.stage', 'completed');
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => 'sold',
            'sold_date' => $completionDate.' 00:00:00',
        ]);

        $this->getJson("/api/v1/sales-progressions/$progressionId/timeline")
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('data.0.event_type', 'stage_changed')
            ->assertJsonPath('data.0.to_stage', 'completed');

        $otherProperty = Property::factory()->for(Team::factory()->create())->create();
        $this->postJson('/api/v1/sales-progressions', [
            'property_id' => $otherProperty->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('property_id');
    }

    public function test_rental_applications_cover_referencing_guarantors_and_decisions(): void
    {
        [$user, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create(['status' => 'to_let']);
        $applicant = Contact::create([
            'team_id' => $team->id,
            'type' => 'applicant',
            'first_name' => 'Morgan',
            'last_name' => 'Renter',
            'emails' => ['morgan@example.test'],
        ]);

        $response = $this->postJson('/api/v1/rental-applications', [
            'property_id' => $property->id,
            'applicant_id' => $applicant->id,
            'employment_status' => 'employed',
            'annual_income' => 72000,
            'monthly_income' => 6000,
            'desired_move_in_date' => now()->addMonth()->toDateString(),
            'screening_consent_at' => now()->toIso8601String(),
            'guarantors' => [[
                'name' => 'Taylor Guarantor',
                'email' => 'taylor@example.test',
                'relationship' => 'parent',
                'annual_income' => 85000,
                'consent_received' => true,
            ]],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.status', 'submitted')
            ->assertJsonPath('data.guarantors.0.name', 'Taylor Guarantor');
        $applicationId = $response->json('data.id');

        $this->patchJson("/api/v1/rental-applications/$applicationId", [
            'status' => 'approved',
        ])->assertUnprocessable()->assertJsonValidationErrors('status');

        $this->patchJson("/api/v1/rental-applications/$applicationId/screening", [
            'background_check_status' => 'passed',
            'credit_report_status' => 'good',
            'rental_history_status' => 'satisfactory',
            'affordability_status' => 'passed',
            'right_to_rent_status' => 'verified',
            'employer_reference' => [
                'status' => 'verified',
                'referee_name' => 'Payroll Manager',
                'referee_email' => 'payroll@example.test',
            ],
            'landlord_reference' => [
                'status' => 'verified',
                'referee_name' => 'Previous Landlord',
            ],
        ])->assertOk()
            ->assertJsonPath('screening_complete', true)
            ->assertJsonPath('screening_passed', true);

        $this->postJson("/api/v1/rental-applications/$applicationId/decision", [
            'decision' => 'approved',
            'notes' => 'All references verified.',
        ])->assertOk()
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.decided_by', $user->id)
            ->assertJsonPath('data.decision_notes', 'All references verified.');

        $this->patchJson("/api/v1/rental-applications/$applicationId/screening", [
            'credit_report_status' => 'poor',
        ])->assertUnprocessable()->assertJsonValidationErrors('status');

        $otherApplicant = Contact::create([
            'team_id' => Team::factory()->create()->id,
            'type' => 'applicant',
            'first_name' => 'Other',
        ]);
        $this->postJson('/api/v1/rental-applications', [
            'property_id' => $property->id,
            'applicant_id' => $otherApplicant->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('applicant_id');
    }

    public function test_viewings_support_attendees_messages_feedback_and_outcomes(): void
    {
        Mail::fake();
        [$user, $team] = $this->actingAsTeamMember();
        $property = Property::factory()->for($team)->create(['title' => '12 Garden Street']);

        $response = $this->postJson('/api/v1/viewings', [
            'property_id' => $property->id,
            'staff_id' => $user->id,
            'appointment_date' => now()->addDay()->toIso8601String(),
            'attendees' => [
                ['name' => 'Avery Buyer', 'email' => 'avery@example.test'],
                ['name' => 'Jordan Buyer', 'email' => 'jordan@example.test'],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonCount(2, 'data.attendees');
        $viewingId = $response->json('data.appointment_id');

        $this->getJson('/api/v1/viewings')
            ->assertOk()
            ->assertJsonPath('data.0.appointment_id', $viewingId);

        $this->postJson("/api/v1/viewings/$viewingId/confirmation")
            ->assertOk()
            ->assertJsonPath('data.status', 'scheduled')
            ->assertJsonPath('data.confirmation_sent_at', fn ($value) => $value !== null);
        $this->postJson("/api/v1/viewings/$viewingId/reminder")
            ->assertOk()
            ->assertJsonPath('data.reminder_sent_at', fn ($value) => $value !== null);

        $feedbackResponse = $this->postJson("/api/v1/viewings/$viewingId/feedback", [
            'attendees' => [
                ['name' => 'Avery Buyer', 'email' => 'avery@example.test'],
            ],
        ])->assertCreated()
            ->assertJsonMissingPath('data.0.token');
        $feedbackId = $feedbackResponse->json('data.0.id');

        $this->patchJson("/api/v1/viewings/$viewingId/feedback/$feedbackId", [
            'overall_rating' => 5,
            'interest_level' => 'very_interested',
            'would_make_offer' => true,
            'offer_price' => 425000,
            'general_comments' => 'Ready to proceed.',
        ])->assertOk()
            ->assertJsonPath('data.overall_rating', 5)
            ->assertJsonPath('data.interest_level', 'very_interested');

        $this->patchJson("/api/v1/viewings/$viewingId", [
            'status' => 'completed',
            'outcome' => 'offer_expected',
            'outcome_notes' => 'Applicant is arranging finance.',
        ])->assertOk()
            ->assertJsonPath('data.outcome', 'offer_expected');

        $otherViewing = Appointment::create([
            'team_id' => Team::factory()->create()->id,
            'user_id' => $user->id,
            'property_id' => $property->id,
            'appointment_date' => now()->addDay(),
        ]);
        $this->getJson("/api/v1/viewings/{$otherViewing->getKey()}/feedback")
            ->assertNotFound();
    }

    private function actingAsTeamMember(): array
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $team->users()->attach($user, ['role' => 'admin']);
        $user->forceFill(['current_team_id' => $team->id])->save();
        Sanctum::actingAs($user);

        return [$user, $team];
    }
}
