# 12 — Acceptance sweep across the public site

**What to build:** Proof that the theme actually holds everywhere, rather than on the pages someone remembered to check. One pass over every public route in both themes, at mobile and desktop, with the results recorded.

This is where the promises the system makes get tested against what shipped: contrast, colour never carrying meaning alone, focus visible everywhere, motion respecting the reader's preference, and no page having quietly regressed its load performance.

**Blocked by:** 03, 04, 05, 06, 07, 08, 09, 10, 11

**Status:** done, with a recorded residue

Two halves. `tests/Feature/PublicSiteSweepTest.php` enumerates the public routes
from the router rather than a hand-kept list, and walks every one.
`tests/Browser/public-site-sweep.mjs` checks the 60 page/width/theme
combinations a browser can see: horizontal scroll, computed contrast, console
errors, and any request that leaves the origin.

Clean across all 60: no horizontal scroll at 390px or 1440px, no console
errors, no third-party request.

What the sweep found and this ticket fixed:

- Two public pages returned 500. `/properties/{property}/book` — the primary
  action on every card and the detail page — had a route parameter named
  `{property}` against a component expecting `$propertyId`, so Livewire could
  not resolve it. `/properties/{property}/apply` rendered a view that does not
  exist; its controller also created a user account, stored no application at
  all, and redirected to a route name that does not exist.
- `bg-primary-*` generated nothing. Replacing the palette deleted that ramp
  while 25-odd usages remained on pages not yet restyled — every one rendering
  white text on no background. Restored as an alias of the action colour.
- White on energy bands B and F measured 2.7:1, under AA at the 12px the strip
  uses. Ink is chosen per band by measurement now; only A and G keep white.
- The danger button used fault-600, whose dark side is 3.1:1 against white.
  Fixed in both themes like the action fill.
- Pages not yet restyled hardcode `bg-white` and `text-gray-900`. Shipping dark
  mode site-wide turned those into a white card on the night ground, then — once
  the surface was fixed — dark ink on a dark card. Both legacy scales now follow
  the theme, unlayered so they beat Tailwind's utilities.
- Leaflet's attribution link sat at 3.6:1.

RESIDUE, not waived — attached to the pages' own tickets. About twenty text
nodes still fall between 2.6:1 and 4.3:1 against a 4.5 requirement, all on
pages awaiting their restyle: book and search (ticket 19), calculators and news
(ticket 10), detail and valuation (tickets 17 and 23). Ticket 24 records them
so they cannot be lost.

- [ ] Every public route checked in both themes at 390px and 1440px, with the list of routes recorded
- [ ] Body copy and labels meet WCAG 2.2 AA; large text meets AA; failures fixed rather than waived
- [ ] No meaning carried by colour alone anywhere — energy bands show the letter, statuses show the word
- [ ] Focus is visible on every interactive element on every page, and focus order is sensible
- [ ] With reduced motion set, no page animates
- [ ] No page scrolls horizontally at 390px
- [ ] No third-party font, script or stylesheet request from a public page
- [ ] Largest Contentful Paint on home and property detail is at or better than the pre-rollout build, measured on mobile
- [ ] The styleguide at /design matches what the site actually renders
