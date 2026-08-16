# 15 — Year built cannot hold anything before 1901

**What to build:** An agent can record the build year of a Victorian, Georgian
or older property and see it on the listing. Today they cannot: `year_built` is
a MySQL `YEAR` column, whose range is 1901–2155. Writing 1861 throws an out of
range error under strict mode, and stores 0000 without it.

That excludes a large share of UK housing stock, and the disclosure strip shows
"Built" as one of its five facts — so the gap is now visible on every card for
any period property.

Found while seeding realistic listings to check the card in a browser: a
Pangbourne cottage built in 1861 could not be saved at all.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] A property built before 1901 can be saved and shows its year on the card and the detail page
- [ ] Existing values migrate without loss, and any row already zeroed is reported rather than silently rewritten
- [ ] Validation rejects a year in the future and anything implausibly early, with a message naming the accepted range
- [ ] The listings filter by year built still works across the widened range
- [ ] A test covers a pre-1901 year end to end, from save to rendered card
