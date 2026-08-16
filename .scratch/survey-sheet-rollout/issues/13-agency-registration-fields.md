# 13 — Agency registration and redress details

**What to build:** A visitor can see, in the footer of every public page, who
they are dealing with and what recourse they have: company registration number,
ICO registration, VAT number where applicable, and the redress scheme the agency
belongs to. In UK property these are the details that make a site credible, and
several of them are a legal requirement rather than a nicety.

An administrator can edit them in the settings panel, so nothing is hard-coded.

Split out of ticket 03: the title block was built but had no fields to read
these from, and inventing registration numbers is not an option.

**Blocked by:** 03

**Status:** ready-for-agent

- [ ] Company registration number, ICO registration, VAT number and redress scheme are editable settings
- [ ] Each is optional, and the footer omits a field entirely rather than rendering an empty label
- [ ] The footer title block shows them alongside the existing office details
- [ ] Values render on every public page
- [ ] A migration adds the settings without disturbing existing values
