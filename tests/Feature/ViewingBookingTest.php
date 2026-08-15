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
    private function book(Property $property, string $date, string $time, string $status = 'confirmed'): Booking
    {
        return Booking::create([
            'property_id' => $property->id,
            'team_id' => $property->team_id,
            'date' => $date,
            'time' => $time,
            'status' => $status,
            'name' => 'Someone else',
            'contact' => '07700 900999',
        ]);
    }

    /**
     * Booking casts `date`, so pluck() returns Carbon objects whose string form
     * is 'Y-m-d H:i:s'. Comparing those against 'Y-m-d' never matched, so a slot
     * already taken was still offered — in the picker and in the check meant to
     * catch it on submission.
     */
    public function test_a_taken_slot_is_no_longer_offered(): void
    {
        $day = now()->addDays(3)->format('Y-m-d');

        $this->book($this->property, $day, '09:00');

        $this->assertNotContains(
            '09:00',
            $this->property->fresh()->availableViewingSlots($day),
            'a slot with a viewing in it is still being offered'
        );
    }

    /**
     * A viewing at 09:00 does not consume the day. Nine slots are offered
     * between 09:00 and 17:00, and blanking the date on the first booking made
     * every one of the other eight unreachable.
     */
    public function test_one_viewing_does_not_close_the_whole_day(): void
    {
        $day = now()->addDays(3)->format('Y-m-d');

        $this->book($this->property, $day, '09:00');

        $property = $this->property->fresh();

        $this->assertContains($day, $property->availableViewingDates(), 'one viewing closed the whole day');
        $this->assertCount(8, $property->availableViewingSlots($day));
    }

    public function test_a_day_with_every_slot_taken_drops_out(): void
    {
        $day = now()->addDays(3)->format('Y-m-d');

        foreach (Property::VIEWING_SLOTS as $slot) {
            $this->book($this->property, $day, $slot);
        }

        $this->assertNotContains(
            $day,
            $this->property->fresh()->availableViewingDates(),
            'a day with no slot left is still being offered'
        );
    }

    /**
     * Cancelling gives the slot back. The date picker ignored status entirely,
     * so a cancelled viewing held its slot for good while the time picker —
     * which does filter — disagreed with it.
     */
    public function test_cancelling_a_viewing_frees_the_slot(): void
    {
        $day = now()->addDays(3)->format('Y-m-d');

        $this->book($this->property, $day, '09:00', 'cancelled');

        $this->assertContains('09:00', $this->property->fresh()->availableViewingSlots($day));
    }

    /**
     * Availability is the property's own. One agency taking a viewing on one
     * of its homes used to close that day across every home on its books.
     */
    public function test_another_property_on_the_team_keeps_its_own_diary(): void
    {
        $team = $this->property->team_id;
        $other = Property::factory()->create(['team_id' => $team, 'status' => 'For Sale']);
        $day = now()->addDays(3)->format('Y-m-d');

        foreach (Property::VIEWING_SLOTS as $slot) {
            $this->book($this->property, $day, $slot);
        }

        $this->assertContains(
            $day,
            $other->fresh()->availableViewingDates(),
            "a booking on one home closed the day on another"
        );
    }

    /**
     * The confirmation used to be flashed to the session, so it survived into
     * the next full page load: book a viewing, open the valuation page, and it
     * greeted you with the viewing confirmation.
     */
    public function test_the_confirmation_does_not_follow_the_visitor_off_the_page(): void
    {
        Mail::fake();

        $this->form()->call('bookViewing')->assertHasNoErrors();

        $this->assertNull(session('message'), 'the confirmation was flashed and will leak onto the next page');
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
