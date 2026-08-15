# 20 — Hovering a result raises its pin, and the reverse

**What to build:** On the listings page above 1024px, moving the pointer over a
result card raises its marker on the map, and hovering a marker raises its card.
Both directions, so a reader scanning either side can find the same home on the
other.

Deferred out of ticket 08 rather than rushed: the card and the marker have to
share an identity across a Livewire re-render, and the map container carries
`wire:ignore` so its DOM is deliberately outside Livewire's control. Getting
that wrong leaves stale listeners pointing at markers that no longer exist.

**Blocked by:** 08

**Status:** ready-for-agent

- [ ] Hovering a card raises its marker; hovering a marker raises its card
- [ ] The pairing survives a filter change, a page change and a Livewire re-render
- [ ] No listener is left behind pointing at a marker that has been replaced
- [ ] Keyboard focus on a card does the same thing hover does
- [ ] Nothing happens below 1024px, where the map is collapsed
