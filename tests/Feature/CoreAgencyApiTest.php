<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Property;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertJsonPath('data.measurement_system', 'metric');

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

        $otherTeam = Team::factory()->create();
        Company::create([
            'team_id' => $otherTeam->id,
            'name' => 'Orchard Hidden Company',
        ]);

        $this->getJson('/api/v1/search?q=Orchard&types[]=properties&types[]=contacts&types[]=companies')
            ->assertOk()
            ->assertJsonPath('meta.total', 3)
            ->assertJsonCount(1, 'data.properties')
            ->assertJsonCount(1, 'data.contacts')
            ->assertJsonCount(1, 'data.companies')
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
