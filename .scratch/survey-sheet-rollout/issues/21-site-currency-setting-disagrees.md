# 21 — The site currency setting disagrees with the listings

**What to build:** One currency on screen at a time. Today a listings page can
show cards priced in pounds — each listing carries its own ISO code, defaulting
to GBP — beside a filter chip reading "Under $100", because the site-wide
`site_currency` setting holds a dollar sign.

Both are behaving as designed; they simply disagree, and the reader sees the
disagreement on one screen.

Decide which is authoritative for a filter, which has no single listing to read
from, and make the setting agree with the stock. Worth checking whether
`site_currency` should hold an ISO code rather than a symbol, so it maps the
same way a listing's does.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] A filter chip and the cards beneath it never show different currencies
- [ ] The site-wide setting and the per-listing default agree out of the box
- [ ] Changing the site currency updates everything that has no listing to read from
- [ ] A test covers a filter chip and a card rendered on the same page
