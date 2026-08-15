# 24 — The contrast the sweep still reports

**What to build:** Nothing new. This records what `tests/Browser/public-site-sweep.mjs`
still reports so it is not quietly forgotten, and so each page's restyle ticket
knows what it has to clear.

About twenty text nodes fall between 2.6:1 and 4.3:1 against a 4.5:1
requirement. Every one is on a page not yet moved onto the design system, and
every one is a pre-existing colour choice rather than something the rollout
introduced:

- `book` — the required-field asterisk, 3.8:1 light / 4.3:1 dark (ticket 19)
- `search` — "Search Properties" button, 3.8:1 (ticket 19)
- `calculators` — "Calculate" button, 3.8:1 (ticket 10)
- `news` — "Check back later for updates", 4.3:1 (ticket 10)
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

**Status:** ready-for-agent

- [ ] Every node the sweep reports is either fixed or has a recorded, justified exception
- [ ] The sweep reports no contrast failure on any public page
- [ ] The compatibility shims in app.css are removed as each page stops needing them
