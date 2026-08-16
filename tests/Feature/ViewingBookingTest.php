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

    /**
     * A slot that has already passed is not a slot. Nothing compared against
     * the clock, and the date rule is only after_or_equal:today, so someone
     * arriving at 18:00 was offered 09:00 this morning — and the booking, the
     * calendar links and the confirmation email were all created for it.
     */
    public function test_an_hour_that_has_already_passed_is_not_offered(): void
    {
        $this->travelTo(now()->setTime(14, 30));

        $today = now()->format('Y-m-d');
        $slots = $this->property->availableViewingSlots($today);

        $this->assertNotContains('09:00', $slots, 'this morning is still being offered this afternoon');
        $this->assertNotContains('14:00', $slots, 'the hour already under way is still being offered');
        $this->assertContains('15:00', $slots);
    }

    public function test_a_day_whose_hours_have_all_passed_drops_out(): void
    {
        $this->travelTo(now()->setTime(23, 0));

        $this->assertNotContains(
            now()->format('Y-m-d'),
            $this->property->availableViewingDates(),
            'today is still offered at 11pm'
        );
        $this->assertContains(now()->addDay()->format('Y-m-d'), $this->property->availableViewingDates());
    }

    /**
     * Two visitors submitting the same slot at once both passed the check
     * before either insert landed. The database has to be the arbiter.
     */
    public function test_the_same_slot_cannot_be_taken_twice(): void
    {
        $day = now()->addDays(3)->format('Y-m-d');

        $this->book($this->property, $day, '14:00');

        $this->expectException(\Illuminate\Database\QueryException::class);

        $this->book($this->property, $day, '14:00');
    }

    public function test_a_cancelled_slot_can_be_taken_again(): void
    {
        $day = now()->addDays(3)->format('Y-m-d');

        $this->book($this->property, $day, '14:00', 'cancelled');
        $this->book($this->property, $day, '14:00');

        $this->assertSame(2, Booking::count());
    }

    /**
     * The panel says "Viewing booked" and reads its text from $confirmation.
     * The flag was set before the broadcast and the notifications, so a throw
     * in either left the panel showing a heading over an empty line, with the
     * error rendered only in the branch the panel had replaced.
     */
    public function test_the_confirmation_panel_is_never_shown_empty(): void
    {
        Mail::fake();

        $component = $this->form()->call('bookViewing');

        $this->assertTrue($component->get('bookingConfirmed'));
        $this->assertNotEmpty($component->get('confirmation'));
    }

    /**
     * Booking casts time through a Carbon accessor, so printing it raw gave
     * "Time: 2026-08-15 14:00:00" — today's date, under a line already naming
     * the viewing's date, in every confirmation email sent.
     */
    public function test_the_confirmation_email_states_the_hour_not_a_timestamp(): void
    {
        Mail::fake();

        $this->form()->call('bookViewing')->assertHasNoErrors();

        Mail::assertSent(ViewingBooked::class, function (ViewingBooked $mail) {
            $body = $mail->render();

            // Any timestamp at all: the markdown renders "**Time:**" as a
            // tag, so anchoring on the label matched nothing and the check
            // passed while the bug was still there.
            $this->assertDoesNotMatchRegularExpression(
                '/\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}/',
                $body,
                'the email prints a timestamp where an hour belongs'
            );

            return true;
        });
    }

    /**
     * The booking is committed before any of this runs. A broadcaster that is
     * not configured must not tell someone their viewing failed when the row
     * is in the diary.
     */
    public function test_a_failing_broadcast_does_not_unbook_a_booked_viewing(): void
    {
        Mail::fake();
        \Illuminate\Support\Facades\Event::listen(\App\Events\BookingCreated::class, function () {
            throw new \RuntimeException('broadcaster is down');
        });

        $component = $this->form()->call('bookViewing');

        $this->assertSame(1, Booking::count(), 'the viewing was not kept');
        $this->assertTrue($component->get('bookingConfirmed'), 'a booked viewing was reported as failed');
    }

    /**
     * A flash set in a Livewire action survives to the next full page load, so
     * a failed booking greeted the visitor again on whatever page they opened.
     */
    public function test_a_failure_does_not_follow_the_visitor_off_the_page(): void
    {
        $component = Livewire::test(PropertyBooking::class, ['propertyId' => $this->property->id])
            ->set('selectedDate', now()->addDays(2)->format('Y-m-d'))
            ->set('selectedTime', '03:00')
            ->set('userName', 'Alex Whitmore')
            ->set('userContact', '07700 900123')
            ->call('bookViewing');

        $this->assertNull(session('error'), 'the failure was flashed and will leak onto the next page');
        $this->assertNotEmpty($component->get('failure'), 'the visitor was told nothing');
    }

    /**
     * The .ics route sits behind auth, and this form is written for guests.
     * Offering it sends them to a login screen from a confirmation page.
     */
    public function test_a_guest_is_not_offered_a_link_they_cannot_follow(): void
    {
        Mail::fake();

        $this->form()->call('bookViewing')->assertDontSee(__('Add to Apple Calendar'));
    }

    /**
     * A booking written at an hour outside the nine on offer — the staff panel
     * and VisitBookingService can both do it — must not subtract from the count
     * of hours that are.
     */
    public function test_a_booking_outside_the_offered_hours_does_not_close_the_day(): void
    {
        $day = now()->addDays(3)->format('Y-m-d');

        foreach (['07:00', '08:00', '19:00', '20:00', '21:00', '22:00', '23:00', '06:00', '05:00'] as $hour) {
            $this->book($this->property, $day, $hour);
        }

        $this->assertContains(
            $day,
            $this->property->fresh()->availableViewingDates(),
            'off-grid bookings closed a day whose nine viewing hours are all free'
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

    /**
     * The unique index on the slot is the last word on who gets an hour, but a
     * reschedule went straight to save() — so moving onto an hour someone else
     * already holds threw a QueryException and 500'd the page instead of
     * telling the customer to pick another.
     */
    public function test_rescheduling_onto_a_taken_slot_is_refused_not_a_500(): void
    {
        $date = now()->addWeek()->format('Y-m-d');
        $taken = $this->book($this->property, $date, '14:00');
        $mine = $this->book($this->property, $date, '15:00');

        try {
            $mine->reschedule($date, '14:00');
            $this->fail('rescheduling onto a taken slot was allowed');
        } catch (\App\Exceptions\SlotAlreadyBooked $e) {
            $this->assertStringContainsString('another', strtolower($e->getMessage()));
        }

        $this->assertSame('14:00', $taken->fresh()->time->format('H:i'));
        $this->assertSame('15:00', $mine->fresh()->time->format('H:i'), 'the losing booking kept its own hour');
    }

    public function test_rescheduling_onto_a_free_slot_still_works(): void
    {
        $date = now()->addWeek()->format('Y-m-d');
        $booking = $this->book($this->property, $date, '15:00');

        $this->assertTrue($booking->reschedule($date, '16:00'));
        $this->assertSame('16:00', $booking->fresh()->time->format('H:i'));
    }

    /**
     * `selectedDate` is bound to an input, so anything can arrive in it. The
     * hook parsed it with Carbon before any rule had run, and an unparseable
     * string took the whole component down with a 500.
     */
    public function test_a_date_that_is_not_a_date_does_not_take_the_page_down(): void
    {
        $component = Livewire::test(PropertyBooking::class, ['propertyId' => $this->property->id])
            ->set('selectedDate', 'not-a-date');

        $component->assertHasErrors('selectedDate');
        $this->assertSame([], $component->get('availableTimeSlots'));
    }

    public function test_a_date_in_the_past_offers_no_hours(): void
    {
        $component = Livewire::test(PropertyBooking::class, ['propertyId' => $this->property->id])
            ->set('selectedDate', now()->subWeek()->format('Y-m-d'));

        $component->assertHasErrors('selectedDate');
        $this->assertSame([], $component->get('availableTimeSlots'));
    }

    /**
     * The model has no business knowing what a form calls its field. It threw a
     * ValidationException keyed to `time`, and no caller has a field by that
     * name — the staff panel binds `new_time` — so the error attached to
     * nothing: the admin pressed Reschedule and the dialog sat there with no
     * change and no reason given.
     *
     * (The other caller, a Livewire ManageBooking component, had no view, no
     * route and no reference anywhere. It is gone.)
     */
    public function test_the_staff_panel_reports_the_clash_rather_than_swallowing_it(): void
    {
        $source = file_get_contents(app_path('Filament/Resources/BookingResource.php'));

        $this->assertStringContainsString('SlotAlreadyBooked', $source, 'the reschedule action ignores the clash');
        $this->assertStringContainsString('->halt()', $source, 'a failed reschedule must not report success');
    }
}
