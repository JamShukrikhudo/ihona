# Core Agency API v1

The authenticated REST API exposes the central estate-agency records under
`/api/v1`. Authenticate with a Laravel Sanctum token and select an organisation
(`current_team_id`) before making requests.

## Resources

| Resource | Endpoints | Notable filters |
|---|---|---|
| Contacts | Full CRUD at `/contacts` | `type`, `status`, `branch_id`, `company_id` |
| Companies | Full CRUD at `/companies` | `type`, `branch_id` |
| Tasks | Full CRUD at `/tasks` | `status`, `priority`, `assigned_to`, `branch_id` |
| Offers | Full CRUD at `/offers` | `property_id`, `contact_id`, `status`, `negotiator_id` |
| Properties | Full CRUD at `/properties` | `status`, `property_type`, price, bedrooms, country |
| Branches | Full CRUD at `/branches` | Search by name, address, or email |
| Buyers | Full CRUD at `/buyers` | `status`, budget, bedrooms, type, location, postal codes, features |
| Tenants | Full CRUD at `/tenants` | Search by name, email, or phone |
| Tenancy agreements | Full CRUD at `/tenancy-agreements` | `property_id`, `tenant_id`, `landlord_id`, `status` |
| Documents | Full CRUD at `/documents` | `property_id`, `file_type`, `is_signable` |
| Maintenance | Full CRUD at `/maintenance` | `property_id`, `tenant_id`, `contractor_id`, `status`, `priority` |
| Sales progression | Full CRUD at `/sales-progressions` | `property_id`, `agent_id`, `stage` |
| Valuations | Full CRUD at `/valuations` | `property_id`, `valuation_type`, `status`, `user_id` |
| Viewings | Full CRUD at `/viewings` | `property_id`, `agent_id`, `staff_id`, `status` |
| Inspections | Full CRUD at `/inspections` | `property_id`, `tenant_id`, `type`, `status` |
| Communications | Full CRUD at `/communications` | `channel`, `direction`, `status` |

## Organisation setup

The first-time setup API is available at:

- `GET /setup/options` for supported country packs and their defaults.
- `GET /setup/status` for the current organisation's onboarding state.
- `PUT /setup` to create or update the organisation profile and complete setup.

Country packs configure the organisation's currency, locale, language, timezone,
date format, measurement system, area unit, postal-code pattern, and available
property portals. Supported packs are GB, IE, AU, CA, US, ZA, and NZ.

## Global search

`GET /search?q={term}` searches properties, contacts, companies, documents,
viewings, tasks, offers, maintenance requests, and staff. Use repeated
`types[]` parameters to restrict record types and `limit` to control the maximum
results per type.

## Reporting

- `GET /reports/dashboard` returns organisation KPIs for properties, contacts,
  offers, viewings, tasks, maintenance, active sales, and valuations.
- `GET /reports/pipeline` returns grouped property, offer, sales progression,
  valuation, and maintenance pipeline totals.
- `/saved-reports` stores reusable dashboard or pipeline definitions with date
  filters, selected columns, sharing controls, and chart types.
- `GET /saved-reports/{report}/run` returns chart-ready labels and values.
- `GET /saved-reports/{report}/export` downloads a tenant-authorised CSV.
- `/dashboard-layouts` stores per-user widget layouts. Supported widgets include
  KPIs, calendar, tasks, recent activity, sales, lettings, leads, property
  pipeline, maintenance, and branch statistics. Setting a new default clears
  the user's previous default layout.

## Property matching

- Buyer requirements are managed through `/buyers`, including budget, bedroom,
  bathroom, area, type, location, postal-code, and feature preferences.
- `POST /buyers/{buyer}/generate-matches` scores available properties for one applicant.
- `POST /properties/{property}/generate-matches` scores active applicants for a listing.
- `GET /property-matches` filters persisted matches by buyer, property, status,
  and minimum score.
- `PATCH /property-matches/{match}` records interest, dismissal, viewing state,
  and agent notes.

Generated matches are organisation-scoped, de-duplicated, and create in-app
notifications for buyers linked to a user account.

## Lettings lifecycle

Tenant records and tenancy agreements are exposed through the versioned API.
Agreements track rent, payment frequency, protected-deposit scheme/reference,
signatures, terms, and status.

- `POST /tenancy-agreements/{agreement}/notice` records landlord, tenant, or
  mutual notice and its effective dates.
- `POST /tenancy-agreements/{agreement}/renew` creates a linked renewal,
  preserves the original agreement, and resets signature state.

Renewal, notice, ended, and terminated states support the full tenancy lifecycle
without introducing bookkeeping into this application.

## Automation and notifications

Automation rules are managed through full CRUD at `/automations`. Each rule has
a trigger name, optional context conditions, one or more actions, and an active
state. Supported actions currently create assigned tasks, send in-app
notifications, and update a property status.

- `POST /automations/{automation}/run` executes a rule against supplied event context.
- `GET /automation-runs` exposes completed, skipped, and failed execution audits.
- `GET /notifications` lists the authenticated user's notifications and unread count.
- `PATCH /notifications/{notification}/read` marks one notification as read.
- `POST /notifications/read-all` clears the user's unread notification state.

Rules and execution records are organisation-scoped. Action targets are checked
against organisation membership before any data is changed.

## Permissions and API tokens

Every authenticated v1 request is authorised against the selected organisation.
Permissions use `{resource}.{action}` names, with the actions `view`, `create`,
`edit`, `delete`, `export`, `approve`, `publish`, and `archive`.

- Owners and organisation admins have unrestricted access.
- Editors default to view, create, and edit.
- Members default to view-only access.
- Explicit membership permissions override role defaults and support
  resource-specific grants.
- `GET /permissions/catalog` returns all assignable permissions.
- `GET /permissions/members` lists roles and overrides.
- `PUT /permissions/members/{member}` updates a member's role and grants.

Users can list, create, and revoke their own scoped Sanctum credentials at
`/api-tokens`. The plaintext token is returned only once when it is created;
abilities and optional expiry dates are persisted with the token.

## Property portals

`/portal-integrations` provides unified, organisation-scoped configuration for
country-specific portals such as Rightmove, Zoopla, OnTheMarket, Domain,
realestate.com.au, Zillow, Realtor.com, and Trade Me Property. Credentials are
encrypted at rest and never serialized in API responses.

- `PUT /portal-integrations/{integration}/properties/{property}` queues a
  property for publication.
- `DELETE /portal-integrations/{integration}/properties/{property}` withdraws it.
- `POST /portal-integrations/{integration}/sync` generates and synchronizes the
  current listing payload.
- `GET /portal-sync-runs` exposes processed, successful, failed, and partial
  synchronization audits with per-listing errors.

Portal payloads contain public listing and media information only; internal
notes and credentials are excluded. Integrations support branch, sales/lettings
channel, country, frequency, active state, and provider-specific settings.

## Accounting boundary

`/accounting-integrations` configures Liberu Accounting, QuickBooks Online,
Xero, or Sage connections. Provider credentials are encrypted and omitted from
responses. `/accounting-links` associates agency customers, suppliers, invoice
references, and payment references with external identifiers.

- Links store only external references, payment state, due date, currency,
  amount, and provider metadata.
- `POST /accounting-integrations/{integration}/sync` records a reconciliation run.
- `GET /accounting-sync-runs` exposes synchronization audits.
- `GET /accounting-summary` returns referenced totals by currency and payment
  status, overdue counts, and outstanding amounts.

No journals, ledgers, bank reconciliation, tax filing, or other bookkeeping is
performed in this application.

## Diary and task collaboration

`GET /calendar` returns a single chronological feed of viewings, valuations,
inspections, maintenance dates, and task deadlines. It accepts `start`, `end`,
repeated `types[]`, and `staff_id` filters and returns FullCalendar-compatible
event identifiers, start/end values, all-day state, status, and property links.

Tasks support collaboration through:

- `GET|POST /tasks/{task}/comments`
- `DELETE /tasks/{task}/comments/{comment}`
- `GET|POST /tasks/{task}/attachments`
- `DELETE /tasks/{task}/attachments/{attachment}`
- `PATCH /tasks/{task}/checklist/{item}`

Uploaded files use private application storage. All calendar queries, comments,
attachments, and checklist mutations are constrained to the selected
organisation.

## Document workflow

Documents support immutable, private file versions and auditable signatures:

- `GET|POST /documents/{document}/versions`
- `GET /documents/{document}/versions/{version}/download`
- `GET|POST /documents/{document}/signatures`

Each upload records its sequential version, original filename, MIME type, size,
SHA-256 checksum, uploader, and optional notes. Downloads are authorised through
the API rather than public storage. Signatures are only accepted for signable
documents and record the signer, exact document version, time, IP address, and
user agent. Documents, versions, downloads, and signatures are constrained to
the selected organisation.

## Public website integration

Agency websites can consume published data without an authenticated back-office
session:

- `GET /api/v1/public/agencies/{team}/properties`
- `GET /api/v1/public/agencies/{team}/properties/{property}`
- `GET /api/v1/public/agencies/{team}/branches`
- `GET /api/v1/public/agencies/{team}/staff`

Property lists support featured, new, sold, property type, price, bedroom, and
text-search filters. Draft, withdrawn, archived, and other internal records are
never returned.

Properties and every resource listed above expose full create, read, update, and
delete operations. List endpoints accept `search`, `filter[field]`, and `per_page` (maximum 100).
Responses use Laravel's paginator format. Single records are returned under a
`data` key.

All queries and related-record validation are constrained to the authenticated
user's current team. A record belonging to another team is returned as `404`;
cross-team relationship IDs fail validation with `422`.

## Example

```http
POST /api/v1/contacts
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "buyer",
  "first_name": "Amina",
  "last_name": "Khan",
  "emails": ["amina@example.com"],
  "tags": ["first-time-buyer"]
}
```
