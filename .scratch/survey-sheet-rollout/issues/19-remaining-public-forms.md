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

**Status:** ready-for-agent

- [ ] Every field a form collects is validated and persisted, or removed from the form
- [ ] Every input has a real label; placeholders are examples, never names
- [ ] Errors name the fix in the interface's voice, and are wired to their field
- [ ] What the visitor typed survives a rejected submission
- [ ] Submit names the action and the confirmation reuses the verb
- [ ] Every form renders in both themes and at 390px, with 44px touch targets
