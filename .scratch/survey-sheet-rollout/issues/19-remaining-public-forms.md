# 19 — The remaining public forms

**What to build:** The four public forms that are not the contact form —
viewing booking, valuation request, rental application and tenancy application
— brought to the same standard, which the contact form now sets.

Each is its own Livewire component with its own validation rules and its own
copy, so they need working through one at a time rather than restyled in bulk.

Check each for the fault the contact form had: fields the form collects that
the controller never validates, and therefore never stores. That one silently
threw away every phone number a visitor left.

**Blocked by:** 09

**Status:** done for the viewing booking; the rest reassessed

The lead in this ticket paid off, and the booking form was worse than the
contact form had been.

Booking a viewing has never worked. `bookViewing()` validated the chosen date
against `Property::getAvailableDates()`, which does not exist — the only method
of that name is private, on a different component. Every submission threw
`BadMethodCallException`, the catch turned it into "an unexpected error
occurred", and no viewing was ever booked. Nothing had exercised it because the
route parameter was broken until ticket 12, so the page could not be reached at
all. This is the primary action on every card and on the property detail page.

Two more in the same method: a missing `staff` role threw `RoleDoesNotExist`
rather than leaving the booking unassigned, and the email was validated,
collected, and then never passed to `Booking::create()` — there was no column
for it either. For a guest, who has no account, that address is the only way to
be reached, and the notification only fired for a logged-in user. Both fixed:
the email is stored and a confirmation is sent, and a mail failure is logged
rather than losing the booking with it.

The form itself is on the system now, with errors that name the fix.

REASSESSED for the other three:

- Rental application persists everything it collects. No fault of this kind.
- Valuation stores nothing — it is a display component, and belongs to ticket 23.
- Tenancy application has no form to fix; it redirects to contact until one is
  built, which is what ticket 19 originally described and remains open.

Clears the `book` and `search` entries from ticket 24: white on `bg-blue-500`
is 3.8:1, under AA for a button label.

- [ ] Every field a form collects is validated and persisted, or removed from the form
- [ ] Every input has a real label; placeholders are examples, never names
- [ ] Errors name the fix in the interface's voice, and are wired to their field
- [ ] What the visitor typed survives a rejected submission
- [ ] Submit names the action and the confirmation reuses the verb
- [ ] Every form renders in both themes and at 390px, with 44px touch targets
