# 18 — Tenure and running costs

**What to build:** A buyer can see what a property will cost them to hold:
council tax band, tenure (freehold or leasehold, and years remaining on the
lease), service charge and ground rent. These are the questions asked
immediately after the price, and a leasehold with 68 years left is a materially
different proposition from the same flat with 900.

None of it can be shown today because none of it is stored: `properties` has no
column for council tax, tenure, service charge or ground rent. Found while
building the ticket 07 disclosure panel, which lists council tax band as a fact
it should carry.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Council tax band, tenure, lease years remaining, service charge and ground rent are stored per property and editable by staff
- [ ] Each is optional; the disclosure panel omits or marks as not supplied rather than inventing a value
- [ ] A leasehold shows its remaining years, and a short lease is visually distinct
- [ ] The disclosure panel and the property card show them where there is room
- [ ] Estimated annual energy cost is derived from the EPC where one is held
