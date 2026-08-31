<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Parties\Application\CreateContactMessage;
use Liberu\RealEstate\Parties\Models\Contact;
use Liberu\RealEstate\Parties\Models\ContactMessage;
use Liberu\RealEstate\PartiesLivewire\Components\ContactEnquiryForm;
use Livewire\Livewire;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

beforeEach(function (): void {
    Livewire::component('test-contact-enquiry-form', ContactEnquiryForm::class);
    Livewire::component('contact-enquiry-form', ContactEnquiryForm::class);
});

it('stores validated public enquiries and exposes the contact page', function (): void {
    expect(Schema::hasTable('real_estate_contacts'))->toBeTrue()->and(Schema::hasTable('real_estate_contact_messages'))->toBeTrue();

    Livewire::test('test-contact-enquiry-form')
        ->set('name', 'Taylor Applicant')
        ->set('email', 'taylor@example.com')
        ->set('interest', 'renting')
        ->set('message', 'I would like to arrange a viewing.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    expect(ContactMessage::query()->where('email', 'taylor@example.com')->exists())->toBeTrue();
    $this->get('/contact')->assertOk()->assertSee('Contact us')->assertSee('Send message');
});

it('accepts the legacy-compatible public contact POST endpoint', function (): void {
    $this->post('/contact', ['name' => 'Morgan Seller', 'email' => 'morgan@example.com', 'interest' => 'selling', 'message' => 'Please call me.'])
        ->assertRedirect(route('contact.show'));
    expect(ContactMessage::query()->where('email', 'morgan@example.com')->exists())->toBeTrue();
});

it('rejects invalid public enquiry data', function (): void {
    expect(fn (): ContactMessage => app(CreateContactMessage::class)->handle(['name' => 'A', 'email' => 'not-an-email', 'interest' => 'unknown', 'message' => '']))
        ->toThrow(ValidationException::class);
    expect(ContactMessage::query()->count())->toBe(0);
});

it('provides team-scoped contact CRUD through the API', function (): void {
    $user = User::factory()->create(['current_team_id' => 21]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/contacts', ['first_name' => 'Jordan', 'last_name' => 'Buyer', 'emails' => ['jordan@example.com']]);
    $create->assertCreated()->assertJsonPath('data.first_name', 'Jordan');
    $id = $create->json('data.id');

    $this->actingAs($user, 'sanctum')->patchJson('/api/v1/real-estate/contacts/'.$id, ['status' => 'inactive'])->assertOk()->assertJsonPath('data.status', 'inactive');
    $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/real-estate/contacts/'.$id)->assertNoContent();

    $other = User::factory()->create(['current_team_id' => 22]);
    $contact = Contact::query()->create(['team_id' => 21, 'first_name' => 'Private']);
    $this->actingAs($other, 'sanctum')->getJson('/api/v1/real-estate/contacts/'.$contact->getKey())->assertNotFound();
});
