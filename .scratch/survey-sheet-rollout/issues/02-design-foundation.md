# 02 — Foundation: type, ground, and the theme stamp

**What to build:** Public pages stop looking like default Tailwind. The vellum ground, graphite ink and the three typefaces — Archivo for display, Instrument Sans for body, IBM Plex Mono for every measurement — are in place, and the page knows which theme it is in before it paints.

The Survey Sheet colour, type, spacing and motion tokens already exist in the stylesheet. This ticket makes them real on screen: load the fonts, paint the ground, set the heading face, give focus a visible ring, honour reduced motion, and stamp the resolved theme on the document so no page ever flashes the wrong ground.

Existing page markup keeps working — the tokens are additive, so pages still using the old utility classes render unchanged until their own ticket lands.

**Blocked by:** None — can start immediately.

**Status:** done

Colour is defined once per token with `light-dark()`; the stamp rules flip
`color-scheme` and the whole palette follows. Lightning CSS downlevels this to
guard variables and emits the `prefers-color-scheme` media query plus both
`[data-theme]` overrides, so all three theme states resolve correctly.

Deviation from the acceptance criteria: the document is stamped only when the
reader has stored a choice. With no stored choice it stays unstamped and
`color-scheme: light dark` follows the operating system natively — which is the
behaviour the criterion was reaching for, without the JS.

Pinned by `tests/Feature/DesignFoundationTest.php`.

- [ ] The three typefaces are self-hosted and served locally; no third-party font request on any public page
- [ ] Body ground, body copy colour and heading face come from tokens
- [ ] Keyboard focus is visible on every interactive element and uses one ring treatment site-wide
- [ ] The document carries an explicit theme stamp on first paint, matching the visitor's system preference when they have not chosen
- [ ] `prefers-reduced-motion` collapses transitions and animations
- [ ] Largest Contentful Paint on the home page does not regress against the current build
