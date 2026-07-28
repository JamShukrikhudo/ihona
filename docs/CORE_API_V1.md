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
