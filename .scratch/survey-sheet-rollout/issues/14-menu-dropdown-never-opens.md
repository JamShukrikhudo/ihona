# 14 — Menu dropdowns never open

**What to build:** A visitor hovering (or focusing) a navigation item that has
children sees its submenu. Today they never can: the submenu is rendered, but
positioned and hidden such that nothing reveals it, so every child page in the
menu is unreachable from the navigation.

`MenuService` puts `relative group` on the link element while adding the submenu
as its *sibling*, and `group-hover:` needs `.group` to be an ancestor. Found
during the code review of the Survey Sheet chrome work — the styling was updated
in place, but the markup was already dead before that, so no behaviour changed.

While fixing it, make the submenu keyboard-reachable: hover alone strands anyone
navigating by keyboard, and on touch there is no hover at all.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] A navigation item with children reveals its submenu on hover
- [ ] The submenu opens on keyboard focus and closes on Escape
- [ ] The submenu is reachable on touch, where there is no hover
- [ ] Submenu links are announced as a group by screen readers
- [ ] A test seeds a parent menu item with children and asserts the submenu markup nests correctly
