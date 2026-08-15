<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 09 of the Survey Sheet rollout: the public forms.
 *
 * Labels are real, errors name the fix, and nothing the visitor types is
 * quietly discarded.
 */
class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Alex Whitmore',
            'email' => 'alex@example.com',
            'phone' => '07700 900123',
            'interest' => 'buying',
            'message' => 'Is the garden south facing?',
        ], $overrides);
    }

    /**
     * The form asked for a phone number and an interest, the controller
     * validated neither, and the model could not store the second at all — so
     * someone asking for a callback had their number thrown away.
     */
    public function test_everything_the_form_asks_for_is_kept(): void
    {
        $this->post(route('contact.submit'), $this->payload())->assertRedirect();

        $message = ContactMessage::sole();

        $this->assertSame('Alex Whitmore', $message->name);
        $this->assertSame('alex@example.com', $message->email);
        $this->assertSame('07700 900123', $message->phone, 'the phone number was discarded');
        $this->assertSame('buying', $message->interest, 'the interest was discarded');
        $this->assertSame('Is the garden south facing?', $message->message);
    }

    /**
     * "Ask a question" on a listing card sends the visitor here with the
     * property in the query. Losing it means the agent cannot tell which home
     * the question is about.
     */
    public function test_a_question_about_a_property_keeps_the_property(): void
    {
        $property = Property::factory()->create(['title' => 'Alexandra Road']);

        $page = $this->get(route('contact.show', ['property' => $property->id]))->assertOk();
        $page->assertSee('Alexandra Road');

        $this->post(route('contact.submit'), $this->payload(['property_id' => $property->id]))
            ->assertRedirect();

        $this->assertSame($property->id, ContactMessage::sole()->property_id);
    }

    public function test_an_unknown_property_is_ignored_rather_than_stored(): void
    {
        $this->post(route('contact.submit'), $this->payload(['property_id' => 999999]))
            ->assertSessionHasErrors('property_id');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_every_field_has_a_real_label(): void
    {
        $html = $this->get(route('contact.show'))->assertOk()->getContent();

        preg_match_all('/<(?:input|select|textarea)\b[^>]*\bid="([^"]+)"/', $html, $controls);

        $this->assertNotEmpty($controls[1], 'the form should have controls');

        foreach ($controls[1] as $id) {
            if (str_starts_with($id, 'home-')) {
                continue; // the navbar search, not this form
            }

            $this->assertMatchesRegularExpression(
                '/<label[^>]*for="'.preg_quote($id, '/').'"/',
                $html,
                "[{$id}] has no label; a placeholder is an example, never a name"
            );
        }
    }

    /**
     * Errors say what happened and what to do about it, in the interface's
     * voice — no "Oops", no "invalid", no apology.
     */
    public function test_an_error_names_the_fix_in_the_interface_voice(): void
    {
        $response = $this->from(route('contact.show'))
            ->post(route('contact.submit'), $this->payload(['email' => 'alex@']));

        $response->assertSessionHasErrors('email');

        $message = session('errors')->first('email');

        $this->assertStringNotContainsStringIgnoringCase('oops', $message);
        $this->assertStringNotContainsStringIgnoringCase('invalid', $message);
        $this->assertStringNotContainsStringIgnoringCase('sorry', $message);
        $this->assertMatchesRegularExpression('/@/', $message, 'the message should name the fix');
    }

    public function test_an_error_is_wired_to_its_field(): void
    {
        $this->from(route('contact.show'))
            ->post(route('contact.submit'), $this->payload(['email' => 'alex@']))
            ->assertRedirect(route('contact.show'));

        $html = $this->get(route('contact.show'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/id="email"[^>]*aria-invalid="true"/', $html);
        $this->assertMatchesRegularExpression('/aria-describedby="[^"]*email-error/', $html);
        $this->assertStringContainsString('id="email-error"', $html);
    }

    /**
     * The control names the action, and the confirmation reuses the verb.
     */
    public function test_the_confirmation_reuses_the_verb(): void
    {
        $html = $this->get(route('contact.show'))->assertOk()->getContent();
        $this->assertStringContainsString('Send message', $html);

        $this->post(route('contact.submit'), $this->payload())
            ->assertRedirect(route('contact.show'));

        $confirmed = $this->get(route('contact.show'))->assertOk()->getContent();

        $this->assertStringContainsString('Message sent', $confirmed);
    }

    public function test_what_the_visitor_typed_survives_a_failed_submission(): void
    {
        $this->from(route('contact.show'))
            ->post(route('contact.submit'), $this->payload(['email' => 'alex@']))
            ->assertRedirect(route('contact.show'));

        $html = $this->get(route('contact.show'))->assertOk()->getContent();

        $this->assertStringContainsString('Alex Whitmore', $html, 'a rejected form must not empty itself');
        $this->assertStringContainsString('Is the garden south facing?', $html);
    }
}
