# 08 — Search, filters, and the results-and-map pane

**What to build:** A visitor narrows a search and sees the result count change as they go, with no Apply button to hunt for. Filters are toggles that apply on click.

Above 1024px results scroll on the left and the map stays fixed on the right; hovering a result raises its pin and hovering a pin raises its result. Below that the map collapses to a toggle above the results rather than a half-height strip nobody can pan.

When a search returns nothing, the page offers the next move — widening the radius, with the count of what that would find — rather than an apology.

**Blocked by:** 05

**Status:** done, with one criterion deferred

The page was 107 lines of stale marketing hero — including a hotlinked stock
illustration from a third-party CDN — with no filter UI at all. Rewritten:
search, applied-filter chips that each lift their own filter, a live result
count, results beside a sticky map above 1024px, and a map that collapses to a
control below that.

The empty state names the move and its result: it works out which single filter
is costing the most homes and offers to clear it — "Clear the maximum price and
4 homes come back" — with the button attached.

Found while building: the map was showing every property on the books beside a
narrowed list, which invites the reader to think the pins and the cards are the
same set. It now takes the filtered results.

DEFERRED: hovering a result highlighting its pin and the reverse. It needs the
card and the marker to share an identity across a Livewire re-render and a
wire:ignore boundary, which is its own piece of work rather than a line in this
one. Raised as ticket 20.

- [ ] Filters apply on click and the result count updates in place
- [ ] Applied filters are visible as a set and can be cleared individually and all at once
- [ ] Filter state survives a reload and is shareable as a URL
- [ ] Above 1024px, results and a sticky map share the sheet; below, the map is a toggle above the results
- [ ] Hovering a result highlights its pin and the reverse, in both directions
- [ ] Empty results name the next action and the count it would return, with the control attached
- [ ] Sort and result count are readable at 390px without horizontal scroll
