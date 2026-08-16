# 24 — The contrast the sweep still reports

**What to build:** Nothing new. This records what `tests/Browser/public-site-sweep.mjs`
still reports so it is not quietly forgotten, and so each page's restyle ticket
knows what it has to clear.

Tickets 10 and 19 cleared the calculators, news, book and search entries. What remains falls between 2.6:1 and 4.3:1 against a 4.5:1
requirement. Every one is on a page not yet moved onto the design system, and
every one is a pre-existing colour choice rather than something the rollout
introduced:

- `detail` — "AI-Powered Insights" 2.6:1, and a rate figure at 3.1:1 (ticket 17)
- `valuation` — a "login" link at 3.4:1 (ticket 23)

Run the sweep to get the current list; it prints every failing node with its
measured ratio, the ratio required at that size and weight, and the text.

The sweep originally skipped every `position: fixed` element — `offsetParent`
is null for fixed positioning whatever its visibility — so the shared chrome
was excluded from all 60 combinations. That is fixed; re-running found no
additional failures, so the list above is the whole of it rather than the part
the sweep could see.

**Blocked by:** None — each is cleared by the page's own restyle ticket.

**Status:** done — with one exception recorded below

The sweep now reports no contrast failure on any of the 60 page/width/theme
combinations. The detail entries went with ticket 17, the valuation "login" link
with ticket 23.

**The exception: the compatibility shims stay.** They remap `bg-white`,
`bg-gray-50/100` and the `text-gray-*` ramp in dark, and 84 Blade files still
depend on them — the auth screens, the Jetstream profile and team views, the
dashboards. None of those is a public storefront page, so none is in this
rollout; removing the shims before they are restyled would leave dark ink on a
dark ground on every one of them. Delete a line as each of those pages moves,
not before.

Worth stating plainly: **the sweep only measures the first paint.** Four
contrast failures found by hand in the previous round — the chatbot reply, the
calculator's estimated value, its disclaimer, three unselected tabs — were all
behind a result the sweep never triggers, and it reported clean throughout. A
clean sweep means no failure on load, not no failure.

- [ ] Every node the sweep reports is either fixed or has a recorded, justified exception
- [ ] The sweep reports no contrast failure on any public page
- [ ] The compatibility shims in app.css are removed as each page stops needing them
