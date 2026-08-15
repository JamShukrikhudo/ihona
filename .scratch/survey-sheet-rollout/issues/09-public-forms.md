# 09 — Public forms

**What to build:** Every form a visitor can reach without an account — enquiry, viewing booking, valuation request, rental application, tenancy application — in the system and behaving the same way.

Labels are mono annotations above the field, the way a drawing labels a dimension. Placeholders are examples, never labels. Errors say what happened and what to do about it, in the interface's voice: no "Oops", no "Invalid input", no apology. Validation fires on blur and clears on input, so nobody is told they are wrong mid-word.

**Blocked by:** 04

**Status:** partly done — the contact form; the rest is ticket 19

The contact form is done end to end and is the pattern the others follow:
labelled fields, errors that name the fix, old input preserved on rejection,
and a confirmation that reuses the verb ("Send message" then "Message sent").

It was also losing data. The form asked for a phone number and an interest;
the controller validated neither, so both were dropped on every submission —
someone asking for a callback had their number thrown away, and `interest` had
no column at all. Both are stored now, along with the property a question came
from, so "Ask a question" on a card arrives with context instead of an
orphaned enquiry.

Found while measuring: at the default size a button rendered 38px tall next to
46px fields, under the 44px touch floor this system documents. Every button
now carries that floor on a coarse pointer.

NOT done, moved to ticket 19: the viewing booking, valuation request, rental
application and tenancy application forms. Each is its own Livewire component
with its own validation, and they need the same pass.

- [ ] Every input has a real label; no form relies on a placeholder to name a field
- [ ] Errors name the fix and are wired to their field for screen readers
- [ ] Validation fires on blur and clears on input
- [ ] Submit buttons name the action, and the confirmation reuses the verb
- [ ] Loading state locks the button width so nothing reflows
- [ ] Tap targets are at least 44px with at least 24px between adjacent targets
- [ ] Server-side validation messages use the same voice as client-side ones
- [ ] Every form renders correctly in both themes and at 390px
