# 17 — Property detail: gallery, AI labelling and restyle

**What to build:** The rest of the detail page. A visitor sees the floor plan
and any site plan in the same gallery as the photographs rather than as a link
at the bottom; every image in a given context shares one crop ratio; and each
room is captioned by name rather than by filename. Anything a model generated —
a valuation, a description, a staged image — says so on its face and shows its
confidence range.

The page also still carries its old styling. The view is 1,115 lines, which is
why the ticket 07 work stopped at the disclosure panel: restyling it safely
means breaking it into sections first, so each can be moved onto the design
system and checked on its own.

**Blocked by:** 07

**Status:** ready-for-agent

- [ ] The view is split into sections small enough to review, with no behaviour change in that step
- [ ] Floor plan and site plan appear in the gallery beside the photographs
- [ ] One crop ratio per context; each room captioned by name
- [ ] Model-generated valuations, descriptions and staged images carry a visible label and, where applicable, a confidence range
- [ ] The page renders in both themes and at 390px with no horizontal scroll
