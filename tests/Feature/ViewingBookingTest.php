<?php

namespace Tests\Feature;

use App\Livewire\PropertyBooking;
use App\Mail\ViewingBooked;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ticket 19 of the Survey Sheet rollout: booking a viewing.
 *
 * This is the primary action on every card and on the property detail page, so
 * it is the form most likely to be the first thing a visitor ever submits.
 */
class ViewingBookingTest extends TestCase
{
    use RefreshDatabase;

    private Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->property = Property::factory()->create([
            'title' => 'Alexandra Road, Reading RG1',
            'status' => 'For Sale',
        ]);
    }

    private function form(): \Livewire\Features\SupportTesting\Testable
    {
        $component = Livewire::test(PropertyBooking::class, ['propertyId' => $this->property->id]);
        $date = $component->get('availableDates')[0] ?? now()->addWeek()->format('Y-m-d');

        // Setting the date populates availableTimeSlots through the component's
        // own updated hook, which is how a visitor gets there too.
        $component->set('selectedDate', $date);

        return $component
            ->set('selectedTime', $component->get('availableTimeSlots')[0] ?? '10:00')
            ->set('userName', 'Alex Whitmore')
            ->set('userEmail', 'alex@example.com')
            ->set('userContact', '07700 900123');
    }

    /**
     * The email was validated, collected, and then never passed to
     * Booking::create() — and there was no column for it either. For a guest
     * booking, who has no account, it is the only way to be reached.
     */
    public function test_the_email_a_visitor_leaves_is_kept(): void
    {
        Mail::fake();

        $this->form()->call('bookViewing')->assertHasNoErrors();

        $booking = Booking::sole();

        $this->assertSame('alex@example.com', $booking->email, 'the email was discarded');
        $this->assertSame('Alex Whitmore', $booking->name);
        $this->assertSame('07700 900123', $booking->contact);
    }

    /**
     * A guest booking notified nobody: the notification only fired for a
     * logged-in user, so someone booking without an account got a confirmation
     * on screen and nothing else.
     */
    public function test_a_guest_who_books_is_sent_a_confirmation(): void
    {
        Mail::fake();

        $this->form()->call('bookViewing')->assertHasNoErrors();

        Mail::assertSent(ViewingBooked::class, fn (ViewingBooked $mail) => $mail->hasTo('alex@example.com'));
    }

    public function test_a_booking_without_an_email_still_succeeds(): void
    {
        Mail::fake();

        $this->form()->set('userEmail', null)->call('bookViewing')->assertHasNoErrors();

        $this->assertSame(1, Booking::count());
        Mail::assertNothingSent();
    }

    /**
     * The control names the action and the confirmation reuses the verb.
     */
    /**
     * Booking casts `date`, so pluck() returns Carbon objects whose string form
     * is 'Y-m-d H:i:s'. Comparing those against 'Y-m-d' never matched, so a day
     * already booked was still offered — in the picker and in the check meant
     * to catch it on submission.
     */
    public function test_a_booked_day_is_no_longer_offered(): void
    {
        Mail::fake();

        $this->form()->call('bookViewing')->assertHasNoErrors();

        $taken = Booking::sole()->date->format('Y-m-d');

        $this->assertNotContains(
            $taken,
            $this->property->fresh()->getAvailableDatesForTeam(),
            'a day with a viewing on it is still being offered'
        );
    }

    /**
     * A property with no team must not have its availability driven by every
     * teamless booking on the platform: where('team_id', null) becomes
     * whereNull and matches all of them.
     */
    public function test_a_teamless_property_is_not_blanked_by_other_bookings(): void
    {
        $mine = Property::factory()->create(['team_id' => null, 'status' => 'For Sale']);
        $theirs = Property::factory()->create(['team_id' => null, 'status' => 'For Sale']);

        Booking::create([
            'property_id' => $theirs->id,
            'date' => now()->addDays(3)->format('Y-m-d'),
            'time' => '10:00',
            'status' => 'confirmed',
            'name' => 'Someone else',
            'contact' => '07700 900999',
        ]);

        $this->assertContains(
            now()->addDays(3)->format('Y-m-d'),
            $mine->getAvailableDatesForTeam(),
            "another property's booking should not blank this one"
        );
    }

    public function test_the_confirmation_reuses_the_verb(): void
    {
        Mail::fake();

        $html = Livewire::test(PropertyBooking::class, ['propertyId' => $this->property->id])->html();
        $this->assertStringContainsString('Book a viewing', $html);

        $this->form()->call('bookViewing')->assertSee('Viewing booked');
    }

    public function test_an_error_names_the_fix_in_the_interface_voice(): void
    {
        $component = Livewire::test(PropertyBooking::class, ['propertyId' => $this->property->id])
            ->set('userEmail', 'alex@')
            ->call('bookViewing');

        $component->assertHasErrors('userEmail');

        $message = $component->errors()->first('userEmail');

        $this->assertStringNotContainsStringIgnoringCase('invalid', $message);
        $this->assertStringNotContainsStringIgnoringCase('oops', $message);
        $this->assertStringContainsString('@', $message);
    }

    public function test_every_field_has_a_real_label(): void
    {
        $html = Livewire::test(PropertyBooking::class, ['propertyId' => $this->property->id])->html();

        preg_match_all('/<(?:input|select|textarea)\b[^>]*\bid="([^"]+)"/', $html, $controls);

        $this->assertNotEmpty($controls[1]);

        foreach ($controls[1] as $id) {
            $this->assertMatchesRegularExpression(
                '/<label[^>]*for="'.preg_quote($id, '/').'"/',
                $html,
                "[{$id}] has no label"
            );
        }
    }

    public function test_the_form_uses_the_system(): void
    {
        $source = file_get_contents(resource_path('views/livewire/property-booking.blade.php'));

        $this->assertStringNotContainsString('bg-white', $source);
        $this->assertStringNotContainsString('text-gray-', $source);
        $this->assertStringNotContainsString('bg-indigo-', $source);
    }
}
