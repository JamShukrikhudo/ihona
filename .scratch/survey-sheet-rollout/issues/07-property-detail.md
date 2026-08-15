# 07 — Property detail page

**What to build:** The page where a buyer or tenant decides whether to book a viewing. Everything the record knows is disclosed and easy to find: the full energy certificate with its band, floor area, year built, tenure and running costs, the price history, and the chain position.

Photography is treated as evidence — consistent crops, no filters. The floor plan and any site plan sit in the same gallery as the photos rather than as a link at the bottom. Heavy media — 360°, 3D, AR, holographic tours — sits behind an explicit control so it never loads itself on the page that most needs to rank.

Booking a viewing is always reachable without hunting for it.

**Blocked by:** 05

**Status:** partly done — the rest is ticket 17

Done: the disclosure panel, which is the ticket's core. Six facts, each with a
dated source line ("Certificate, assessed 12 Mar 2019", "Derived from price and
floor area"), and an em dash where the record holds nothing. Heavy media no
longer loads itself — the 3D model carries `reveal="interaction"` and
`loading="lazy"`, both videos `preload="none"`. "Book a viewing" is the named
action at the top and again at the end of the page.

NOT done, and moved to ticket 17: the gallery work (floor plan and site plan
alongside the photographs, one crop ratio per context, rooms captioned), the AI
labelling, and the visual restyle of the page itself. The view is 1,115 lines
and needs breaking up before it can be restyled safely; doing it inside this
ticket would have been a large blind edit.

Council tax band cannot be shown at all: there is no column for it, nor for
tenure, service charge or ground rent. Raised as ticket 18.

- [ ] A disclosure panel shows energy band and score, floor area, year built, price per square foot, days listed and council tax band, each labelled with where it came from and when
- [ ] Floor plan and site plan appear in the gallery alongside the photographs
- [ ] Gallery images use one crop ratio per context; rooms are captioned by name
- [ ] Tours load only on an explicit action, never automatically
- [ ] Book a viewing is reachable from the first screen and again at the end of the page
- [ ] The button that says "Book a viewing" produces a confirmation that says "Viewing booked"
- [ ] Any AI-generated valuation, description or staged image carries a visible label and, where applicable, a confidence range
- [ ] Renders correctly in both themes and at 390px
