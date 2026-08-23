# Liberu Real Estate

The Real Estate scope covers agency operations for properties, parties,
listings, viewings, offers, progression, lettings, management, marketing, and
portal reporting. Each capability has one framework-neutral core boundary and
optional one-to-one API, Filament, and Livewire adapters.

## Delivery order

1. Core, Parties, Properties, Media, Valuations, Instructions, Listings,
   Matching, Viewings, Offers, and the website feed.
2. Sales Progression, portals, marketing, reporting, communications, and
   document/e-signature flows.
3. Lettings, deposits, inspections, management, maintenance, accounting, and
   owner/tenant portals.

All records are team-scoped, authorization-aware, auditable, and subject to
retention and recovery rules. Provider integrations belong in replaceable
adapters rather than core packages.
