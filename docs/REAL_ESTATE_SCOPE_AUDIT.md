# Real Estate Product Scope Audit

Audit date: 2026-07-28  
Scope: [Liberu Real Estate Product Feature & Design Scope v1.0](https://raw.githubusercontent.com/liberusoftware/scopes/refs/heads/main/REAL-ESTATE.md)

## Result

All present-tense requirements in scope v1.0 are implemented. The items under
“Future Modules”, the future installable-country-module note, future communication
channels, and the future visual workflow builder are explicitly roadmap items in
the source and are not treated as current release requirements.

The primary implementation surface is the authenticated, organisation-scoped
`/api/v1` REST API documented in [CORE_API_V1.md](CORE_API_V1.md). Public agency
website endpoints are separately rate-limited. All tenant-owned resource lookups
validate organisation ownership, and module permissions are enforced by
`RequireAgencyPermission` and `AgencyPermissionService`.

## Requirement traceability

| Scope area | Implementation evidence | Verification evidence |
| --- | --- | --- |
| Vision and core principles | Laravel 13 and Filament 5 application; versioned API; organisation/team tenancy; branches; country configuration; modules; roles and granular permissions; pagination, indexes, queues, scheduler, responsive Filament panels, environment-based filesystem and service configuration | `routes/api.php`, `config/modules.php`, `app/Services/AgencyPermissionService.php`; API route inventory |
| First-time setup | Setup options/status/completion API captures organisation identity, branding, contacts, address, countries, primary country, locale and timezone; regional defaults are snapshotted per organisation | `SetupController`, `CountryConfiguration`, `ApplyOrganisationLocale`; setup and localisation feature tests |
| Country defaults and localisation | GB, IE, AU, CA, US, ZA and NZ packs provide currency, dates, phones, addresses, units, taxes, languages, postal validation, terminology, legal defaults and portals; multiple listing currencies and organisation timezones/locales are supported; English and French translation files are included | `config/countries.php`, `lang/en/agency.php`, `lang/fr/agency.php`; tests reject unsupported countries/languages/postcodes and prove Canadian French runtime locale |
| Users and permissions | Users, teams, branches, departments, job roles/profile fields, Fortify MFA, Sanctum ability-scoped API tokens, module/action permission catalog, member overrides and permission audit trail | `config/fortify.php`, `User`, `ApiTokenController`, `PermissionController`; permission, cross-team, token, staff and department tests |
| CRM contacts | A single typed contact record supports every listed persona, multiple addresses/phones/emails, notes, tags, communication history, documents and bidirectional relationships | Contact, contact-address, contact-channel and relationship models/controllers; CRM feature tests |
| Companies | Organisation-scoped business contacts cover developers, property companies, housing associations, investors and contractors with linked contacts | `CompanyController`; company CRUD and tenant-isolation tests |
| Property records and statuses | Residential, commercial, land, new-build, development and mixed-use records contain address, coordinates, EPC, areas, rooms, parking, gardens, features, descriptions, notes, branch, currency and the complete scoped status vocabulary | `PropertyController`, `Property`, property migrations; property detail/default/status tests |
| Property media | Images, floorplans, EPCs, video, virtual/360 tours, documents and brochures support ordering and metadata; image ingestion performs orientation correction, resizing, optimisation, watermarking and checksums | `PropertyMediaController`, `PropertyMediaProcessor`; upload, ordering and processing tests |
| Sales | Vendor instruction/onboarding, valuation, marketing, viewing, offer, memorandum and milestone progression through exchange and completion are linked to listings and contacts | Instruction, valuation, offer and sales-progression APIs; end-to-end lifecycle tests |
| Lettings | Landlords, applicants, tenants, references/screening, guarantors, deposits, inventories, agreements, renewals, notices and end-of-tenancy states are represented | Rental application, tenancy agreement, tenant, deposit/inventory fields and APIs; lettings lifecycle tests |
| Property matching | Buyers and tenants are scored against available property budget, bedrooms, type, location/radius, features, availability, schools and transport; matches can generate notifications | `PropertyMatchingService`, match API; scoring/filtering/notification tests |
| Valuations | Market, rental and commercial appraisals support appointments, negotiators, notes, comparables, reminders and outcome tracking | `ValuationController`; valuation workflow tests |
| Viewings | Appointments support multiple attendees, assigned staff, calendar projection, confirmations/reminders, feedback and outcomes | `ViewingController`, `Appointment`, calendar API; viewing workflow and calendar tests |
| Offers | Offers retain amount, buyer, mortgage, chain, conditions, status and immutable negotiation events for accepted, rejected and withdrawn decisions | `OfferController`, offer events/timeline; negotiation and tenant-isolation tests |
| Sales progression | Configurable milestones and timeline cover accepted offer, memorandum, mortgage, searches, survey, exchange and completion | `SalesProgressionController`; stage/timeline tests |
| Maintenance | Requests include issue, priority, photos, contractor assignment, quotes, work orders, completion, cost/invoice references and updates without implementing bookkeeping | Maintenance, contractor, quote and work-order APIs; operational workflow tests |
| Inspections | Routine, check-in, check-out and mid-tenancy inspections store photos, notes, damage and signatures | `InspectionController`; inspection CRUD/validation tests |
| Diary and calendar | Unified appointments/calendar entries cover viewings, valuations, meetings, inspections, maintenance, tasks and reminders with day/week/month/agenda query modes and ICS output | `CalendarController`, `CalendarEntryController`; calendar aggregation tests |
| Tasks | Personal/team assignment, due dates, priorities, checklists, attachments and comments are organisation scoped | Task and task-collaboration APIs; collaboration and cross-team tests |
| Communication | Organisation/contact/property communication history supports email, SMS, phone, note and letter channels | `CommunicationController`; communication tests. WhatsApp, Teams and Slack are marked future in the source |
| Documents | Private documents cover every listed category, generated files, categories, permissions, version history and digital-signature records with authorised download endpoints | Document and document-workflow APIs; version/signature/access tests |
| Marketing | Brochures, window cards, social assets and QR codes are generated; public property pages, featured listings and scheduled email campaigns with metrics are available | Marketing asset/campaign controllers and public website API; marketing tests |
| Website integration | Rate-limited public endpoints expose searchable property lists/details, featured/new/sold filters, branches and staff profiles | `/api/v1/public/agencies/{team}` routes; public visibility/filter tests |
| Property portals | Country packs advertise appropriate portals; integrations manage listing publication, media payloads, automatic/manual exports, status and run history; hourly/daily/weekly schedules are dispatched every 15 minutes | `PortalIntegrationController`, `PortalSyncService`, `portal-integrations:sync`, `routes/console.php`; portal and scheduled-command tests |
| Reporting | Dashboard, sales/lettings pipelines, instructions, offers, viewings, valuations, staff/branch/marketing/portal performance, date/branch filters, saved reports, charts and CSV exports | `ReportController`, saved-report API; complete reporting and isolation tests |
| Dashboards | Per-user layouts persist KPI, calendar, task, activity, sales, lettings, lead, property, maintenance and branch widgets | `DashboardLayoutController`; layout validation tests |
| Automation | Event/criteria/action rules cover email, assignment, notifications, publishing/export, reminders and task creation; runs are auditable and retry-safe | `AutomationEngine`, automation/run APIs; action and idempotency tests. Visual builder is explicitly future |
| Notifications | Preference-aware in-app, email, SMS and push deliveries cover enquiries, offers, viewing requests, tasks, maintenance, renewals and portal failures | `WorkflowNotifier`, `NotificationDispatcher`, notification/preferences/delivery APIs; workflow dispatch tests |
| Search and filtering | Global search spans properties, contacts, companies, documents, current viewings, tasks, offers, maintenance and staff; domain list endpoints expose typed filters | `SearchController` and resource controllers; global search and filter tests |
| Accounting integrations | Liberu Accounting, QuickBooks Online, Xero and Sage connectors store customer/supplier links, invoice references, payment status, sync runs and summaries; no ledger/bookkeeping is implemented | `AccountingIntegrationController`; accounting-boundary tests |
| Email, calendar, maps and SMS integrations | Encrypted organisation-scoped service connections support SMTP, Microsoft 365, Gmail, Google Calendar, Outlook, Google Maps, OpenStreetMap and pluggable SMS providers with connection checks | `ServiceIntegrationController`, encrypted credential casts; provider validation/encryption/isolation tests |
| Public API | Authentication uses Sanctum; 261 `/api/v1` routes expose all major current-scope domains for websites, mobile clients, third parties and internal products | `routes/api.php`, [CORE_API_V1.md](CORE_API_V1.md), feature suite |
| Future modules | Auctions, specialist lettings/leases, facilities, AI features, customer-role portals and mobile applications are retained as roadmap candidates | Deliberately excluded because the source labels them “Potential future modules” |

## Verification record

| Check | Result |
| --- | --- |
| Full automated suite | 670 passed, 32 skipped, 2,338 assertions |
| Versioned API inventory | 261 `/api/v1` routes |
| Dependency advisory scan | `composer audit --locked`: no security vulnerability advisories |
| Database coverage | Feature tests use Laravel database refresh/migration support across the schema |
| Tenant and access controls | Cross-organisation rejection is exercised throughout core feature tests |
| Source hygiene | `git diff --check` clean |

The 32 skips are existing Filament component placeholders whose expected Livewire
test classes are not part of the installed panel set; they are not failures in the
real-estate API suite. The active feature and unit tests complete without failures.

