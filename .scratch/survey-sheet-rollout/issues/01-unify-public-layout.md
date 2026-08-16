# 01 — Every public page renders through one shell

**What to build:** A visitor moving between any two public pages — home, a property, the news, the mortgage calculators, their wishlist — sees the same header and footer on both. Today roughly ten Livewire pages render with no navigation and no footer at all, because they fall back to the bare Livewire shell instead of the public one. After this ticket every public route shares a single shell.

This is a prefactor. No visual redesign happens here: the pages keep the styling they have now, they just gain the chrome they were missing. Everything that follows assumes one shell, so the theme only has to be applied once.

**Blocked by:** None — can start immediately.

**Status:** done

Reality differed from the ticket. `config('livewire.component_layout')` already
pointed every Livewire full-page component at the public shell, so the shell was
never the problem. What was actually broken:

- Four Livewire component views (`news-list`, `news-detail`, `property-list`,
  `wishlist-manager`) used `@extends`/`@section`, which renders a whole second
  document inside the slot. `/news` was a hard 500; `/properties` silently served
  two nested `<html>` documents.
- `holographic-viewer` wrapped its body in `@section('content')` with no
  `@extends`, so the page rendered an empty div.
- The shell had no `@stack('scripts')` or `@yield('styles')`, so every
  `@push('scripts')` in the codebase (5 views) was silently dropped, along with
  the home page's Leaflet stylesheet.

All fixed. `tests/Feature/PublicShellTest.php` pins it, including a
shell-renders-exactly-once assertion that catches the silent nesting case.

- [ ] Every public route renders with the site navigation and footer present
- [ ] The bare Livewire shell is no longer reachable from a public route
- [ ] Pages that already had chrome are visually unchanged
- [ ] Authenticated and Filament panel routes are untouched
- [ ] A test asserts the shell renders for a representative public route from each family (static page, property, Livewire page)
