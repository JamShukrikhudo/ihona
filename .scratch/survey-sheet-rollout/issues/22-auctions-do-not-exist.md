# 22 — Auctions are wired up but have no table

**What to build:** Decide what auctions are, then either build them or remove
the half of them that exists.

Today the codebase carries an `Auction` model, a `Property::auctions()`
relation, `currentAuction()`, `isInAuction()`, a `Bid` model and a seven-line
`auctions/show.blade.php` — and no `auctions` table, no migration that would
create one, and no route that would reach the view. Anything calling
`isInAuction()` is one query away from a "table does not exist" error.

Found while working ticket 11, whose acceptance criteria include showing lot
status, registration deadline and legal pack availability. None of those have
anywhere to be stored.

**Blocked by:** None — can start immediately.

**Status:** done

**Decision: removed.** No table, no migration, no route, no caller outside the
auction files themselves — 385 lines describing a feature the site has never
been able to run. Building it means lots, registration deadlines, legal packs,
live bidding, an outbid notification and the money path behind all of it; that
is a product someone has to want, not a gap to fill during a theme rollout.
Deleted so it stops reading as a feature that exists:
`Auction`, `Bid`, `BidPlaced`, `AuctionController`, `AuctionBidding`,
`AuctionInterface`, `AuctionNotification`, `auctions/show.blade.php`,
`livewire/auction-interface.blade.php`, and `Property::auctions()`,
`currentAuction()` and `isInAuction()`. The styleguide's "Auction" chip example
now shows a status the site can actually reach.

If auctions are wanted, they start from a schema and a route, and git holds the
old sketch.

- [x] A decision recorded: build auctions, or delete the model, relations and view
- [x] If removing: no dead relation, model or view is left behind, and nothing calls `isInAuction()`
- [x] Either way, no code path can query a table that does not exist
