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

**Status:** ready-for-agent

- [ ] The estimate shows a range, never a bare single figure
- [ ] What it was derived from is stated, with the date of that evidence
- [ ] A model-generated figure carries a visible label and its confidence range
- [ ] The page renders in both themes and at 390px
- [ ] A visitor can act from the page — book a real valuation with a person
