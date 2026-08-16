# 03 — Header, title-block footer, and the Vellum / Night switch

**What to build:** The site's frame in the system. The header carries navigation and a two-state theme switch — a sun and a moon, no words — that flips the whole site between Vellum and Night. The choice sticks between visits and across pages. Until a visitor chooses, the site follows their operating system.

The footer becomes a title block: the fields a drawing sheet carries — who the agency is, registration and redress-scheme numbers, when the data was last updated — laid out in a ruled grid rather than four columns of links. Those fields are the footer's trust job, so they are content, not decoration.

**Blocked by:** 01, 02

**Status:** done, with one criterion deferred

Navigation, footer title block and the Vellum / Night switch are in the system.
Menu link styling was fixed in `MenuService`, which both navbar templates render,
rather than in the templates themselves.

DEFERRED: "Footer states the agency's registration and redress details."
`GeneralSettings` has no field for a company number, ICO registration or redress
scheme membership, and those are not values to invent. The title block renders
office, telephone, email, country and last-updated from the settings that exist.
Adding the missing fields needs a settings migration and Filament form changes —
worth its own ticket.

Pinned by `tests/Feature/ThemeSwitchTest.php`.

- [ ] Header and footer render in the system on every public page
- [ ] The theme switch flips the site immediately and the choice survives a reload and a navigation
- [ ] With no choice made, the site follows the system preference and keeps following it until the visitor chooses
- [ ] No flash of the wrong theme on a hard reload with a stored choice
- [ ] The switch has an accessible name and a visible pressed state; it is reachable and operable by keyboard
- [ ] Footer states the agency's registration and redress details
- [ ] Navigation collapses to a usable menu at 390px
