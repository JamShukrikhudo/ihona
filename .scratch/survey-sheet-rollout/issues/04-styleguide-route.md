# 04 — Styleguide route, backed by real components

**What to build:** A team member opens `/design` and sees the live Survey Sheet reference — palette, type scale, buttons, fields, chips, EPC bands, the icon set — rendered by the same components the public site uses. When a component changes, the styleguide changes with it, so it cannot drift into a lie.

Building the page is what produces the shared vocabulary: button, field with label and error, chip, EPC band, and the technical-pen icon set (bedrooms, bathrooms, floor area, floor plan, aspect, certificate, EPC, transport, location, chain, viewing, price, tenure, 360 tour, property, enquiry).

The home page currently carries three Font Awesome icon spans for bedrooms, bathrooms and floor area. Font Awesome is never loaded, so those icons render as nothing today. Replace them with the system icons and drop the dead references.

**Blocked by:** 02

**Status:** done

`/design` renders from real components, registered outside production and served
`X-Robots-Tag: noindex, nofollow`. Components live under `x-ui.*` — `button`,
`field`, `chip`, `epc-band`, `icon` — plus `x-design.heading` for the styleguide
itself. No `@layer components`: everything is utilities in the Blade files, so
Tailwind purges normally and nothing captures the Bootstrap-idiom `.btn`
buttons in the bookings views.

Two bugs the unit tests passed straight through, both caught in a browser:

- The secondary button had no visible border. `border-transparent` in the base
  and `border-sheet-300` in the variant are the same utility, so generated
  source order decided the winner, not the order they were written. Variants now
  each set their own border colour, with a test asserting none sets it twice.
- The energy bands painted white on white. Tailwind drops any `@theme` variable
  no utility references, and the component reached for `--color-epc-*` through
  an inline `var()` the scanner cannot see. Colours are whole class names now.

- [ ] `/design` renders palette, type scale, buttons, fields, chips, EPC bands and the icon set from real components
- [ ] Every component reads its colour and type from tokens; no hard-coded hex in a component
- [ ] Buttons cover primary, secondary, ghost and danger, in three sizes, with hover, active, focus, disabled and loading
- [ ] Fields cover label, hint, error and disabled, wired for screen readers
- [ ] EPC bands A–G use the statutory colours and always show the letter, never colour alone
- [ ] Icons are 1.5px stroke on a 24px grid with square caps; each carries an accessible name or is marked decorative
- [ ] The dead Font Awesome spans are gone from the home page
- [ ] The route is reachable in local and staging, and not indexed
