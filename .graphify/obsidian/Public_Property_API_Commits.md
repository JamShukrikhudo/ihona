# Public Property API Commits

> 64 nodes

## Key Concepts

- **c495db8 Wire the tamper-evident audit trail to Property price/status and Offer changes** (16 connections) — `git`
- **3aa7f41 Registration intent -> real roles, and SSO between Nuxt and the /app panel** (9 connections) — `git`
- **9d913ce Add deal_type (sale/rent) to properties — a real column, not display-only** (9 connections) — `git`
- **PublicPropertyResource.php** (8 connections) — `modules/real-estate-properties-api/src/Http/Resources/PublicPropertyResource.php`
- **34c3786 Add property view counts — dedup'd per visitor per day, not a raw hit counter** (8 connections) — `git`
- **944f698 Add hunting-lodge property type; close the has_generator/altitude schema gap** (8 connections) — `git`
- **DatabaseSeeder.php** (7 connections) — `database/seeders/DatabaseSeeder.php`
- **PublicPropertyController.php** (7 connections) — `modules/real-estate-properties-api/src/Http/Controllers/PublicPropertyController.php`
- **4937b90 Add bounding-box filter to the public properties endpoint** (6 connections) — `git`
- **aa2ae5a Areas 1 & 3 (host half): territory seeder + host/sales_agent roles** (6 connections) — `git`
- **f947db1 Add public, unauthenticated GET /v1/public/properties (+ /:id)** (6 connections) — `git`
- **PropertySeeder.php** (5 connections) — `database/seeders/PropertySeeder.php`
- **24e84da Add identity-core-api: Sanctum Bearer token issuance for the Nuxt frontend** (5 connections) — `git`
- **51d07bc Add PropertySeeder: 5 real test properties for the public API to serve** (5 connections) — `git`
- **67c07d7 Add a public, unauthenticated GET /v1/public/territories endpoint** (5 connections) — `git`
- **2ba537d Grant host/sales_agent roles property CRUD permissions** (4 connections) — `git`
- **413ee37 Add cian-style room-count chips to the public properties filter** (4 connections) — `git`
- **58d6004 Fix: define the 'api' rate limiter — every throttle:api route 500s without it** (4 connections) — `git`
- **9e7241e Add cian-style sort to the public properties endpoint** (4 connections) — `git`
- **daab577 Fix all 21 Overview stub pages colliding on the same /overview URL** (4 connections) — `git`
- **ecdb09f Add POST /api/v1/auth/register — real self-registration for the frontend** (4 connections) — `git`
- **RealEstateRolesSeeder.php** (3 connections) — `database/seeders/RealEstateRolesSeeder.php`
- **2026_09_04_190000_add_signup_intent_to_users_table.php** (3 connections) — `modules/profiles/database/migrations/2026_09_04_190000_add_signup_intent_to_users_table.php`
- **PublicPropertyController** (3 connections) — `modules/real-estate-properties-api/src/Http/Controllers/PublicPropertyController.php`
- **PublicPropertyResource** (3 connections) — `modules/real-estate-properties-api/src/Http/Resources/PublicPropertyResource.php`
- *... and 39 more nodes in this community*

## Relationships

- [[Media Document Module Commits]] (20 shared connections)
- [[Property Favorites Commit History]] (7 shared connections)
- [[Auth Views and Panel Commit History]] (6 shared connections)
- [[Real Estate Feature Commit History]] (5 shared connections)
- [[Admin Panel Localization Commits]] (4 shared connections)
- [[Property Create/Update Commits]] (3 shared connections)
- [[Jetstream Blade Component Library]] (2 shared connections)
- [[Viewing and Valuation Lifecycle Actions]] (2 shared connections)
- [[Instruction and Offer Actions]] (2 shared connections)
- [[Property Financial Calculators]] (2 shared connections)
- [[Community 581]] (1 shared connections)
- [[Telegram Login Bridge]] (1 shared connections)

## Source Files

- `database/seeders/DatabaseSeeder.php`
- `database/seeders/PropertySeeder.php`
- `database/seeders/RealEstateRolesSeeder.php`
- `database/seeders/SignupRolesSeeder.php`
- `database/seeders/TerritorySeeder.php`
- `git`
- `modules/audit-filament/src/Resources/ActivityLogResource/Pages/ListActivityLogs.php`
- `modules/audit-filament/src/Resources/ActivityLogResource/Pages/ViewActivityLog.php`
- `modules/profiles/database/migrations/2026_09_04_190000_add_signup_intent_to_users_table.php`
- `modules/real-estate-core-api/src/Http/Controllers/PublicTerritoryController.php`
- `modules/real-estate-properties-api/src/Http/Controllers/PublicPropertyController.php`
- `modules/real-estate-properties-api/src/Http/Resources/PublicPropertyResource.php`
- `modules/real-estate-properties/database/migrations/2026_08_31_000001_add_deal_type_to_properties.php`
- `modules/real-estate-properties/database/migrations/2026_09_03_000001_add_regional_amenities_to_properties.php`
- `modules/real-estate-properties/database/migrations/2026_09_03_000002_add_views_count_to_properties.php`
- `modules/real-estate-properties/src/Domain/DealType.php`
- `tests/Feature/PublicPropertyBboxTest.php`

## Audit Trail

- EXTRACTED: 208 (100%)
- INFERRED: 0 (0%)
- AMBIGUOUS: 0 (0%)

---

*Part of the graphify knowledge wiki. See [[index]] to navigate.*