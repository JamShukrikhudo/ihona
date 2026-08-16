# 06 — Home page

**What to build:** The front door in the system. A visitor lands and can immediately do the one thing they came for — search — and see featured properties as themed cards carrying their disclosure strips.

The hero states something the visitor can act on rather than "Find your dream home". The search bar is the primary control on the page and the only orange thing on it. Featured properties reuse the card from 05. The map section is styled to the system rather than left as a default tile layer.

**Blocked by:** 03, 05

**Status:** done

The hero leads with the search. Featured homes reuse the card from ticket 05
with its CTA row suppressed, so the search stays the page's one primary action.

Two things found while building:

- The map called `.locate({setView: true})` on page load, so the home page
  asked every visitor for their location before they had done anything, and
  pulled OpenStreetMap tiles above the fold. It now draws nothing until the map
  scrolls into view — measured 0 tile requests on load, 18 after scrolling — and
  geolocation moved behind a "Show my location" button.
- The navbar's Register link was styled as a primary action on every page,
  competing with whatever primary that page had. It is secondary now.

Review follow-up found more, all confirmed before fixing:

- `App\View\Components\PropertyMap` discarded the properties passed to it and
  re-queried without `price`, so every marker popup read "£NaN" and the
  controller's map query was dead code. The controller no longer duplicates it.
- The popup concatenated a staff-entered title into HTML — stored XSS across
  tenants. Built as DOM nodes now; verified a title of `<img src=x
  onerror=alert(1)>` renders as text and fires nothing.
- The hero's property-type options were title case while the staff panel stores
  lowercase and the scope is an exact match, so the filter returned nothing on
  any case-sensitive connection. `condo` was missing entirely.
- The search hid every listing over £1m (see ticket 16).
- Only the first map on a page initialised, and a Livewire re-render wiped
  Leaflet's DOM permanently.

Also: the home page pushed a second `@livewireScripts` (harmless, Livewire
dedupes, but dead), the featured query ran a media lookup per card, and the map
query selected every column of every property to build an eight-key array.

- [ ] Hero leads with the search, not a full-bleed lifestyle photo with text over the middle
- [ ] Search accepts a postcode, station or area and submits to the listings page with the filters applied
- [ ] Featured properties render as the card from 05, disclosure strips included
- [ ] Exactly one primary action on the page
- [ ] Map geometry and pins use the system colours; the map does not autoload heavy tiles above the fold
- [ ] Renders correctly in both themes and at 390px
- [ ] Largest Contentful Paint on mobile does not regress
