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

**Status:** ready-for-agent

- [ ] A decision recorded: build auctions, or delete the model, relations and view
- [ ] If building: lot status, registration deadline and legal pack availability are stored per auction
- [ ] If building: a public route reaches the page, and a property in auction says so on its card and detail page
- [ ] If removing: no dead relation, model or view is left behind, and nothing calls `isInAuction()`
- [ ] Either way, no code path can query a table that does not exist
