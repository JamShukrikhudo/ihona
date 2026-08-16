# 23 — Valuation page

**What to build:** A visitor asking what their home is worth sees an estimate
with a range, not a single confident number, and is told plainly what the
estimate was derived from — comparable sales, floor area, the energy record —
and how recent that evidence is.

Anything a model produced says so on its face, with its confidence range. An
unlabelled machine valuation presented as a firm figure is the fastest way to
lose a property audience, and the design system already commits to labelling it.

Split out of ticket 11: the page is 269 lines and carries the same AI-labelling
requirement as ticket 17, so the two are worth doing with one set of decisions
about how a generated figure is presented.

**Blocked by:** None — can start immediately.

**Status:** done

The band was already being computed — `NeuralNetworkValuationService` returned a
`price_range` on every call that nothing read, stored or printed, and the width
was inverted: `confidence / 200` made a model 90% sure quote ±45% and one 20%
sure quote ±10%. One formula now lives on `PropertyValuation`, derived rather
than stored so every row already in the table gets a band.

Two things found on the way: `viewValuation()` took any id and rendered it, so
the public route handed out any property's valuation history by counting
integers; and the page showed nothing at all until a signed-in agent pressed a
button, so a visitor asking what a home is worth saw a form.

- [x] The estimate shows a range, never a bare single figure
- [x] What it was derived from is stated, with the date of that evidence
- [x] A model-generated figure carries a visible label and its confidence range
- [x] The page renders in both themes and at 390px
- [x] A visitor can act from the page — book a real valuation with a person
