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

**Status:** done

- [x] Hovering a card raises its marker; hovering a marker raises its card
- [x] The pairing survives a filter change, a page change and a Livewire re-render
- [x] No listener is left behind pointing at a marker that has been replaced
- [x] Keyboard focus on a card does the same thing hover does
- [x] Nothing happens below 1024px, where the map is collapsed

## Notes

Both sides key on the listing id. `PropertyMap::points()` already carried it;
the card now carries `data-property-id`.

The re-render problem the ticket was deferred over is answered by **not binding
to cards at all**. Every card listener is delegated from `document`, which
Livewire never replaces, so there is nothing to leave behind — a card swapped
out mid-hover is simply a card the next event no longer matches.

Markers are the opposite case: they are real objects that are destroyed and
rebuilt on every filter change. The update handler releases the raised pair
*before* removing layers — afterwards there is no element left to take the
class off, and the card stays lit with no pin to match it — and calls
`marker.off()` before `removeLayer()` so a marker that has left the map is not
still listening for a hover.

Two smaller traps:

- The raised state is a plain CSS rule on an attribute and a literal class, not
  a Tailwind utility. Tailwind scans these files as text and emits nothing for a
  class name it cannot see, so a utility added at runtime is a class with no
  rule behind it. That trap has cost this rollout four bugs already.
- The marker lift uses the `scale` property rather than `transform`. Leaflet
  positions every marker with its own `transform`, so writing to that property
  drops the pin at the map's origin.

Covered twice: `ResultsAndPinsTest` holds the contract (matching identities,
delegation, release-before-remove, the width gate), and
`tests/Browser/map-pairing-check.mjs` drives the real thing in Chromium —
including tearing every card out of the DOM and replacing it the way a Livewire
re-render does. 12/12 browser checks pass.
