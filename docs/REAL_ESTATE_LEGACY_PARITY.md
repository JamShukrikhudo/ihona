# Real-estate legacy parity

This note records the comparison of the `old` branch with the modular
real-estate implementation. The modular packages and the closed architecture
issues are authoritative; legacy application classes are evidence of behavior,
not dependencies to restore.

## Carried into the modular boundaries

| Legacy behavior | Current boundary | Parity status |
| --- | --- | --- |
| `App\Models\Booking` lifecycle, slot collisions, cancellation and rescheduling windows | `real-estate-viewings` actions, `ViewingStatus`, `AvailableViewingSlots`, `ViewingCalendarExport` | Carried over and team-scoped |
| Property valuation ranges and explainable reports | `real-estate-valuations` valuation actions and API/Livewire estimator | Carried over through public actions |
| Property, media, keys, favorites, saved searches, price alerts, reviews and history | `real-estate-properties` and `real-estate-media-and-documents` | Carried over through owned models/actions |
| Rental applications and lease agreements | `real-estate-lettings` | Carried over with API, Filament and Livewire adapters |
| News and community events | `real-estate-marketing` and property presentation surfaces | Carried over through module boundaries |
| Custom report persistence and export | `real-estate-portals-reporting` saved reports | Core workflow carried over; export is currently CSV rather than the legacy PDF/Excel variants |
| AR/holographic tour configuration | `real-estate-properties` API and Filament surfaces | Carried over; no canonical Livewire requirement in the Properties issue scope |

## Remaining legacy candidates

- The old payment routes/models have no matching real-estate issue capability;
  they should be mapped to the payments foundation or a separate module rather
  than added to a real-estate package.
- Social-stream routes likewise belong to their existing social/integration
  boundary, not to the real-estate modules.
- PDF/Excel custom-report exports remain a product decision because the current
  reporting contract exposes a stable CSV export and does not define file
  storage or document-generation ownership.
- The old controller-level calendar integration has been replaced by the
  team-scoped Viewings API iCalendar export; it does not reintroduce legacy
  `App\` services.

## Verification

The comparison was performed against `git show old:...` legacy models/routes and
the current module source. Current verification includes module-boundary and
capability-contract tests plus the full application suite.
