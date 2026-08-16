# 11 — Auctions, comparison, wishlist and valuation

**What to build:** The remaining public pages, brought onto the card and disclosure vocabulary so nothing on the public site is left in the old styling.

Auctions show lot status, the legal pack and the registration deadline as stated facts with dates. Comparison lines properties up in a table where the disclosure facts become the rows — the one place the mono tabular figures earn their keep most. The wishlist is a saved set of cards with an empty state that invites a search. Valuation shows its estimate with a range and says what it was derived from.

**Blocked by:** 05

**Status:** partly done — auctions and valuation are tickets 22 and 23

Done: comparison and the wishlist.

Comparison is the strongest case for the mono tabular figures in the whole
system. The disclosure facts are the rows, so two homes line up digit for digit
and the difference is the thing you see rather than something you work out. The
table scrolls inside its own container — verified at 390px, where the table
scrolls and the page does not.

The wishlist renders saved homes as the card, with an empty state that names
the next action. Found while testing: its default sort joined `favorites` a
second time on top of the `belongsToMany` that already joins it, so every
`favorites` column was ambiguous and the page threw on its own default view.
A test now renders every sort order.

NOT done:

- Auctions. There is an `Auction` model, a `Property::auctions()` relation, an
  `isInAuction()` check and a seven-line view — but no table, no migration and
  no route. The feature does not exist to restyle. Ticket 22.
- Valuation. It is 269 lines with its own AI labelling requirement, which is
  the same requirement ticket 17 carries for the detail page. Ticket 23.

- [ ] Auction pages show lot status, registration deadline and legal pack availability as dated facts
- [ ] Comparison renders the disclosure facts as aligned rows with tabular figures and scrolls horizontally inside its own container
- [ ] Wishlist renders saved cards; its empty state names the next action and links to search
- [ ] Valuation shows a range and states what the estimate was derived from, labelled if model-generated
- [ ] Saving and removing from the wishlist gives immediate feedback and survives a reload
- [ ] All four render correctly in both themes and at 390px
