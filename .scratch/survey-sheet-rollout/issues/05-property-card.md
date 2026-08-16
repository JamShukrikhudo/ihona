# 05 — The property card and its disclosure strip

**What to build:** The signature element. On the property listings page every result carries a measured band of five facts pulled straight from the record — energy band and score, price per square foot, days since listing, year built, distance to transport — set in mono and tabular, so a column of results lines up and can be compared down the page. It sits where a listing card normally carries a FEATURED flash.

The facts have to come from somewhere. Price per square foot and days listed are derived from data already stored; the energy band and score come from the EPC record. Where a value is genuinely missing the cell says so rather than showing a zero.

**Blocked by:** 01, 04

**Status:** done, with one criterion adapted

`x-property-card` renders on `/properties`; the derived facts live in
`App\Models\Concerns\HasDisclosureFacts` so `Property` did not grow further.
The strip drops 5 cells to 3 below 380px, verified in a browser at 360px.

ADAPTED: "distance to transport". The record holds no distance — it holds
`transit_score`, from the walkability service. The strip shows that, labelled
Transit, rather than inventing a distance. A real distance needs a station
dataset and is its own ticket.

Found while building, and fixed:

- A rental's price per square foot rounded 1.69 to 2, throwing away the only
  part of a monthly rate anyone compares. It keeps two decimals below the unit
  now, and the first test I wrote had encoded the bug.
- The card read the site-wide currency symbol, set to a dollar sign in this
  environment, while each listing carries its own ISO code. It uses the
  listing's currency and falls back to the setting.
- "Not supplied" is wider than a 60px strip cell at 11.5px mono, so it
  truncated to "Not supp..." and wrapped, making one card's strip taller than
  its neighbours — breaking the column alignment the strip exists for. Missing
  values render as an em dash carrying an aria-label, and every value line has
  a fixed line box so a chevron cell cannot outgrow a dash cell. Measured
  aligned at 52px across all four cards in both themes.

Raised ticket 15: `year_built` is a MySQL YEAR column and cannot hold anything
before 1901, so period stock cannot record a build year at all.

- [ ] The listings page renders themed cards in the sheet grid: 1 up to 640px, 2 to 1023px, 3 above
- [ ] Each card shows price, property type and bedroom count, address, bedrooms, bathrooms and floor area
- [ ] The disclosure strip shows energy band and score, price per square foot, days listed, year built and distance to transport
- [ ] Price per square foot and days listed are derived, not stored, and are correct for both sale and rental listings
- [ ] A missing value renders as an explicit "not supplied", never 0 or a blank cell
- [ ] Below 380px the strip drops to three cells and keeps energy, price per square foot and days listed
- [ ] Status chips state a fact with a number — "Reduced £15k", "New — 2 days" — never a mood
- [ ] The whole card is one link target; the buttons inside it are separate targets and are not nested anchors
- [ ] A missing photo renders the drawn-elevation placeholder and the grid does not jump
- [ ] Everything the card says is legible without hover
