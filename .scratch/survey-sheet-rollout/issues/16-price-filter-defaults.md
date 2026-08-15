# 16 — Search defaults still hide listings

**What to build:** A visitor searching without touching the price sliders sees
every matching home, whatever it costs. The results count they are shown is the
real count.

`PropertyList::$maxPrice` defaulted to 1,000,000 and was applied unconditionally,
so the home page's search — which sends no price bounds — silently dropped every
listing over £1m. Fixed for the maximum in the ticket 06 follow-up, but the same
shape of bug is still latent across the component: several filters default to a
bound rather than to "unset", and `$queryString` disagrees with the property
defaults (it declared `maxPrice` except 10,000,000 against a property default of
1,000,000, ten times apart).

Worth a pass over every filter on that component, because each one can hide
stock without ever saying it did.

**Blocked by:** None — can start immediately.

**Status:** done

Three more instances of the same bug were live: `maxBedrooms = 10`,
`maxBathrooms = 10` and `maxArea = 10000`, each applied unconditionally. So a
14-bedroom or 18,000 sq ft house was invisible on the listings page, always,
with nothing on screen saying a filter was active. Every maximum is unset by
default now, the scopes skip an unset bound, and the `$queryString`
except-values match the property defaults.

A test asserts no maximum defaults to a bound, and a data provider walks a
property past each old cap to prove it still appears.

- [ ] Every filter defaults to unset, and an unset filter narrows nothing
- [ ] `$queryString` except-values match the property defaults exactly
- [ ] Applied filters are visible to the visitor, so a narrowed result set is never a surprise
- [ ] A test asserts an unfiltered search returns the same count as the unfiltered model query
- [ ] A test covers a property priced above every default bound appearing in an untouched search
