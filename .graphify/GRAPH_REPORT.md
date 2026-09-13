# Graph Report - .  (2026-09-10)

## Corpus Check
- label mode - file stats not available

## Summary
- 7605 nodes · 12887 edges · 599 communities detected
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 178 edges (avg confidence: 0.78)
- Token cost: 0 input · 0 output
- Edge kinds: MODIFIES: 6479 · method: 2337 · contains: 1217 · references: 905 · ON_BRANCH: 484 · implements: 464 · PARENT_OF: 432 · calls: 295 · conceptually_related_to: 152 · semantically_similar_to: 55 · cites: 43 · shares_data_with: 24


## Graph Freshness
- Built from Git commit: `87e83fe`
- Compare this hash to `git rev-parse HEAD` before trusting freshness-sensitive graph output.
## God Nodes (most connected - your core abstractions)
1. `Property` - 70 edges
2. `liberu-module Composer package type` - 40 edges
3. `Implementation Summary` - 32 edges
4. `ThemeManager` - 30 edges
5. `real-estate-default theme` - 30 edges
6. `base theme (Liberu Base)` - 24 edges
7. `Liberu Module Manager` - 22 edges
8. `Liberu Real Estate scope` - 22 edges
9. `Livewire adapter tier` - 22 edges
10. `default theme (Liberu Default)` - 22 edges

## Surprising Connections (you probably didn't know these)
- `Replaceable provider credentials and transport` --semantically_similar_to--> `Safe theme fallback`  [INFERRED] [semantically similar]
  projects/real-estate/integrations/README.md → themes/base/README.md
- `Session handoffs in docs/handoffs/` --conceptually_related_to--> `lerd local PHP development environment`  [AMBIGUOUS]
  CLAUDE.md → AGENTS.md
- `lerd local PHP development environment` --semantically_similar_to--> `Container deployment topology`  [INFERRED] [semantically similar]
  AGENTS.md → docker-compose.yml
- `'planned' as an explicit backlog marker` --semantically_similar_to--> `Unpublished theme preview image`  [INFERRED] [semantically similar]
  projects/real-estate/core/README.md → themes/base/README.md
- `Presentation-only package boundary` --semantically_similar_to--> `Framework-neutral core boundary`  [INFERRED] [semantically similar]
  themes/base/README.md → projects/real-estate/REAL-ESTATE.md

## Communities

### Community 0 - "Common CRUD Action Verbs"
Cohesion: 0.00
Nodes (1): decbeb1 Локальные изменения перед обновлением

### Community 1 - "Property Financial Calculators"
Cohesion: 0.01
Nodes (56): AnalyzePropertyInvestment, CalculateCostOfMoving, CalculateStampDuty, CanAccessMediaDocument, CreateDocumentVersion, CreateHomeReport, RequestViewingFeedback, SignMediaDocument (+48 more)

### Community 2 - "Lettings and Viewings Actions"
Cohesion: 0.03
Nodes (28): CancelViewing, CreateContact, CreateLeaseAgreement, CreateRentalApplication, CreateWorkOrder, DeletePriceAlert, RecordCommunication, RecordWorkOrderUpdate (+20 more)

### Community 3 - "Jetstream Blade Component Library"
Cohesion: 0.01
Nodes (21): 1e7a367 Revert remaining unpublished modules/ and Jetstream component edits, 75f7818 Bootstrap application from Liberu Laravel Boilerplate, CurrencyMismatch, DependencyResolutionFailed, InvalidManifest, InvalidTheme, UnknownCurrency, RoledUser (+13 more)

### Community 4 - "Valuation and Party Actions"
Cohesion: 0.02
Nodes (48): CalculateHomeValuation, CreatePartyRelationship, DefineStatus, DeletePartyRelationship, GeneratePropertyBrochure, ManagePartyConsent, NextNumber, RecordAuditEntry (+40 more)

### Community 5 - "Instruction and Offer Actions"
Cohesion: 0.02
Nodes (47): CreateInstruction, CreateMatchProfile, CreateOffer, DeleteInstruction, DeleteListing, DeleteMatchProfile, DeleteOffer, DeleteSalesProgression (+39 more)

### Community 6 - "Viewing and Valuation Lifecycle Actions"
Cohesion: 0.02
Nodes (50): CalculateComparables, CompleteValuation, CompleteViewing, ConfirmViewing, ConvertValuation, CreateAgency, CreateBranch, CreateTerritory (+42 more)

### Community 7 - "Real Estate Feature Commit History"
Cohesion: 0.03
Nodes (131): main, 000ccd5 fix: close the media route, and eight more review findings, 0094406 Add contractor maintenance workflow API, 052e63b Update dependencies, 056e34b Fix PermissionsSeeder to use shield:generate instead of missing permissions:sync command, 0755a5b feat(design): acceptance sweep across every public page, 086ae2d feat(listings): pair a result with its pin, 0a16901 fix(design): the filter fix reached only one of two callers (+123 more)

### Community 8 - "Auth Views and Panel Commit History"
Cohesion: 0.04
Nodes (31): 0812f9b Update main workflow Docker with prerequisite of tests and update Dockerfile, 0cf7b7c Fix permissions seeder completion, 1b76115 fix(nav): give the top navigation room and let its dropdowns be clicked, 2c45d44 Remove maxContentWidth('full') — tables were stretching edge-to-edge, 30d2b7d Add socialstream, Docker/k8s upgrades, security improvements, and expanded test coverage, 42d4a59 Update dependencies, 45479e6 Merge pull request #1301 from liberusoftware/panel-access-and-dashboards, 4b07ee8 Update main workflow Docker with prerequisite of tests and update Dockerfile (+23 more)

### Community 9 - "Listing and Sync Creation Actions"
Cohesion: 0.02
Nodes (26): CreateListing, CreateMarketingCampaign, CreateOnTheMarketSync, CreateRightmoveSync, CreateSalesProgression, CreateZooplaSync, DeleteOnTheMarketSync, DeleteRightmoveSync (+18 more)

### Community 10 - "Letting and Management Record Actions"
Cohesion: 0.03
Nodes (21): CreateLetting, CreateManagementRecord, RecordLettingFailure, RecordManagementFailure, TransitionLetting, TransitionManagementRecord, UpdateLettingDetails, UpdateManagementDetails (+13 more)

### Community 11 - "Marketing Campaign Lifecycle"
Cohesion: 0.03
Nodes (22): DeleteMarketingCampaign, UpdateMarketingCampaign, 4fd850d Add real estate marketing module surfaces, 5eb4712 Harden real estate Filament boundaries, CreateLetting, CreateManagementRecord, CreateMarketingCampaign, CreateOnTheMarketSync (+14 more)

### Community 12 - "Theme Package Manifests"
Cohesion: 0.06
Nodes (72): liberusoftware/boilerplate-scripts, Optional capability: blog.publish, Optional capability: foundation.localization, Optional capability: foundation.theme-support, Child-theme relative CSS import across install dirs, Component repositories are the source of truth, storage/app/coverage.tsv, Liberu Meta Repository Scripts README (+64 more)

### Community 13 - "Real Estate OpenAPI Contracts"
Cohesion: 0.06
Nodes (70): OpenAPI v1: Real Estate OnTheMarket Sync API, OpenAPI v1: Real Estate Parties API, OpenAPI v1: Real Estate Portals and Reporting API, OpenAPI v1: Real Estate Properties API, Archive Instead of Hard Delete, Shared Error Schema, Core/API/Filament/Livewire Package Split, Host-attached Filament plugin (+62 more)

### Community 14 - "Media Document Module Commits"
Cohesion: 0.07
Nodes (50): CreateMediaDocument, DeleteMediaDocument, UpdateMediaDocument, ihona-tj-localization-business-domain, 0910e2b Add real estate media and documents core module, 0c8f995 Wire the valuations/matching Filament plugins into the admin panel, 0d7e580 Actually give host/sales_agent somewhere to use their permissions, 1331232 Include modules and themes in coverage reporting (#1308) (+42 more)

### Community 15 - "Public Property API Commits"
Cohesion: 0.04
Nodes (28): 24e84da Add identity-core-api: Sanctum Bearer token issuance for the Nuxt frontend, 255fad3 Remove the audit stub Overview page, replaced by ActivityLogResource, 2ba537d Grant host/sales_agent roles property CRUD permissions, 34c3786 Add property view counts — dedup'd per visitor per day, not a raw hit counter, 3aa7f41 Registration intent -> real roles, and SSO between Nuxt and the /app panel, 413ee37 Add cian-style room-count chips to the public properties filter, 4937b90 Add bounding-box filter to the public properties endpoint, 51d07bc Add PropertySeeder: 5 real test properties for the public API to serve (+20 more)

### Community 16 - "Mortgage and Valuation Calculators"
Cohesion: 0.09
Nodes (22): CalculateMortgage, CalculateRentalYield, RankPropertyRecommendations, 118b084 Merge pull request #1352 from liberusoftware/module-real-estate-v5-conformance, 25436b4 Add explainable property valuation estimates, 27f1f8e Fix silent-keep-ours gaps the merge left behind (deep sweep, not just tests), 4793429 Разрешил конфликт в Property.php, 5c5fb52 Merge pull request #1353 from liberusoftware/module-real-estate-v5-conformance (+14 more)

### Community 17 - "Liberu Module Package Registry"
Cohesion: 0.05
Nodes (62): Liberu Activity and Comments, liberusoftware/analytics-contracts, Liberu Analytics Core, Liberu Google Analytics, Liberu Meta Server-Side Tracking, Liberu API Access, Liberu Application Core, Liberu Audit (+54 more)

### Community 18 - "Property Create/Update Commits"
Cohesion: 0.05
Nodes (21): CreateProperty, UpdateProperty, 042b50d Carry legacy property categories into modular surfaces, 113d034 Wire deal_type and territory_id into the admin property form, 11a01a0 Carry legacy property templates into modular surfaces, 1cfcbdd Merge remote-tracking branch 'origin/main' into module-real-estate-v5-conformance, 21731e3 Carry legacy property metadata into modular surfaces, 21f3633 Align category capability contracts (+13 more)

### Community 19 - "Property Favorites Commit History"
Cohesion: 0.15
Nodes (42): TogglePropertyFavorite, 0beacf8 Restore safe property ordering, 1366b8d Merge pull request #1321 from liberusoftware/module-real-estate-v5-conformance, 16b1d83 Merge remote-tracking branch 'origin/main' into module-real-estate-v5-conformance, 18f6bd2 Merge pull request #1325 from liberusoftware/module-real-estate-v5-conformance, 1e749b7 Preserve branch associations in property modules, 2656a9a Merge pull request #1330 from liberusoftware/module-real-estate-v5-conformance, 358b4f4 Merge pull request #1337 from liberusoftware/module-real-estate-v5-conformance (+34 more)

### Community 20 - "Property Model Behavior"
Cohesion: 0.04
Nodes (1): Property

### Community 21 - "Foundation Module Capabilities"
Cohesion: 0.10
Nodes (52): CacheModulesCommand, Capability: foundation.localization, Capability: foundation.localization.livewire, Capability: foundation.module-manager.filament, Capability: foundation.modules, Capability: foundation.notifications, Capability: foundation.observability, Capability: foundation.organizations (+44 more)

### Community 22 - "Admin Panel Localization Commits"
Cohesion: 0.11
Nodes (15): 075a847 Translate the Listings table columns, tune Horizon, and guard tests against production, 09c319f Restyle 20 admin resource forms from flat field lists into Sections, 1fe858d Localize admin panel: Russian model labels across all Filament resources, 478c4ca Give NewsArticleResource an actual CRUD — it had no Pages at all, 58ac3ca Add missing profile fields to the User edit form and group it into sections, 8b7d98a Translate every remaining Filament resource form's field labels, 9ab3949 Translate table column labels across all remaining Filament resources, a2ad771 Stop UserSeeder echoing the admin password into test output (+7 more)

### Community 23 - "Real Estate Livewire List Adapters"
Cohesion: 0.04
Nodes (14): 3172546 Add valuations Filament and Livewire adapters, 72d35f5 Add media documents Filament and Livewire adapters, d07b218 Harden real estate Livewire list surfaces, OnTheMarketSyncList, RightmoveSyncList, ZooplaSyncList, CreateValuation, EditMediaDocument (+6 more)

### Community 24 - "Property Deletion and Tax Commits"
Cohesion: 0.06
Nodes (21): DeleteProperty, RemovePropertyFavorite, 09ddb06 Complete saved property wishlist parity, 0a06ec8 Merge pull request #1347 from liberusoftware/module-real-estate-v5-conformance, 100c1f6 Merge pull request #1349 from liberusoftware/module-real-estate-v5-conformance, 2affb83 Lock published module package names, 503393a Add modular property tax estimates, 6b45813 Merge pull request #1354 from liberusoftware/module-real-estate-v5-conformance (+13 more)

### Community 25 - "Viewing Module Surface"
Cohesion: 0.05
Nodes (12): CreateViewing, DeleteViewing, UpdateViewing, 6ad060b Add real estate viewings module surfaces, 96d3932 Fix viewing/booking requests filing under the requester's own team, CreateViewing, EditViewing, ListViewings (+4 more)

### Community 26 - "Foundation Capability Declarations"
Cohesion: 0.09
Nodes (39): Capability: foundation.authorization, Capability: foundation.authorization.filament, Capability: foundation.scheduler-queues, Capability: foundation.search, Capability: foundation.search.api, Capability: foundation.sessions-devices.filament, Capability: foundation.settings, Capability: foundation.settings.filament (+31 more)

### Community 27 - "Lettings Domain Specification"
Cohesion: 0.10
Nodes (38): Adapter Over One Matching Core Package, Capability: foundation.teams, Unprefixed Composer Name vs module- GitHub Repository, Idempotency-Key Header, Instruction Record, Instruction Status Lifecycle, laravel/jetstream, Letting Lifecycle Capabilities (+30 more)

### Community 28 - "Home Report and Dashboard Controllers"
Cohesion: 0.06
Nodes (10): UpdateHomeReportConditions, d8625fd Fix release validation and API resource boundaries, f5a9117 Merge remote main before v6.0.0 release, DashboardLayoutController, CalendarEntryResource, CommunicationResource, DashboardLayoutResource, HomeReportResource (+2 more)

### Community 29 - "Portal Report Module Surface"
Cohesion: 0.06
Nodes (10): CreatePortalReport, DeletePortalReport, 3c98a2a Add real estate portals reporting module surfaces, CreatePortalReport, EditPortalReport, ListPortalReports, PortalsReportingApiServiceProvider, PortalsReportingFilamentServiceProvider (+2 more)

### Community 30 - "Party Module Surface"
Cohesion: 0.06
Nodes (10): CreateParty, DeleteParty, UpdateParty, 73659b3 Add real estate parties module surfaces, CreateParty, EditParty, ListParties, PartiesApiServiceProvider (+2 more)

### Community 31 - "ThemeManager Service"
Cohesion: 0.11
Nodes (1): ThemeManager

### Community 32 - "Composer Package Boundary Rules"
Cohesion: 0.15
Nodes (29): Missing config.allow-plugins across packages, Host architecture boundary rules, Root composer install clobbers tracked packages, liberusoftware/composer-installer, docs/CONFORMANCE.md, GitHub Issues as the issue tracker, Handoffs live in docs/handoffs/, scripts/migrate-testbench (+21 more)

### Community 33 - "Capability Definitions per Module"
Cohesion: 0.09
Nodes (11): 10d5d92 Pin module sources to HTTPS conformance commits, 12bd6e1 Conform real estate modules to open issue capabilities (#1311), 799806e Conform real estate modules to open issue capabilities, InstructionsCapabilityDefinition, ListingsCapabilityDefinition, MarketingCapabilityDefinition, OffersCapabilityDefinition, PartiesCapabilityDefinition (+3 more)

### Community 34 - "Role-Aware Dashboard Seeders"
Cohesion: 0.10
Nodes (9): 10d943c Update UserSeeder and RoleSeeder, 418571f Implement role-aware dashboards and workspace UX, 8d87245 Merge pull request #1357 from liberusoftware/release/v7.1.0, Dashboard, RolesSeeder, TeamSeeder, UserSeeder, AdminOverviewWidget (+1 more)

### Community 35 - "Theme System Conventions"
Cohesion: 0.26
Nodes (24): Deterministic theme builds, liberu-theme Composer package, Semantic design tokens, Theme accessibility requirements, liberusoftware/theme-base, Theme Blade directives, liberusoftware/theme-clear-signal, liberusoftware/theme-dark (+16 more)

### Community 36 - "Real Estate Theme Integration"
Cohesion: 0.15
Nodes (5): 2d8dfb2 Integrate real estate theme and Packagist Composer sources (#1306), f514669 Integrate real estate theme and Packagist Composer sources, menu, menuButton, RealEstateDefaultThemeServiceProvider

### Community 37 - "Telegram Login Bridge"
Cohesion: 0.10
Nodes (10): VerifyTelegramAuthPayload, 232e622 Bridge Google/Telegram login into the Nuxt SPA's Sanctum auth, 481c8cf Enable Google sign-in and add Telegram Login Widget, 4f68b2c Restyle the social login block to match the site's own design tokens, 6fa1645 Fix Telegram login: route collision and missing previous_url, d5456a6 Sync new registrations into the standalone liberu CRM as leads, TelegramAuthController, SyncNewOAuthUserToCrm (+2 more)

### Community 38 - "Real Estate Marketing/Listings Adapters"
Cohesion: 0.15
Nodes (22): Composer Unprefixed / GitHub module- Prefix, Composer name unprefixed, GitHub repo carries module- prefix, Offer qualification, negotiation, proof and decision history, Filament 5 Resource Adapter, Livewire 4 List Adapter, Provider-Neutral Domain, Replaceable Adapters, Real Estate Listings Filament, Real Estate Listings Livewire (+14 more)

### Community 39 - "Notifications and Broadcast Design"
Cohesion: 0.21
Nodes (22): ActivityNotification, Admin user-management enhancement, Alternative broadcast services, Pusher broadcasting driver, Browser push notifications, Admin dashboard widgets, Filament Shield role-based access, FriendRequestNotification (+14 more)

### Community 40 - "Messaging API Architecture"
Cohesion: 0.26
Nodes (21): Alpine.js for the messaging UI, Separate API (Sanctum) and web (session) routes, Conversation query indexes, CSRF protection on state-changing requests, Layered authorization, MessageController, Message encryption at rest, Message Model (+13 more)

### Community 41 - "Search Module Architecture"
Cohesion: 0.26
Nodes (20): Advanced search across users, posts and groups, Eager loading to avoid N+1, Full-text indexes, Group model, liberu-module Composer package, modules/search package, liberusoftware/module-search-demo, Enforced pagination limits (+12 more)

### Community 42 - "Valuation API Module"
Cohesion: 0.11
Nodes (6): CreateValuation, DeleteValuation, UpdateValuation, 188c376 Add real estate valuations API adapter, 44c2353 Add real estate valuations core module, ValuationsApiServiceProvider

### Community 43 - "Filament Module Plugin System"
Cohesion: 0.20
Nodes (19): Admin Filament panel, App Filament panel, Contract packages, filament_plugins manifest declaration, Companion *-filament presentation module, Capability declarations (provides / requires), Module categories, Explicit module enablement (+11 more)

### Community 44 - "Conformance Naming Decisions"
Cohesion: 0.16
Nodes (19): ADR exceptions: none (§6), Atomic commit does not extend to verification, 48-of-48 divergence audit, foundation-filament dissolved, Migration sequence (steps −1 to 9), Module-repo-first source of truth (§3.1), Namespace decisions (§3.5), Six out-of-scope packages exiled (§3.4) (+11 more)

### Community 45 - "Public Property Controller"
Cohesion: 0.11
Nodes (1): PropertyController

### Community 46 - "Developer Experience Conformance"
Cohesion: 0.16
Nodes (18): Blocker: theme discovery is not Composer-driven, Capability: foundation.developer-experience, Conformance Step 5 — Test Redistribution, Liberu Developer Experience, docs/CONFORMANCE.md — Conformance Plan, FoundationDoctorCommand, Handoff: Conformance Step 4 (Testbench Migration), Handoff: Conformance Step 5 (Test Redistribution) (+10 more)

### Community 47 - "Module Manifest Parser"
Cohesion: 0.11
Nodes (1): Manifest

### Community 48 - "Team Policy"
Cohesion: 0.12
Nodes (1): TeamPolicy

### Community 49 - "Public Property List Livewire"
Cohesion: 0.20
Nodes (1): PropertyList

### Community 50 - "Conformance Audit Findings"
Cohesion: 0.17
Nodes (16): Audit sliced by catching mechanism, Package CI: three workflows, not three jobs (§3.9), Code-level conformance audit, Per-package coverage ratchet, Finding ranks and the security flag, Per-package PHPStan level ratchet, Code-level conformance audit, scripts/measure-coverage and set-coverage-thresholds (+8 more)

### Community 51 - "Real Estate Zoopla Adapter Tier"
Cohesion: 0.17
Nodes (15): API Error Schema, Shared PaginationMeta Schema, Four Implementation Indexes (core / api / filament / livewire), Liberu API Modules Scope, Liberu Application Composition, Liberu Platform Scope, Versioned openapi/v1 Adapter Contract, Packagist Names Omit the module- Prefix (+7 more)

### Community 52 - "Real Estate Domain Scope Docs"
Cohesion: 0.23
Nodes (15): Real Estate capability: Lettings, Real Estate capability: Marketing, Real Estate capability: Property Management, Delivery phase 2: progression, portals, marketing, reporting, documents, Delivery phase 3: lettings, management, accounting, owner/tenant portals, Liberu Real Estate scope (REAL-ESTATE.md), Framework-neutral core boundary, One-to-one adapter rule (+7 more)

### Community 53 - "Valuation Controller"
Cohesion: 0.13
Nodes (1): ValuationController

### Community 54 - "User Model Authorization"
Cohesion: 0.18
Nodes (1): User

### Community 55 - "Translation Publishing Commits"
Cohesion: 0.14
Nodes (5): 1dd7a02 composer.lock: pin identity-core-api to the local path source, 32db7b5 Publish English translations (were missing entirely), 5c1379d Fix two real API/admin bugs found via functional testing, e1e37b3 composer.lock: resolve laravel-lang/common + laravel-lang/lang, PropertyCategory

### Community 56 - "ThemeManifest Value Object"
Cohesion: 0.15
Nodes (1): ThemeManifest

### Community 57 - "Agency Policy"
Cohesion: 0.14
Nodes (1): AgencyPolicy

### Community 58 - "Branch Policy"
Cohesion: 0.14
Nodes (1): BranchPolicy

### Community 59 - "Party Policy"
Cohesion: 0.14
Nodes (1): PartyPolicy

### Community 60 - "Portal Report Policy"
Cohesion: 0.14
Nodes (1): PortalReportPolicy

### Community 61 - "Property Category Policy"
Cohesion: 0.14
Nodes (1): PropertyCategoryPolicy

### Community 62 - "Property Policy"
Cohesion: 0.14
Nodes (1): PropertyPolicy

### Community 63 - "Property Template Policy"
Cohesion: 0.14
Nodes (1): PropertyTemplatePolicy

### Community 64 - "Role Policy"
Cohesion: 0.14
Nodes (1): RolePolicy

### Community 65 - "Sales Progression Policy"
Cohesion: 0.14
Nodes (1): SalesProgressionPolicy

### Community 66 - "Status Definition Policy"
Cohesion: 0.14
Nodes (1): StatusDefinitionPolicy

### Community 67 - "Territory Policy"
Cohesion: 0.14
Nodes (1): TerritoryPolicy

### Community 68 - "Valuation Policy"
Cohesion: 0.14
Nodes (1): ValuationPolicy

### Community 69 - "Localization Translation Files"
Cohesion: 0.15
Nodes (1): 11b55b8 Area 4: tg/uz localization files

### Community 70 - "Payment Gateway Contract"
Cohesion: 0.15
Nodes (4): 1d95836 Add a PaymentGateway contract for future booking prepayment, NullPaymentGateway, PaymentRequest, PaymentResult

### Community 71 - "Public Property Detail Livewire"
Cohesion: 0.27
Nodes (1): PropertyDetail

### Community 72 - "CLAUDE.md Architecture Notes"
Cohesion: 0.18
Nodes (13): Session handoffs in docs/handoffs/, Dual Filament panels, The host measures the host, A package must run against its own dependency tree, Reverb is installed but not wired, Packages are standalone-testable, Tenancy rules that bite, Known upgrade blockers (+5 more)

### Community 73 - "Instruction Policy"
Cohesion: 0.15
Nodes (1): InstructionPolicy

### Community 74 - "Listing Policy"
Cohesion: 0.15
Nodes (1): ListingPolicy

### Community 75 - "Marketing Campaign Policy"
Cohesion: 0.15
Nodes (1): MarketingCampaignPolicy

### Community 76 - "Match Profile Policy"
Cohesion: 0.15
Nodes (1): MatchProfilePolicy

### Community 77 - "Media Document Policy"
Cohesion: 0.15
Nodes (1): MediaDocumentPolicy

### Community 78 - "Offer Policy"
Cohesion: 0.15
Nodes (1): OfferPolicy

### Community 79 - "User Policy"
Cohesion: 0.15
Nodes (1): UserPolicy

### Community 80 - "Viewing Policy"
Cohesion: 0.15
Nodes (1): ViewingPolicy

### Community 81 - "Identity Filament Module Scope"
Cohesion: 0.26
Nodes (12): API module index, Capability boundary discipline, Capability: foundation.identity.filament, Core module index, Feature module index, filament/filament ^5.1, Filament module index, Liberu Identity Administration (identity-filament) (+4 more)

### Community 82 - "Property Dashboard Widgets"
Cohesion: 0.17
Nodes (4): ad3081e Add real dashboard widgets: property stats + territory/status breakdowns, PropertiesByStatusChart, PropertiesByTerritoryChart, RealEstateOverview

### Community 83 - "Viewing Controller"
Cohesion: 0.20
Nodes (1): ViewingController

### Community 84 - "Property Tax Estimator Action"
Cohesion: 0.42
Nodes (1): EstimatePropertyTax

### Community 85 - "Portal Sync Service Providers"
Cohesion: 0.18
Nodes (3): bd4b1dc Add independent property portal transport sync, OnTheMarketServiceProvider, ZooplaServiceProvider

### Community 86 - "Public Property Comparison Livewire"
Cohesion: 0.38
Nodes (1): PropertyComparison

### Community 87 - "Module Discovery and Enablement"
Cohesion: 0.24
Nodes (11): Manifest default_enabled, Installation never implies boot, Discovery and enablement derive from manifests (§3.6), Module system, config/modules.php env overrides, Contract packages (analytics-contracts, localization-contracts), localization-mymemory adapter, ModuleManagerServiceProvider (+3 more)

### Community 88 - "Real Estate Migrations Batch"
Cohesion: 0.20
Nodes (1): e3111ed Fix three merge-added migrations exceeding MySQL's 64-char identifier limit

### Community 89 - "Price Alert Manager Livewire"
Cohesion: 0.47
Nodes (1): PriceAlertManager

### Community 90 - "Composition Host Boundaries"
Cohesion: 0.24
Nodes (10): Composition host, Three foundation boundaries, Foundation capability → package matrix, Host boundary: /app is composition only, Foundation verification gates, Foundation compliance, Foundation module implementation matrix, module:validate, theme:validate, foundation:doctor, module:cache (+2 more)

### Community 91 - "Ops Tooling and Boost Guidelines"
Cohesion: 0.36
Nodes (8): Container deployment topology, Laravel Boost guidelines, lerd local PHP development environment, Driving optimisation from real traffic, lerd git worktree subdomains, Operations stack (Horizon, Octane, Telescope, Pulse), lerd twelve grouped MCP tools, App\Support\ThemeColors

### Community 92 - "Architecture Rule Consolidation"
Cohesion: 0.27
Nodes (10): A rule that cannot fire is not coverage, Architecture rules relocated (12 → 8), Shared Pint and PHPStan configs via --config, tests/Architecture/ModuleBoundariesTest, module.json manifest, App\Filament\ModulePlugins, ModuleValidationGuard, ModuleValidator (+2 more)

### Community 93 - "Media Document Controller"
Cohesion: 0.20
Nodes (1): MediaDocumentController

### Community 94 - "Party Controller"
Cohesion: 0.20
Nodes (1): PartyController

### Community 95 - "Rental Application Controller"
Cohesion: 0.36
Nodes (1): RentalApplicationController

### Community 96 - "Real Estate Rightmove Adapter Tier"
Cohesion: 0.27
Nodes (10): Real Estate Portal Integrations README, liberusoftware/real-estate-rightmove, liberusoftware/real-estate-rightmove-api, liberusoftware/real-estate-rightmove-filament, liberusoftware/real-estate-rightmove-livewire, Portal integrations are independently releasable, Portal provider: Rightmove, Replaceable provider credentials and transport (+2 more)

### Community 97 - "HomeReport Model"
Cohesion: 0.24
Nodes (1): HomeReport

### Community 98 - "MediaDocument Model"
Cohesion: 0.20
Nodes (1): MediaDocument

### Community 99 - "ActivityLogResource"
Cohesion: 0.20
Nodes (1): ActivityLogResource

### Community 100 - "ModuleRegistry Service"
Cohesion: 0.22
Nodes (1): ModuleRegistry

### Community 101 - "Match Score Calculator"
Cohesion: 0.42
Nodes (1): CalculateMatchScore

### Community 102 - "Localization Module Support"
Cohesion: 0.42
Nodes (9): LanguageSwitcher Livewire component, Localization module split, Multi-language support, SetLocale middleware, config('app.supported_locales'), translate:generate artisan command, Translation caching, TranslationService (MyMemory) (+1 more)

### Community 103 - "Lease Agreement Controller"
Cohesion: 0.39
Nodes (1): LeaseAgreementController

### Community 104 - "Match Profile Controller"
Cohesion: 0.22
Nodes (1): MatchProfileController

### Community 105 - "Offer Controller"
Cohesion: 0.22
Nodes (1): OfferController

### Community 106 - "Work Order Controller"
Cohesion: 0.39
Nodes (1): WorkOrderController

### Community 107 - "Lerd Dev Environment Config"
Cohesion: 0.22
Nodes (7): laravel-boost, lerd, lerd, php, /usr/local/bin/php, laravel-boost, lerd

### Community 108 - "PropertyHistory Model"
Cohesion: 0.25
Nodes (1): PropertyHistory

### Community 109 - "Account Setup Wizard"
Cohesion: 0.28
Nodes (1): AccountSetupWizard

### Community 110 - "NewsArticleResource"
Cohesion: 0.22
Nodes (1): NewsArticleResource

### Community 111 - "PropertyResource"
Cohesion: 0.28
Nodes (1): PropertyResource

### Community 112 - "Real Estate Zoopla API Tier"
Cohesion: 0.36
Nodes (8): API adapter tier, Adapters stay planned until core contracts exist, Real Estate API modules README, liberusoftware/real-estate-zoopla, liberusoftware/real-estate-zoopla-api, liberusoftware/real-estate-zoopla-filament, liberusoftware/real-estate-zoopla-livewire, Portal provider: Zoopla

### Community 113 - "Real Estate Properties Livewire Tier"
Cohesion: 0.39
Nodes (8): Livewire adapter tier, Real Estate capability: Properties, Real Estate Livewire modules README, liberusoftware/real-estate-properties, liberusoftware/real-estate-properties-api, liberusoftware/real-estate-properties-filament, liberusoftware/real-estate-properties-livewire, Real-estate theme extension points

### Community 114 - "Property Valuation Prediction Action"
Cohesion: 0.46
Nodes (1): GeneratePropertyValuation

### Community 115 - "Shield Policy Generation Commit"
Cohesion: 0.25
Nodes (1): b97f8da Generate Shield policies for the 13 previously-unregistered resources

### Community 116 - "Advanced Property Search Livewire"
Cohesion: 0.29
Nodes (1): AdvancedPropertySearch

### Community 117 - "Listing List Livewire"
Cohesion: 0.39
Nodes (1): ListingList

### Community 118 - "Real Estate Host Application README"
Cohesion: 0.36
Nodes (8): Liberu Real Estate host application, Portal synchronization modules (Rightmove, Zoopla, OnTheMarket), Theme packages and inheritance, Vite inputs are derived, not listed, Liberu Real Estate README, theme.json manifest, ThemeManager::inheritanceChain, theme-real-estate-default

### Community 119 - "Listing Controller"
Cohesion: 0.25
Nodes (1): ListingController

### Community 120 - "Marketing Campaign Controller"
Cohesion: 0.25
Nodes (1): MarketingCampaignController

### Community 121 - "Portal Report Controller"
Cohesion: 0.25
Nodes (1): PortalReportController

### Community 122 - "Rental Charge Controller"
Cohesion: 0.43
Nodes (1): RentalChargeController

### Community 123 - "Sales Progression Controller"
Cohesion: 0.25
Nodes (1): SalesProgressionController

### Community 124 - "SearchController"
Cohesion: 0.39
Nodes (1): SearchController

### Community 125 - "Vendor Quote Controller"
Cohesion: 0.43
Nodes (1): VendorQuoteController

### Community 126 - "Searcher Registry Tests"
Cohesion: 0.36
Nodes (1): SearcherRegistryTest

### Community 127 - "CommunityEvent Model"
Cohesion: 0.25
Nodes (1): CommunityEvent

### Community 128 - "Viewing Model"
Cohesion: 0.29
Nodes (1): Viewing

### Community 129 - "AgencyResource"
Cohesion: 0.25
Nodes (1): AgencyResource

### Community 130 - "BranchResource"
Cohesion: 0.25
Nodes (1): BranchResource

### Community 131 - "InspectionResource"
Cohesion: 0.25
Nodes (1): InspectionResource

### Community 132 - "InstructionResource"
Cohesion: 0.25
Nodes (1): InstructionResource

### Community 133 - "LettingResource"
Cohesion: 0.25
Nodes (1): LettingResource

### Community 134 - "ListingResource"
Cohesion: 0.29
Nodes (1): ListingResource

### Community 135 - "MaintenanceRequestResource"
Cohesion: 0.25
Nodes (1): MaintenanceRequestResource

### Community 136 - "ManagementRecordResource"
Cohesion: 0.25
Nodes (1): ManagementRecordResource

### Community 137 - "MarketingCampaignResource"
Cohesion: 0.29
Nodes (1): MarketingCampaignResource

### Community 138 - "MatchProfileResource"
Cohesion: 0.29
Nodes (1): MatchProfileResource

### Community 139 - "MediaDocumentResource"
Cohesion: 0.29
Nodes (1): MediaDocumentResource

### Community 140 - "OfferResource"
Cohesion: 0.25
Nodes (1): OfferResource

### Community 141 - "OnTheMarketSyncResource"
Cohesion: 0.25
Nodes (1): OnTheMarketSyncResource

### Community 142 - "PartyResource"
Cohesion: 0.29
Nodes (1): PartyResource

### Community 143 - "PortalReportResource"
Cohesion: 0.29
Nodes (1): PortalReportResource

### Community 144 - "PropertyCategoryResource"
Cohesion: 0.25
Nodes (1): PropertyCategoryResource

### Community 145 - "PropertySavedSearchResource"
Cohesion: 0.25
Nodes (1): PropertySavedSearchResource

### Community 146 - "PropertyTemplateResource"
Cohesion: 0.25
Nodes (1): PropertyTemplateResource

### Community 147 - "RentalApplicationResource"
Cohesion: 0.25
Nodes (1): RentalApplicationResource

### Community 148 - "RightmoveSyncResource"
Cohesion: 0.25
Nodes (1): RightmoveSyncResource

### Community 149 - "SalesProgressionResource"
Cohesion: 0.29
Nodes (1): SalesProgressionResource

### Community 150 - "TeamResource"
Cohesion: 0.25
Nodes (1): TeamResource

### Community 151 - "TerritoryResource"
Cohesion: 0.25
Nodes (1): TerritoryResource

### Community 152 - "UserResource"
Cohesion: 0.25
Nodes (1): UserResource

### Community 153 - "ValuationResource"
Cohesion: 0.29
Nodes (1): ValuationResource

### Community 154 - "VendorQuoteResource"
Cohesion: 0.25
Nodes (1): VendorQuoteResource

### Community 155 - "ViewingResource"
Cohesion: 0.25
Nodes (1): ViewingResource

### Community 156 - "WorkOrderResource"
Cohesion: 0.25
Nodes (1): WorkOrderResource

### Community 157 - "ZooplaSyncResource"
Cohesion: 0.25
Nodes (1): ZooplaSyncResource

### Community 158 - "TranslationService"
Cohesion: 0.32
Nodes (1): TranslationService

### Community 159 - "Theme Helper Functions"
Cohesion: 0.50
Nodes (7): active_theme(), set_theme(), theme(), theme_asset(), theme_layout(), theme_path(), theme_views_path()

### Community 160 - "Money Value Object"
Cohesion: 0.32
Nodes (1): Money

### Community 161 - "Real Estate OnTheMarket Core Tier"
Cohesion: 0.43
Nodes (7): Core (domain) tier, Real Estate core modules README, liberusoftware/real-estate-onthemarket, liberusoftware/real-estate-onthemarket-api, liberusoftware/real-estate-onthemarket-filament, liberusoftware/real-estate-onthemarket-livewire, Portal provider: OnTheMarket

### Community 162 - "Walkability Score Fetcher"
Cohesion: 0.52
Nodes (1): FetchWalkabilityScores

### Community 163 - "Real Estate Listings Scope"
Cohesion: 0.48
Nodes (7): Real Estate capability: Listings, Delivery phase 1: core transaction chain and website feed, liberusoftware/real-estate-listings, liberusoftware/real-estate-listings-api, liberusoftware/real-estate-listings-filament, liberusoftware/real-estate-listings-livewire, Website feed (phase 1 deliverable)

### Community 164 - "Instruction List Livewire"
Cohesion: 0.48
Nodes (1): InstructionList

### Community 165 - "Property Map Livewire"
Cohesion: 0.29
Nodes (1): PropertyMap

### Community 166 - "Property Submission Form Livewire"
Cohesion: 0.29
Nodes (1): PropertySubmissionForm

### Community 167 - "Property Tax Estimator Livewire"
Cohesion: 0.48
Nodes (1): PropertyTaxEstimator

### Community 168 - "Agency Controller"
Cohesion: 0.38
Nodes (1): AgencyController

### Community 169 - "Inspection Controller"
Cohesion: 0.48
Nodes (1): InspectionController

### Community 170 - "Instruction Controller"
Cohesion: 0.29
Nodes (1): InstructionController

### Community 171 - "Letting Controller"
Cohesion: 0.29
Nodes (1): LettingController

### Community 172 - "Maintenance Request Controller"
Cohesion: 0.48
Nodes (1): MaintenanceRequestController

### Community 173 - "Management Record Controller"
Cohesion: 0.29
Nodes (1): ManagementRecordController

### Community 174 - "OnTheMarket Sync Controller"
Cohesion: 0.29
Nodes (1): OnTheMarketSyncController

### Community 175 - "Rightmove Sync Controller"
Cohesion: 0.29
Nodes (1): RightmoveSyncController

### Community 176 - "Territory Controller"
Cohesion: 0.38
Nodes (1): TerritoryController

### Community 177 - "Zoopla Sync Controller"
Cohesion: 0.29
Nodes (1): ZooplaSyncController

### Community 178 - "Neighborhood Model"
Cohesion: 0.38
Nodes (1): Neighborhood

### Community 179 - "Party Model"
Cohesion: 0.38
Nodes (1): Party

### Community 180 - "RentalApplication Model"
Cohesion: 0.33
Nodes (1): RentalApplication

### Community 181 - "Connected Account Policy"
Cohesion: 0.29
Nodes (1): ConnectedAccountPolicy

### Community 182 - "StatusDefinitionResource"
Cohesion: 0.29
Nodes (1): StatusDefinitionResource

### Community 183 - "SearchService"
Cohesion: 0.38
Nodes (1): SearchService

### Community 184 - "RegistryCache"
Cohesion: 0.33
Nodes (1): RegistryCache

### Community 185 - "ThemeCache"
Cohesion: 0.33
Nodes (1): ThemeCache

### Community 186 - "Public Calculators Livewire"
Cohesion: 0.33
Nodes (1): Calculators

### Community 187 - "Media Document List Livewire"
Cohesion: 0.33
Nodes (1): MediaDocumentList

### Community 188 - "Offer List Livewire"
Cohesion: 0.53
Nodes (1): OfferList

### Community 189 - "Valuation List Livewire"
Cohesion: 0.53
Nodes (1): ValuationList

### Community 190 - "Viewing List Livewire"
Cohesion: 0.53
Nodes (1): ViewingList

### Community 191 - "Branch Controller"
Cohesion: 0.33
Nodes (1): BranchController

### Community 192 - "Saved Report Controller"
Cohesion: 0.47
Nodes (1): SavedReportController

### Community 193 - "UserFactory"
Cohesion: 0.33
Nodes (1): UserFactory

### Community 194 - "SyncNewUserToCrm Job"
Cohesion: 0.33
Nodes (1): SyncNewUserToCrm

### Community 195 - "NeighborhoodReview Model"
Cohesion: 0.33
Nodes (1): NeighborhoodReview

### Community 196 - "NewsArticle Model"
Cohesion: 0.33
Nodes (1): NewsArticle

### Community 197 - "Offer Model"
Cohesion: 0.33
Nodes (1): Offer

### Community 198 - "PartyReview Model"
Cohesion: 0.33
Nodes (1): PartyReview

### Community 200 - "Community 200"
Cohesion: 0.33
Nodes (1): PropertyReview

### Community 201 - "Community 201"
Cohesion: 0.33
Nodes (1): FoundationOperations

### Community 202 - "Community 202"
Cohesion: 0.47
Nodes (1): ThemeServiceProvider

### Community 203 - "Community 203"
Cohesion: 0.40
Nodes (1): SessionReader

### Community 204 - "Community 204"
Cohesion: 0.33
Nodes (1): IdentityFilamentPlugin

### Community 205 - "Community 205"
Cohesion: 0.33
Nodes (1): ModuleManagerFilamentPlugin

### Community 206 - "Community 206"
Cohesion: 0.33
Nodes (1): OrganizationsFilamentPlugin

### Community 207 - "Community 207"
Cohesion: 0.33
Nodes (1): PartiesFilamentPlugin

### Community 208 - "Community 208"
Cohesion: 0.33
Nodes (1): SessionsDevicesFilamentPlugin

### Community 209 - "Community 209"
Cohesion: 0.33
Nodes (1): SettingsFilamentPlugin

### Community 210 - "Community 210"
Cohesion: 0.40
Nodes (1): TestCase

### Community 211 - "Community 211"
Cohesion: 0.53
Nodes (1): OnTheMarketClient

### Community 212 - "Community 212"
Cohesion: 0.53
Nodes (1): RightmoveClient

### Community 213 - "Community 213"
Cohesion: 0.53
Nodes (1): ZooplaClient

### Community 214 - "Community 214"
Cohesion: 0.50
Nodes (1): CreateUserFromProvider

### Community 215 - "Community 215"
Cohesion: 0.40
Nodes (5): Filament adapter tier, Liberu Filament modules README, Liberu Livewire modules README, Real Estate Filament modules README, Foundation adapters vs product scope

### Community 216 - "Community 216"
Cohesion: 0.70
Nodes (5): Real Estate capability: Instructions, liberusoftware/real-estate-instructions, liberusoftware/real-estate-instructions-api, liberusoftware/real-estate-instructions-filament, liberusoftware/real-estate-instructions-livewire

### Community 217 - "Community 217"
Cohesion: 0.70
Nodes (5): Real Estate capability: Matching, liberusoftware/real-estate-matching, liberusoftware/real-estate-matching-api, liberusoftware/real-estate-matching-filament, liberusoftware/real-estate-matching-livewire

### Community 218 - "Community 218"
Cohesion: 0.70
Nodes (5): Real Estate capability: Media and Documents, liberusoftware/real-estate-media-and-documents, liberusoftware/real-estate-media-and-documents-api, liberusoftware/real-estate-media-and-documents-filament, liberusoftware/real-estate-media-and-documents-livewire

### Community 219 - "Community 219"
Cohesion: 0.70
Nodes (5): Real Estate capability: Offers, liberusoftware/real-estate-offers, liberusoftware/real-estate-offers-api, liberusoftware/real-estate-offers-filament, liberusoftware/real-estate-offers-livewire

### Community 220 - "Community 220"
Cohesion: 0.70
Nodes (5): Real Estate capability: Parties, liberusoftware/real-estate-parties, liberusoftware/real-estate-parties-api, liberusoftware/real-estate-parties-filament, liberusoftware/real-estate-parties-livewire

### Community 221 - "Community 221"
Cohesion: 0.70
Nodes (5): Real Estate capability: Portals and Reporting, liberusoftware/real-estate-portals-reporting, liberusoftware/real-estate-portals-reporting-api, liberusoftware/real-estate-portals-reporting-filament, liberusoftware/real-estate-portals-reporting-livewire

### Community 222 - "Community 222"
Cohesion: 0.70
Nodes (5): Real Estate capability: Real Estate Core, liberusoftware/real-estate-core, liberusoftware/real-estate-core-api, liberusoftware/real-estate-core-filament, liberusoftware/real-estate-core-livewire

### Community 223 - "Community 223"
Cohesion: 0.70
Nodes (5): Real Estate capability: Sales Progression, liberusoftware/real-estate-sales-progression, liberusoftware/real-estate-sales-progression-api, liberusoftware/real-estate-sales-progression-filament, liberusoftware/real-estate-sales-progression-livewire

### Community 224 - "Community 224"
Cohesion: 0.70
Nodes (5): Real Estate capability: Valuations, liberusoftware/real-estate-valuations, liberusoftware/real-estate-valuations-api, liberusoftware/real-estate-valuations-filament, liberusoftware/real-estate-valuations-livewire

### Community 225 - "Community 225"
Cohesion: 0.70
Nodes (5): Real Estate capability: Viewings, liberusoftware/real-estate-viewings, liberusoftware/real-estate-viewings-api, liberusoftware/real-estate-viewings-filament, liberusoftware/real-estate-viewings-livewire

### Community 226 - "Community 226"
Cohesion: 0.60
Nodes (1): NeighborhoodReviewForm

### Community 227 - "Community 227"
Cohesion: 0.40
Nodes (1): PartyList

### Community 228 - "Community 228"
Cohesion: 0.60
Nodes (1): PartyReviewForm

### Community 229 - "Community 229"
Cohesion: 0.60
Nodes (1): PropertyRecommendations

### Community 230 - "Community 230"
Cohesion: 0.60
Nodes (1): PropertyReviewForm

### Community 231 - "Community 231"
Cohesion: 0.40
Nodes (1): PropertyValuationEstimator

### Community 232 - "Community 232"
Cohesion: 0.40
Nodes (1): ViewingBooking

### Community 233 - "Community 233"
Cohesion: 0.40
Nodes (1): WishlistManager

### Community 235 - "Community 235"
Cohesion: 0.40
Nodes (1): ContactController

### Community 236 - "Community 236"
Cohesion: 0.40
Nodes (1): CoreConfigurationController

### Community 237 - "Community 237"
Cohesion: 0.60
Nodes (1): NewsArticleController

### Community 238 - "Community 238"
Cohesion: 0.40
Nodes (1): PropertyCategoryController

### Community 239 - "Community 239"
Cohesion: 0.40
Nodes (1): PropertyPriceAlertController

### Community 240 - "Community 240"
Cohesion: 0.40
Nodes (1): PropertyTemplateController

### Community 241 - "Community 241"
Cohesion: 0.60
Nodes (1): VirtualStagingController

### Community 242 - "Community 242"
Cohesion: 0.50
Nodes (1): ThemeDiscovery

### Community 243 - "Community 243"
Cohesion: 0.40
Nodes (1): PropertyGalleryItem

### Community 244 - "Community 244"
Cohesion: 0.40
Nodes (1): LocaleFormatter

### Community 245 - "Community 245"
Cohesion: 0.50
Nodes (1): UpdateUserProfileInformation

### Community 246 - "Community 246"
Cohesion: 0.50
Nodes (1): ReadinessRegistry

### Community 247 - "Community 247"
Cohesion: 0.50
Nodes (1): DeleteUser

### Community 248 - "Community 248"
Cohesion: 0.40
Nodes (1): LanguageSwitcher

### Community 249 - "Community 249"
Cohesion: 0.40
Nodes (1): ThemeSwitcher

### Community 250 - "Community 250"
Cohesion: 0.40
Nodes (1): ActivityLogEntry

### Community 251 - "Community 251"
Cohesion: 0.40
Nodes (1): LeaseAgreement

### Community 253 - "Community 253"
Cohesion: 0.40
Nodes (1): PropertyPriceAlert

### Community 254 - "Community 254"
Cohesion: 0.40
Nodes (1): Valuation

### Community 255 - "Community 255"
Cohesion: 0.40
Nodes (1): VendorQuote

### Community 256 - "Community 256"
Cohesion: 0.40
Nodes (1): WorkOrder

### Community 257 - "Community 257"
Cohesion: 0.40
Nodes (1): AccountSecurity

### Community 258 - "Community 258"
Cohesion: 0.40
Nodes (1): ManageSiteSettings

### Community 259 - "Community 259"
Cohesion: 0.60
Nodes (1): LettingPolicy

### Community 260 - "Community 260"
Cohesion: 0.60
Nodes (1): ManagementRecordPolicy

### Community 261 - "Community 261"
Cohesion: 0.50
Nodes (1): TelescopeDashboardServiceProvider

### Community 262 - "Community 262"
Cohesion: 0.40
Nodes (1): RecoveryCodeHasher

### Community 263 - "Community 263"
Cohesion: 0.40
Nodes (1): SearcherRegistry

### Community 264 - "Community 264"
Cohesion: 0.40
Nodes (1): LocalSearchIndexer

### Community 265 - "Community 265"
Cohesion: 0.40
Nodes (1): ActivityCommentsFilamentPlugin

### Community 266 - "Community 266"
Cohesion: 0.40
Nodes (1): AnalyticsCoreFilamentPlugin

### Community 267 - "Community 267"
Cohesion: 0.40
Nodes (1): AnalyticsGoogleFilamentPlugin

### Community 268 - "Community 268"
Cohesion: 0.40
Nodes (1): AnalyticsMetaFilamentPlugin

### Community 269 - "Community 269"
Cohesion: 0.40
Nodes (1): ApiAccessFilamentPlugin

### Community 270 - "Community 270"
Cohesion: 0.40
Nodes (1): ApplicationFilamentPlugin

### Community 271 - "Community 271"
Cohesion: 0.40
Nodes (1): AuditFilamentPlugin

### Community 272 - "Community 272"
Cohesion: 0.40
Nodes (1): CurrencyContextFilamentPlugin

### Community 273 - "Community 273"
Cohesion: 0.40
Nodes (1): DeveloperExperienceFilamentPlugin

### Community 274 - "Community 274"
Cohesion: 0.40
Nodes (1): FeatureFlagsFilamentPlugin

### Community 275 - "Community 275"
Cohesion: 0.40
Nodes (1): FilesMediaFilamentPlugin

### Community 276 - "Community 276"
Cohesion: 0.40
Nodes (1): ImportExportFilamentPlugin

### Community 277 - "Community 277"
Cohesion: 0.40
Nodes (1): IntegrationsFilamentPlugin

### Community 278 - "Community 278"
Cohesion: 0.40
Nodes (1): JetstreamBridgeFilamentPlugin

### Community 279 - "Community 279"
Cohesion: 0.40
Nodes (1): LettingsFilamentPlugin

### Community 280 - "Community 280"
Cohesion: 0.40
Nodes (1): LocalizationCoreFilamentPlugin

### Community 281 - "Community 281"
Cohesion: 0.40
Nodes (1): NotificationsFilamentPlugin

### Community 282 - "Community 282"
Cohesion: 0.40
Nodes (1): ObservabilityFilamentPlugin

### Community 283 - "Community 283"
Cohesion: 0.40
Nodes (1): OnTheMarketFilamentPlugin

### Community 284 - "Community 284"
Cohesion: 0.40
Nodes (1): PortalsReportingFilamentPlugin

### Community 285 - "Community 285"
Cohesion: 0.40
Nodes (1): ProfilesFilamentPlugin

### Community 286 - "Community 286"
Cohesion: 0.40
Nodes (1): PropertiesFilamentPlugin

### Community 287 - "Community 287"
Cohesion: 0.40
Nodes (1): PropertyManagementFilamentPlugin

### Community 288 - "Community 288"
Cohesion: 0.40
Nodes (1): RealEstateCoreFilamentPlugin

### Community 289 - "Community 289"
Cohesion: 0.40
Nodes (1): RightmoveFilamentPlugin

### Community 290 - "Community 290"
Cohesion: 0.40
Nodes (1): SalesProgressionFilamentPlugin

### Community 291 - "Community 291"
Cohesion: 0.40
Nodes (1): SchedulerQueuesFilamentPlugin

### Community 292 - "Community 292"
Cohesion: 0.40
Nodes (1): SearchFilamentPlugin

### Community 293 - "Community 293"
Cohesion: 0.40
Nodes (1): TwoFactorAuthenticationFilamentPlugin

### Community 294 - "Community 294"
Cohesion: 0.40
Nodes (1): WebhooksFilamentPlugin

### Community 295 - "Community 295"
Cohesion: 0.40
Nodes (1): ZooplaFilamentPlugin

### Community 296 - "Community 296"
Cohesion: 0.40
Nodes (1): ConfiguredRegistrationPolicy

### Community 297 - "Community 297"
Cohesion: 0.40
Nodes (1): DestinationRegistry

### Community 298 - "Community 298"
Cohesion: 0.40
Nodes (1): GoogleDestination

### Community 299 - "Community 299"
Cohesion: 0.40
Nodes (1): IntegrationRegistry

### Community 300 - "Community 300"
Cohesion: 0.40
Nodes (1): MetaCustomerNormalizer

### Community 301 - "Community 301"
Cohesion: 0.40
Nodes (1): MetaDestination

### Community 302 - "Community 302"
Cohesion: 0.40
Nodes (1): TranslationRegistry

### Community 303 - "Community 303"
Cohesion: 0.40
Nodes (1): TrustedDeviceManager

### Community 306 - "Community 306"
Cohesion: 0.50
Nodes (1): UpdateConnectedAccount

### Community 307 - "Community 307"
Cohesion: 0.50
Nodes (1): GenerateNeuralPropertyValuation

### Community 308 - "Community 308"
Cohesion: 0.83
Nodes (1): GeneratePropertyDescription

### Community 309 - "Community 309"
Cohesion: 0.83
Nodes (1): SendPropertyToFriend

### Community 310 - "Community 310"
Cohesion: 0.50
Nodes (1): SyncOnTheMarketListing

### Community 311 - "Community 311"
Cohesion: 0.50
Nodes (1): SyncRightmoveListing

### Community 312 - "Community 312"
Cohesion: 0.50
Nodes (1): SyncZooplaListing

### Community 314 - "Community 314"
Cohesion: 0.50
Nodes (1): ContactEnquiryForm

### Community 315 - "Community 315"
Cohesion: 0.50
Nodes (1): MarketingCampaignList

### Community 316 - "Community 316"
Cohesion: 0.50
Nodes (1): MatchProfileList

### Community 317 - "Community 317"
Cohesion: 0.50
Nodes (1): MortgageCalculator

### Community 318 - "Community 318"
Cohesion: 0.50
Nodes (1): PortalReportList

### Community 319 - "Community 319"
Cohesion: 0.50
Nodes (1): PropertyPreview

### Community 320 - "Community 320"
Cohesion: 0.50
Nodes (1): RentalApplicationForm

### Community 321 - "Community 321"
Cohesion: 0.50
Nodes (1): RentalYieldCalculator

### Community 322 - "Community 322"
Cohesion: 0.50
Nodes (1): SalesProgressionList

### Community 323 - "Community 323"
Cohesion: 0.83
Nodes (4): docs/adr/ decision records, CONTEXT.md glossary, Single-context domain docs, Agent Domain Docs Convention

### Community 324 - "Community 324"
Cohesion: 0.50
Nodes (1): LocaleContext

### Community 331 - "Community 331"
Cohesion: 0.50
Nodes (1): CalendarEntryController

### Community 332 - "Community 332"
Cohesion: 0.50
Nodes (1): CommunicationController

### Community 333 - "Community 333"
Cohesion: 0.50
Nodes (1): CommunityEventController

### Community 334 - "Community 334"
Cohesion: 0.50
Nodes (1): HomeReportController

### Community 335 - "Community 335"
Cohesion: 0.50
Nodes (1): PropertySavedSearchController

### Community 336 - "Community 336"
Cohesion: 0.50
Nodes (1): CoreCapabilityDefinition

### Community 337 - "Community 337"
Cohesion: 0.50
Nodes (1): ContentPagesTest

### Community 339 - "Community 339"
Cohesion: 0.50
Nodes (1): ModulePlugins

### Community 340 - "Community 340"
Cohesion: 0.67
Nodes (1): OrganizationUser

### Community 341 - "Community 341"
Cohesion: 0.50
Nodes (1): CreateNewUser

### Community 342 - "Community 342"
Cohesion: 0.50
Nodes (1): PropertyShareMail

### Community 343 - "Community 343"
Cohesion: 0.50
Nodes (1): SetLocale

### Community 344 - "Community 344"
Cohesion: 0.83
Nodes (3): down(), getConnection(), up()

### Community 345 - "Community 345"
Cohesion: 0.50
Nodes (1): Agency

### Community 346 - "Community 346"
Cohesion: 0.50
Nodes (1): Branch

### Community 347 - "Community 347"
Cohesion: 0.50
Nodes (1): CalendarEntry

### Community 348 - "Community 348"
Cohesion: 0.50
Nodes (1): Communication

### Community 349 - "Community 349"
Cohesion: 0.50
Nodes (1): Contact

### Community 350 - "Community 350"
Cohesion: 0.67
Nodes (1): DocumentTemplate

### Community 351 - "Community 351"
Cohesion: 0.50
Nodes (1): Inspection

### Community 352 - "Community 352"
Cohesion: 0.50
Nodes (1): Instruction

### Community 353 - "Community 353"
Cohesion: 0.50
Nodes (1): Letting

### Community 354 - "Community 354"
Cohesion: 0.50
Nodes (1): Listing

### Community 355 - "Community 355"
Cohesion: 0.50
Nodes (1): MaintenanceRequest

### Community 356 - "Community 356"
Cohesion: 0.50
Nodes (1): ManagementRecord

### Community 357 - "Community 357"
Cohesion: 0.50
Nodes (1): MarketingCampaign

### Community 358 - "Community 358"
Cohesion: 0.50
Nodes (1): MatchProfile

### Community 359 - "Community 359"
Cohesion: 0.50
Nodes (1): OnTheMarketSync

### Community 360 - "Community 360"
Cohesion: 0.50
Nodes (1): Organization

### Community 361 - "Community 361"
Cohesion: 0.50
Nodes (1): PortalReport

### Community 362 - "Community 362"
Cohesion: 0.50
Nodes (1): PropertySavedSearch

### Community 363 - "Community 363"
Cohesion: 0.50
Nodes (1): RentalCharge

### Community 364 - "Community 364"
Cohesion: 0.50
Nodes (1): RightmoveSync

### Community 365 - "Community 365"
Cohesion: 0.50
Nodes (1): SalesProgression

### Community 366 - "Community 366"
Cohesion: 0.50
Nodes (1): SavedReport

### Community 367 - "Community 367"
Cohesion: 0.50
Nodes (1): StatusDefinition

### Community 368 - "Community 368"
Cohesion: 0.50
Nodes (1): TeamInvitation

### Community 369 - "Community 369"
Cohesion: 0.50
Nodes (1): Team

### Community 370 - "Community 370"
Cohesion: 0.50
Nodes (1): Territory

### Community 371 - "Community 371"
Cohesion: 0.50
Nodes (1): ViewingFeedback

### Community 372 - "Community 372"
Cohesion: 0.50
Nodes (1): ZooplaSync

### Community 373 - "Community 373"
Cohesion: 0.50
Nodes (1): EditNewsArticle

### Community 374 - "Community 374"
Cohesion: 0.50
Nodes (1): FortifyServiceProvider

### Community 375 - "Community 375"
Cohesion: 0.50
Nodes (1): HorizonDashboardServiceProvider

### Community 376 - "Community 376"
Cohesion: 0.50
Nodes (1): IndexableRegistry

### Community 377 - "Community 377"
Cohesion: 0.50
Nodes (1): PermissionRegistry

### Community 378 - "Community 378"
Cohesion: 0.50
Nodes (1): BreakGlass

### Community 379 - "Community 379"
Cohesion: 0.50
Nodes (1): CurrencyContext

### Community 380 - "Community 380"
Cohesion: 0.50
Nodes (1): CurrencyRegistry

### Community 381 - "Community 381"
Cohesion: 0.50
Nodes (1): MoneyFormatter

### Community 382 - "Community 382"
Cohesion: 0.67
Nodes (1): OpenExchangeRateApiProvider

### Community 383 - "Community 383"
Cohesion: 0.50
Nodes (1): ScopedSettings

### Community 384 - "Community 384"
Cohesion: 0.50
Nodes (1): AnalyticsServiceProvider

### Community 385 - "Community 385"
Cohesion: 0.50
Nodes (1): ApplicationCoreServiceProvider

### Community 386 - "Community 386"
Cohesion: 0.50
Nodes (1): AuditServiceProvider

### Community 387 - "Community 387"
Cohesion: 0.50
Nodes (1): CurrencyServiceProvider

### Community 388 - "Community 388"
Cohesion: 0.50
Nodes (1): FilesMediaServiceProvider

### Community 389 - "Community 389"
Cohesion: 0.50
Nodes (1): IdentityServiceProvider

### Community 390 - "Community 390"
Cohesion: 0.50
Nodes (1): InstructionsFilamentPlugin

### Community 391 - "Community 391"
Cohesion: 0.50
Nodes (1): IntegrationsServiceProvider

### Community 392 - "Community 392"
Cohesion: 0.50
Nodes (1): LettingsServiceProvider

### Community 393 - "Community 393"
Cohesion: 0.50
Nodes (1): ListingsFilamentPlugin

### Community 394 - "Community 394"
Cohesion: 0.50
Nodes (1): LocalizationServiceProvider

### Community 395 - "Community 395"
Cohesion: 0.50
Nodes (1): MarketingFilamentPlugin

### Community 396 - "Community 396"
Cohesion: 0.50
Nodes (1): MatchingFilamentPlugin

### Community 397 - "Community 397"
Cohesion: 0.50
Nodes (1): MediaAndDocumentsFilamentPlugin

### Community 398 - "Community 398"
Cohesion: 0.50
Nodes (1): ModuleManagerServiceProvider

### Community 399 - "Community 399"
Cohesion: 0.50
Nodes (1): ModuleValidationGuard

### Community 400 - "Community 400"
Cohesion: 0.50
Nodes (1): ObservabilityServiceProvider

### Community 401 - "Community 401"
Cohesion: 0.50
Nodes (1): OffersFilamentPlugin

### Community 402 - "Community 402"
Cohesion: 0.50
Nodes (1): OrganizationsServiceProvider

### Community 403 - "Community 403"
Cohesion: 0.50
Nodes (1): PartiesServiceProvider

### Community 404 - "Community 404"
Cohesion: 0.50
Nodes (1): RolesPermissionsServiceProvider

### Community 405 - "Community 405"
Cohesion: 0.67
Nodes (1): SearchServiceProvider

### Community 406 - "Community 406"
Cohesion: 0.50
Nodes (1): SessionsDevicesServiceProvider

### Community 407 - "Community 407"
Cohesion: 0.50
Nodes (1): TwoFactorServiceProvider

### Community 408 - "Community 408"
Cohesion: 0.50
Nodes (1): ValuationsFilamentPlugin

### Community 409 - "Community 409"
Cohesion: 0.50
Nodes (1): ViewingsFilamentPlugin

### Community 410 - "Community 410"
Cohesion: 0.50
Nodes (1): DeliveryRetry

### Community 411 - "Community 411"
Cohesion: 0.50
Nodes (1): EventRouter

### Community 412 - "Community 412"
Cohesion: 0.50
Nodes (1): EventSanitizer

### Community 413 - "Community 413"
Cohesion: 0.50
Nodes (1): IdempotencyStore

### Community 414 - "Community 414"
Cohesion: 0.50
Nodes (1): JobPolicy

### Community 415 - "Community 415"
Cohesion: 0.50
Nodes (1): NotificationPolicy

### Community 416 - "Community 416"
Cohesion: 0.50
Nodes (1): NullMetrics

### Community 417 - "Community 417"
Cohesion: 0.67
Nodes (1): RowValidator

### Community 418 - "Community 418"
Cohesion: 0.50
Nodes (1): SloRegistry

### Community 419 - "Community 419"
Cohesion: 0.50
Nodes (1): TeamIntegrationSettings

### Community 420 - "Community 420"
Cohesion: 0.50
Nodes (1): ThemeColors

### Community 421 - "Community 421"
Cohesion: 0.67
Nodes (1): WebhookSigner

### Community 422 - "Community 422"
Cohesion: 0.50
Nodes (1): ExchangeRate

### Community 423 - "Community 423"
Cohesion: 0.83
Nodes (1): RoleWelcomeWidget

### Community 424 - "Community 424"
Cohesion: 0.67
Nodes (1): AcceptInvitation

### Community 425 - "Community 425"
Cohesion: 0.67
Nodes (1): CreateConnectedAccount

### Community 426 - "Community 426"
Cohesion: 0.67
Nodes (1): GenerateRedirectForProvider

### Community 427 - "Community 427"
Cohesion: 0.67
Nodes (1): HandleInvalidState

### Community 428 - "Community 428"
Cohesion: 0.67
Nodes (1): InviteMember

### Community 429 - "Community 429"
Cohesion: 0.67
Nodes (1): ResolveSocialiteUser

### Community 430 - "Community 430"
Cohesion: 0.67
Nodes (1): TransferOwnership

### Community 431 - "Community 431"
Cohesion: 0.67
Nodes (1): UpdateProfile

### Community 432 - "Community 432"
Cohesion: 0.67
Nodes (1): CheckPriceAlerts

### Community 433 - "Community 433"
Cohesion: 0.67
Nodes (1): CreateCalendarEntry

### Community 434 - "Community 434"
Cohesion: 0.67
Nodes (1): CreateContactMessage

### Community 435 - "Community 435"
Cohesion: 0.67
Nodes (1): CreateInspection

### Community 436 - "Community 436"
Cohesion: 0.67
Nodes (1): CreateMaintenanceRequest

### Community 437 - "Community 437"
Cohesion: 0.67
Nodes (1): CreatePriceAlert

### Community 438 - "Community 438"
Cohesion: 0.67
Nodes (1): CreateRentalCharge

### Community 439 - "Community 439"
Cohesion: 0.67
Nodes (1): CreateVendorQuote

### Community 440 - "Community 440"
Cohesion: 0.67
Nodes (1): DecideRentalApplication

### Community 441 - "Community 441"
Cohesion: 0.67
Nodes (1): DecideVendorQuote

### Community 442 - "Community 442"
Cohesion: 0.67
Nodes (1): DeletePropertySearch

### Community 443 - "Community 443"
Cohesion: 1.00
Nodes (1): GeneratePropertyQrCode

### Community 444 - "Community 444"
Cohesion: 1.00
Nodes (1): SubmitPropertyReview

### Community 445 - "Community 445"
Cohesion: 1.00
Nodes (1): TransitionInstruction

### Community 446 - "Community 446"
Cohesion: 1.00
Nodes (1): TransitionListing

### Community 447 - "Community 447"
Cohesion: 1.00
Nodes (1): TransitionProperty

### Community 448 - "Community 448"
Cohesion: 0.67
Nodes (1): UpdateMaintenanceRequest

### Community 449 - "Community 449"
Cohesion: 0.67
Nodes (1): UpdateRentalApplicationScreening

### Community 450 - "Community 450"
Cohesion: 0.67
Nodes (1): UploadHomeReportFile

### Community 451 - "Community 451"
Cohesion: 1.00
Nodes (2): scopeSearch(), searchableColumns()

### Community 452 - "Community 452"
Cohesion: 0.67
Nodes (1): CacheModulesCommand

### Community 453 - "Community 453"
Cohesion: 0.67
Nodes (1): ClearModulesCommand

### Community 454 - "Community 454"
Cohesion: 0.67
Nodes (1): FoundationDoctorCommand

### Community 455 - "Community 455"
Cohesion: 0.67
Nodes (1): ListFeaturesCommand

### Community 456 - "Community 456"
Cohesion: 0.67
Nodes (1): ListModulesCommand

### Community 457 - "Community 457"
Cohesion: 0.67
Nodes (1): ModuleStatusCommand

### Community 458 - "Community 458"
Cohesion: 0.67
Nodes (1): ReindexCommand

### Community 459 - "Community 459"
Cohesion: 0.67
Nodes (1): ThemeCacheCommand

### Community 460 - "Community 460"
Cohesion: 0.67
Nodes (1): ThemeClearCommand

### Community 461 - "Community 461"
Cohesion: 0.67
Nodes (1): ThemeValidateCommand

### Community 462 - "Community 462"
Cohesion: 0.67
Nodes (1): ValidateModulesCommand

### Community 463 - "Community 463"
Cohesion: 0.67
Nodes (1): LocaleResolver

### Community 467 - "Community 467"
Cohesion: 0.67
Nodes (1): ReadinessController

### Community 468 - "Community 468"
Cohesion: 0.67
Nodes (1): ProfileUpdate

### Community 469 - "Community 469"
Cohesion: 0.67
Nodes (1): TransferSchema

### Community 470 - "Community 470"
Cohesion: 0.67
Nodes (1): TwoFactorPolicy

### Community 471 - "Community 471"
Cohesion: 0.67
Nodes (1): IdentityEvent

### Community 472 - "Community 472"
Cohesion: 0.67
Nodes (1): ConnectedAccountFactory

### Community 473 - "Community 473"
Cohesion: 0.67
Nodes (1): TeamFactory

### Community 474 - "Community 474"
Cohesion: 0.67
Nodes (1): TestPanelProvider

### Community 476 - "Community 476"
Cohesion: 0.67
Nodes (1): EmitAuthenticationEvent

### Community 477 - "Community 477"
Cohesion: 0.67
Nodes (1): CorrelationId

### Community 478 - "Community 478"
Cohesion: 0.67
Nodes (1): SecurityHeaders

### Community 526 - "Community 526"
Cohesion: 0.67
Nodes (1): ConnectedAccount

### Community 527 - "Community 527"
Cohesion: 0.67
Nodes (1): ContactMessage

### Community 530 - "Community 530"
Cohesion: 0.67
Nodes (1): WorkOrderUpdate

### Community 531 - "Community 531"
Cohesion: 0.67
Nodes (1): CreatePropertySavedSearch

### Community 532 - "Community 532"
Cohesion: 0.67
Nodes (1): CreateUser

### Community 533 - "Community 533"
Cohesion: 0.67
Nodes (1): EditPropertySavedSearch

### Community 534 - "Community 534"
Cohesion: 0.67
Nodes (1): EditTeam

### Community 535 - "Community 535"
Cohesion: 0.67
Nodes (1): EditUser

### Community 536 - "Community 536"
Cohesion: 0.67
Nodes (1): ListTeams

### Community 537 - "Community 537"
Cohesion: 0.67
Nodes (1): ListUsers

### Community 538 - "Community 538"
Cohesion: 0.67
Nodes (1): ListLettings

### Community 539 - "Community 539"
Cohesion: 0.67
Nodes (1): ListManagementRecords

### Community 540 - "Community 540"
Cohesion: 0.67
Nodes (1): CommunityEventResource

### Community 541 - "Community 541"
Cohesion: 0.67
Nodes (1): ContactResource

### Community 542 - "Community 542"
Cohesion: 0.67
Nodes (1): LeaseAgreementResource

### Community 543 - "Community 543"
Cohesion: 0.67
Nodes (1): PropertyHistoryResource

### Community 544 - "Community 544"
Cohesion: 0.67
Nodes (1): PropertyPriceAlertResource

### Community 545 - "Community 545"
Cohesion: 0.67
Nodes (1): PropertyQrCodeResource

### Community 546 - "Community 546"
Cohesion: 0.67
Nodes (1): PropertyShareResource

### Community 547 - "Community 547"
Cohesion: 0.67
Nodes (1): RentalChargeResource

### Community 548 - "Community 548"
Cohesion: 0.67
Nodes (1): WorkOrderUpdateResource

### Community 549 - "Community 549"
Cohesion: 0.67
Nodes (1): AnyTeamRoleLookup

### Community 550 - "Community 550"
Cohesion: 0.67
Nodes (1): CurrencyPreferenceResolver

### Community 551 - "Community 551"
Cohesion: 0.67
Nodes (1): CurrentTeamResolver

### Community 552 - "Community 552"
Cohesion: 0.67
Nodes (1): SeparationOfDuty

### Community 553 - "Community 553"
Cohesion: 0.67
Nodes (1): ActivityCommentsApiServiceProvider

### Community 554 - "Community 554"
Cohesion: 0.67
Nodes (1): ActivityCommentsLivewireServiceProvider

### Community 555 - "Community 555"
Cohesion: 0.67
Nodes (1): ActivityCommentsServiceProvider

### Community 556 - "Community 556"
Cohesion: 0.67
Nodes (1): AnalyticsCoreApiServiceProvider

### Community 557 - "Community 557"
Cohesion: 0.67
Nodes (1): AnalyticsCoreLivewireServiceProvider

### Community 558 - "Community 558"
Cohesion: 0.67
Nodes (1): AnalyticsGoogleApiServiceProvider

### Community 559 - "Community 559"
Cohesion: 0.67
Nodes (1): AnalyticsGoogleLivewireServiceProvider

### Community 560 - "Community 560"
Cohesion: 0.67
Nodes (1): AnalyticsGoogleServiceProvider

### Community 561 - "Community 561"
Cohesion: 0.67
Nodes (1): AnalyticsMetaApiServiceProvider

### Community 562 - "Community 562"
Cohesion: 0.67
Nodes (1): AnalyticsMetaFilamentServiceProvider

### Community 563 - "Community 563"
Cohesion: 0.67
Nodes (1): AnalyticsMetaLivewireServiceProvider

### Community 564 - "Community 564"
Cohesion: 0.67
Nodes (1): AnalyticsMetaServiceProvider

### Community 565 - "Community 565"
Cohesion: 0.67
Nodes (1): ApiAccessApiServiceProvider

### Community 566 - "Community 566"
Cohesion: 0.67
Nodes (1): ApiAccessLivewireServiceProvider

### Community 567 - "Community 567"
Cohesion: 0.67
Nodes (1): ApplicationApiServiceProvider

### Community 568 - "Community 568"
Cohesion: 0.67
Nodes (1): ApplicationLivewireServiceProvider

### Community 569 - "Community 569"
Cohesion: 0.67
Nodes (1): AuditApiServiceProvider

### Community 570 - "Community 570"
Cohesion: 0.67
Nodes (1): AuditLivewireServiceProvider

### Community 571 - "Community 571"
Cohesion: 0.67
Nodes (1): CurrencyContextFilamentServiceProvider

### Community 572 - "Community 572"
Cohesion: 0.67
Nodes (1): CurrencyContextLivewireServiceProvider

### Community 573 - "Community 573"
Cohesion: 0.67
Nodes (1): DeveloperExperienceApiServiceProvider

### Community 574 - "Community 574"
Cohesion: 0.67
Nodes (1): DeveloperExperienceLivewireServiceProvider

### Community 575 - "Community 575"
Cohesion: 0.67
Nodes (1): DeveloperExperienceServiceProvider

### Community 576 - "Community 576"
Cohesion: 0.67
Nodes (1): FeatureFlagsFilamentServiceProvider

### Community 577 - "Community 577"
Cohesion: 0.67
Nodes (1): FeatureFlagsLivewireServiceProvider

### Community 578 - "Community 578"
Cohesion: 0.67
Nodes (1): FeatureFlagsServiceProvider

### Community 579 - "Community 579"
Cohesion: 0.67
Nodes (1): FilesMediaApiServiceProvider

### Community 580 - "Community 580"
Cohesion: 0.67
Nodes (1): FilesMediaLivewireServiceProvider

### Community 581 - "Community 581"
Cohesion: 0.67
Nodes (1): IdentityCoreApiServiceProvider

### Community 582 - "Community 582"
Cohesion: 0.67
Nodes (1): IdentityCoreLivewireServiceProvider

### Community 583 - "Community 583"
Cohesion: 0.67
Nodes (1): ImportExportApiServiceProvider

### Community 584 - "Community 584"
Cohesion: 0.67
Nodes (1): ImportExportFilamentServiceProvider

### Community 585 - "Community 585"
Cohesion: 0.67
Nodes (1): ImportExportLivewireServiceProvider

### Community 586 - "Community 586"
Cohesion: 0.67
Nodes (1): ImportExportServiceProvider

### Community 587 - "Community 587"
Cohesion: 0.67
Nodes (1): IntegrationsApiServiceProvider

### Community 588 - "Community 588"
Cohesion: 0.67
Nodes (1): IntegrationsLivewireServiceProvider

### Community 589 - "Community 589"
Cohesion: 0.67
Nodes (1): JetstreamBridgeApiServiceProvider

### Community 590 - "Community 590"
Cohesion: 0.67
Nodes (1): JetstreamBridgeFilamentServiceProvider

### Community 591 - "Community 591"
Cohesion: 0.67
Nodes (1): JetstreamBridgeLivewireServiceProvider

### Community 592 - "Community 592"
Cohesion: 0.67
Nodes (1): LettingsApiServiceProvider

### Community 593 - "Community 593"
Cohesion: 0.67
Nodes (1): LettingsFilamentServiceProvider

### Community 594 - "Community 594"
Cohesion: 0.67
Nodes (1): LocalizationCoreApiServiceProvider

### Community 595 - "Community 595"
Cohesion: 0.67
Nodes (1): LocalizationLivewireServiceProvider

### Community 596 - "Community 596"
Cohesion: 0.67
Nodes (1): MediaAndDocumentsApiServiceProvider

### Community 597 - "Community 597"
Cohesion: 0.67
Nodes (1): ModuleDiscovery

### Community 598 - "Community 598"
Cohesion: 0.67
Nodes (1): ModuleManagerApiServiceProvider

### Community 599 - "Community 599"
Cohesion: 0.67
Nodes (1): ModuleManagerFilamentServiceProvider

### Community 600 - "Community 600"
Cohesion: 0.67
Nodes (1): ModuleManagerLivewireServiceProvider

### Community 601 - "Community 601"
Cohesion: 0.67
Nodes (1): ModuleValidator

### Community 602 - "Community 602"
Cohesion: 0.67
Nodes (1): MyMemoryServiceProvider

### Community 603 - "Community 603"
Cohesion: 0.67
Nodes (1): NotificationsApiServiceProvider

### Community 604 - "Community 604"
Cohesion: 0.67
Nodes (1): NotificationsFilamentServiceProvider

### Community 605 - "Community 605"
Cohesion: 0.67
Nodes (1): NotificationsLivewireServiceProvider

### Community 606 - "Community 606"
Cohesion: 0.67
Nodes (1): NotificationsServiceProvider

### Community 607 - "Community 607"
Cohesion: 0.67
Nodes (1): ObservabilityApiServiceProvider

### Community 608 - "Community 608"
Cohesion: 0.67
Nodes (1): ObservabilityLivewireServiceProvider

### Community 609 - "Community 609"
Cohesion: 0.67
Nodes (1): OrganizationsTeamsApiServiceProvider

### Community 610 - "Community 610"
Cohesion: 0.67
Nodes (1): OrganizationsTeamsLivewireServiceProvider

### Community 611 - "Community 611"
Cohesion: 0.67
Nodes (1): ProfilesApiServiceProvider

### Community 612 - "Community 612"
Cohesion: 0.67
Nodes (1): ProfilesFilamentServiceProvider

### Community 613 - "Community 613"
Cohesion: 0.67
Nodes (1): ProfilesLivewireServiceProvider

### Community 614 - "Community 614"
Cohesion: 0.67
Nodes (1): ProfilesServiceProvider

### Community 615 - "Community 615"
Cohesion: 0.67
Nodes (1): PropertyManagementApiServiceProvider

### Community 616 - "Community 616"
Cohesion: 0.67
Nodes (1): PropertyManagementFilamentServiceProvider

### Community 617 - "Community 617"
Cohesion: 0.67
Nodes (1): RightmoveServiceProvider

### Community 618 - "Community 618"
Cohesion: 0.67
Nodes (1): RolesPermissionsApiServiceProvider

### Community 619 - "Community 619"
Cohesion: 0.67
Nodes (1): RolesPermissionsFilamentPlugin

### Community 620 - "Community 620"
Cohesion: 0.67
Nodes (1): RolesPermissionsLivewireServiceProvider

### Community 621 - "Community 621"
Cohesion: 0.67
Nodes (1): SchedulerQueuesApiServiceProvider

### Community 622 - "Community 622"
Cohesion: 0.67
Nodes (1): SchedulerQueuesFilamentServiceProvider

### Community 623 - "Community 623"
Cohesion: 0.67
Nodes (1): SchedulerQueuesLivewireServiceProvider

### Community 624 - "Community 624"
Cohesion: 0.67
Nodes (1): SchedulerQueuesServiceProvider

### Community 625 - "Community 625"
Cohesion: 0.67
Nodes (1): SearchApiServiceProvider

### Community 626 - "Community 626"
Cohesion: 0.67
Nodes (1): SearchFilamentServiceProvider

### Community 627 - "Community 627"
Cohesion: 0.67
Nodes (1): SearchLivewireServiceProvider

### Community 628 - "Community 628"
Cohesion: 0.67
Nodes (1): SessionsDevicesApiServiceProvider

### Community 629 - "Community 629"
Cohesion: 0.67
Nodes (1): SessionsDevicesFilamentServiceProvider

### Community 630 - "Community 630"
Cohesion: 0.67
Nodes (1): SessionsDevicesLivewireServiceProvider

### Community 631 - "Community 631"
Cohesion: 0.67
Nodes (1): SettingsApiServiceProvider

### Community 632 - "Community 632"
Cohesion: 0.67
Nodes (1): SettingsLivewireServiceProvider

### Community 633 - "Community 633"
Cohesion: 0.67
Nodes (1): SettingsServiceProvider

### Community 634 - "Community 634"
Cohesion: 0.67
Nodes (1): ThemeSupportLivewireServiceProvider

### Community 635 - "Community 635"
Cohesion: 0.67
Nodes (1): TwoFactorAuthenticationApiServiceProvider

### Community 636 - "Community 636"
Cohesion: 0.67
Nodes (1): TwoFactorAuthenticationFilamentServiceProvider

### Community 637 - "Community 637"
Cohesion: 0.67
Nodes (1): TwoFactorAuthenticationLivewireServiceProvider

### Community 638 - "Community 638"
Cohesion: 0.67
Nodes (1): WebhooksApiServiceProvider

### Community 639 - "Community 639"
Cohesion: 0.67
Nodes (1): WebhooksFilamentServiceProvider

### Community 640 - "Community 640"
Cohesion: 0.67
Nodes (1): WebhooksLivewireServiceProvider

### Community 641 - "Community 641"
Cohesion: 0.67
Nodes (1): WebhooksServiceProvider

### Community 642 - "Community 642"
Cohesion: 0.67
Nodes (1): AuditContext

### Community 643 - "Community 643"
Cohesion: 0.67
Nodes (1): ConsentPolicy

### Community 644 - "Community 644"
Cohesion: 0.67
Nodes (1): DatabaseAuditRecorder

### Community 645 - "Community 645"
Cohesion: 0.67
Nodes (1): EnvironmentDoctor

### Community 646 - "Community 646"
Cohesion: 0.67
Nodes (1): EnvironmentValidator

### Community 647 - "Community 647"
Cohesion: 0.67
Nodes (1): FlagEvaluator

### Community 648 - "Community 648"
Cohesion: 0.67
Nodes (1): GoogleEventMapper

### Community 649 - "Community 649"
Cohesion: 0.67
Nodes (1): IdentifierNormalizer

### Community 650 - "Community 650"
Cohesion: 0.67
Nodes (1): Redactor

### Community 651 - "Community 651"
Cohesion: 0.67
Nodes (1): RejectingInvitationValidator

### Community 652 - "Community 652"
Cohesion: 0.67
Nodes (1): RejectingMalwareScanner

### Community 653 - "Community 653"
Cohesion: 0.67
Nodes (1): RetrySchedule

### Community 654 - "Community 654"
Cohesion: 0.67
Nodes (1): SystemClock

### Community 655 - "Community 655"
Cohesion: 0.67
Nodes (1): UploadPolicy

### Community 656 - "Community 656"
Cohesion: 0.67
Nodes (1): UuidIdentifierFactory

### Community 657 - "Community 657"
Cohesion: 1.00
Nodes (2): coverageThemePackage(), writeCoverageTheme()

### Community 659 - "Community 659"
Cohesion: 0.67
Nodes (1): ExampleTest

### Community 660 - "Community 660"
Cohesion: 0.67
Nodes (1): Currency

### Community 661 - "Community 661"
Cohesion: 1.00
Nodes (1): Code-level Conformance Audit

### Community 662 - "Community 662"
Cohesion: 1.00
Nodes (1): /api/v1/real-estate Versioned Routes

### Community 663 - "Community 663"
Cohesion: 1.00
Nodes (1): Sweep AI automation

### Community 664 - "Community 664"
Cohesion: 1.00
Nodes (1): Real Estate Legacy Parity Note

### Community 665 - "Community 665"
Cohesion: 1.00
Nodes (1): Sanctum Bearer Security Scheme

## Ambiguous Edges - Review These
- `OpenAPI v1: Real Estate OnTheMarket Sync API` → `Real Estate Portals and Reporting (core module)`  [AMBIGUOUS]
  modules/real-estate-onthemarket-api/openapi/v1/real-estate-onthemarket.yaml · relation: conceptually_related_to
- `liberusoftware/boilerplate-scripts` → `scripts/fleet`  [AMBIGUOUS]
  scripts/README.md · relation: conceptually_related_to
- `Admin user-management enhancement` → `Host boundary: /app is composition only`  [AMBIGUOUS]
  docs/ADMIN_PANEL_ENHANCEMENTS.md · relation: conceptually_related_to
- `Session handoffs in docs/handoffs/` → `lerd local PHP development environment`  [AMBIGUOUS]
  CLAUDE.md · relation: conceptually_related_to
- `Full-text indexes` → `Post model`  [AMBIGUOUS]
  docs/SEARCH_OPTIMIZATION.md · relation: conceptually_related_to
- `Explicit module enablement` → `Reproducible composition from the lockfile`  [AMBIGUOUS]
  docs/MODULE_DEVELOPMENT.md · relation: conceptually_related_to
- `modules/search package` → `liberusoftware/module-search-demo`  [AMBIGUOUS]
  docs/SEARCH_FUNCTIONALITY.md · relation: conceptually_related_to
- `Real-time notification system` → `Private Messaging System`  [AMBIGUOUS]
  docs/IMPLEMENTATION_SUMMARY.md · relation: shares_data_with
- `Enabled By Default Declaration` → `Installation Does Not Imply Enablement`  [AMBIGUOUS]
  modules/module-manager/README.md · relation: conceptually_related_to
- `Liberu Identity Administration (identity-filament)` → `Liberu Identity`  [AMBIGUOUS]
  modules/identity-core-filament/README.md · relation: conceptually_related_to
- `Instruction Record` → `Branch Resource`  [AMBIGUOUS]
  modules/real-estate-instructions/README.md · relation: conceptually_related_to
- `Liberu Application Composition` → `Real Estate Zoopla`  [AMBIGUOUS]
  projects/LIBERU.md · relation: references
- `Real Estate Property Management Module` → `Real Estate Properties Module`  [AMBIGUOUS]
  modules/real-estate-property-management/README.md · relation: conceptually_related_to
- `real-estate-default theme` → `Real Estate capability: Portals and Reporting`  [AMBIGUOUS]
  themes/real-estate-default/README.md · relation: conceptually_related_to
- `Website feed (phase 1 deliverable)` → `Real Estate capability: Portals and Reporting`  [AMBIGUOUS]
  projects/real-estate/REAL-ESTATE.md · relation: conceptually_related_to

## Knowledge Gaps
- **185 isolated node(s):** `php`, `/usr/local/bin/php`, `CurrencyMismatch`, `UnknownCurrency`, `IdentityFilamentServiceProvider` (+180 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **Thin community `Common CRUD Action Verbs`** (1 nodes): `decbeb1 Локальные изменения перед обновлением`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Property Model Behavior`** (1 nodes): `Property`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ThemeManager Service`** (1 nodes): `ThemeManager`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Public Property Controller`** (1 nodes): `PropertyController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Module Manifest Parser`** (1 nodes): `Manifest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Team Policy`** (1 nodes): `TeamPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Public Property List Livewire`** (1 nodes): `PropertyList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Valuation Controller`** (1 nodes): `ValuationController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `User Model Authorization`** (1 nodes): `User`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ThemeManifest Value Object`** (1 nodes): `ThemeManifest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Agency Policy`** (1 nodes): `AgencyPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Branch Policy`** (1 nodes): `BranchPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Party Policy`** (1 nodes): `PartyPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Portal Report Policy`** (1 nodes): `PortalReportPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Property Category Policy`** (1 nodes): `PropertyCategoryPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Property Policy`** (1 nodes): `PropertyPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Property Template Policy`** (1 nodes): `PropertyTemplatePolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Role Policy`** (1 nodes): `RolePolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Sales Progression Policy`** (1 nodes): `SalesProgressionPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Status Definition Policy`** (1 nodes): `StatusDefinitionPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Territory Policy`** (1 nodes): `TerritoryPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Valuation Policy`** (1 nodes): `ValuationPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Localization Translation Files`** (1 nodes): `11b55b8 Area 4: tg/uz localization files`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Public Property Detail Livewire`** (1 nodes): `PropertyDetail`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Instruction Policy`** (1 nodes): `InstructionPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Listing Policy`** (1 nodes): `ListingPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Marketing Campaign Policy`** (1 nodes): `MarketingCampaignPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Match Profile Policy`** (1 nodes): `MatchProfilePolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Media Document Policy`** (1 nodes): `MediaDocumentPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Offer Policy`** (1 nodes): `OfferPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `User Policy`** (1 nodes): `UserPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Viewing Policy`** (1 nodes): `ViewingPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Viewing Controller`** (1 nodes): `ViewingController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Property Tax Estimator Action`** (1 nodes): `EstimatePropertyTax`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Public Property Comparison Livewire`** (1 nodes): `PropertyComparison`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Real Estate Migrations Batch`** (1 nodes): `e3111ed Fix three merge-added migrations exceeding MySQL's 64-char identifier limit`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Price Alert Manager Livewire`** (1 nodes): `PriceAlertManager`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Media Document Controller`** (1 nodes): `MediaDocumentController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Party Controller`** (1 nodes): `PartyController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Rental Application Controller`** (1 nodes): `RentalApplicationController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `HomeReport Model`** (1 nodes): `HomeReport`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `MediaDocument Model`** (1 nodes): `MediaDocument`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ActivityLogResource`** (1 nodes): `ActivityLogResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ModuleRegistry Service`** (1 nodes): `ModuleRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Match Score Calculator`** (1 nodes): `CalculateMatchScore`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Lease Agreement Controller`** (1 nodes): `LeaseAgreementController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Match Profile Controller`** (1 nodes): `MatchProfileController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Offer Controller`** (1 nodes): `OfferController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Work Order Controller`** (1 nodes): `WorkOrderController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `PropertyHistory Model`** (1 nodes): `PropertyHistory`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Account Setup Wizard`** (1 nodes): `AccountSetupWizard`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `NewsArticleResource`** (1 nodes): `NewsArticleResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `PropertyResource`** (1 nodes): `PropertyResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Property Valuation Prediction Action`** (1 nodes): `GeneratePropertyValuation`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Shield Policy Generation Commit`** (1 nodes): `b97f8da Generate Shield policies for the 13 previously-unregistered resources`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Advanced Property Search Livewire`** (1 nodes): `AdvancedPropertySearch`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Listing List Livewire`** (1 nodes): `ListingList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Listing Controller`** (1 nodes): `ListingController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Marketing Campaign Controller`** (1 nodes): `MarketingCampaignController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Portal Report Controller`** (1 nodes): `PortalReportController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Rental Charge Controller`** (1 nodes): `RentalChargeController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Sales Progression Controller`** (1 nodes): `SalesProgressionController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `SearchController`** (1 nodes): `SearchController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Vendor Quote Controller`** (1 nodes): `VendorQuoteController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Searcher Registry Tests`** (1 nodes): `SearcherRegistryTest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `CommunityEvent Model`** (1 nodes): `CommunityEvent`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Viewing Model`** (1 nodes): `Viewing`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `AgencyResource`** (1 nodes): `AgencyResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `BranchResource`** (1 nodes): `BranchResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `InspectionResource`** (1 nodes): `InspectionResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `InstructionResource`** (1 nodes): `InstructionResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `LettingResource`** (1 nodes): `LettingResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ListingResource`** (1 nodes): `ListingResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `MaintenanceRequestResource`** (1 nodes): `MaintenanceRequestResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ManagementRecordResource`** (1 nodes): `ManagementRecordResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `MarketingCampaignResource`** (1 nodes): `MarketingCampaignResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `MatchProfileResource`** (1 nodes): `MatchProfileResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `MediaDocumentResource`** (1 nodes): `MediaDocumentResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `OfferResource`** (1 nodes): `OfferResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `OnTheMarketSyncResource`** (1 nodes): `OnTheMarketSyncResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `PartyResource`** (1 nodes): `PartyResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `PortalReportResource`** (1 nodes): `PortalReportResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `PropertyCategoryResource`** (1 nodes): `PropertyCategoryResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `PropertySavedSearchResource`** (1 nodes): `PropertySavedSearchResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `PropertyTemplateResource`** (1 nodes): `PropertyTemplateResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `RentalApplicationResource`** (1 nodes): `RentalApplicationResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `RightmoveSyncResource`** (1 nodes): `RightmoveSyncResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `SalesProgressionResource`** (1 nodes): `SalesProgressionResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `TeamResource`** (1 nodes): `TeamResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `TerritoryResource`** (1 nodes): `TerritoryResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `UserResource`** (1 nodes): `UserResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ValuationResource`** (1 nodes): `ValuationResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `VendorQuoteResource`** (1 nodes): `VendorQuoteResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ViewingResource`** (1 nodes): `ViewingResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `WorkOrderResource`** (1 nodes): `WorkOrderResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ZooplaSyncResource`** (1 nodes): `ZooplaSyncResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `TranslationService`** (1 nodes): `TranslationService`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Money Value Object`** (1 nodes): `Money`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Walkability Score Fetcher`** (1 nodes): `FetchWalkabilityScores`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Instruction List Livewire`** (1 nodes): `InstructionList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Property Map Livewire`** (1 nodes): `PropertyMap`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Property Submission Form Livewire`** (1 nodes): `PropertySubmissionForm`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Property Tax Estimator Livewire`** (1 nodes): `PropertyTaxEstimator`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Agency Controller`** (1 nodes): `AgencyController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Inspection Controller`** (1 nodes): `InspectionController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Instruction Controller`** (1 nodes): `InstructionController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Letting Controller`** (1 nodes): `LettingController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Maintenance Request Controller`** (1 nodes): `MaintenanceRequestController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Management Record Controller`** (1 nodes): `ManagementRecordController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `OnTheMarket Sync Controller`** (1 nodes): `OnTheMarketSyncController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Rightmove Sync Controller`** (1 nodes): `RightmoveSyncController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Territory Controller`** (1 nodes): `TerritoryController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Zoopla Sync Controller`** (1 nodes): `ZooplaSyncController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Neighborhood Model`** (1 nodes): `Neighborhood`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Party Model`** (1 nodes): `Party`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `RentalApplication Model`** (1 nodes): `RentalApplication`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Connected Account Policy`** (1 nodes): `ConnectedAccountPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `StatusDefinitionResource`** (1 nodes): `StatusDefinitionResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `SearchService`** (1 nodes): `SearchService`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `RegistryCache`** (1 nodes): `RegistryCache`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `ThemeCache`** (1 nodes): `ThemeCache`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Public Calculators Livewire`** (1 nodes): `Calculators`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Media Document List Livewire`** (1 nodes): `MediaDocumentList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Offer List Livewire`** (1 nodes): `OfferList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Valuation List Livewire`** (1 nodes): `ValuationList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Viewing List Livewire`** (1 nodes): `ViewingList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Branch Controller`** (1 nodes): `BranchController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Saved Report Controller`** (1 nodes): `SavedReportController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `UserFactory`** (1 nodes): `UserFactory`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `SyncNewUserToCrm Job`** (1 nodes): `SyncNewUserToCrm`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `NeighborhoodReview Model`** (1 nodes): `NeighborhoodReview`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `NewsArticle Model`** (1 nodes): `NewsArticle`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Offer Model`** (1 nodes): `Offer`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `PartyReview Model`** (1 nodes): `PartyReview`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 200`** (1 nodes): `PropertyReview`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 201`** (1 nodes): `FoundationOperations`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 202`** (1 nodes): `ThemeServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 203`** (1 nodes): `SessionReader`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 204`** (1 nodes): `IdentityFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 205`** (1 nodes): `ModuleManagerFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 206`** (1 nodes): `OrganizationsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 207`** (1 nodes): `PartiesFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 208`** (1 nodes): `SessionsDevicesFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 209`** (1 nodes): `SettingsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 210`** (1 nodes): `TestCase`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 211`** (1 nodes): `OnTheMarketClient`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 212`** (1 nodes): `RightmoveClient`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 213`** (1 nodes): `ZooplaClient`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 214`** (1 nodes): `CreateUserFromProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 226`** (1 nodes): `NeighborhoodReviewForm`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 227`** (1 nodes): `PartyList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 228`** (1 nodes): `PartyReviewForm`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 229`** (1 nodes): `PropertyRecommendations`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 230`** (1 nodes): `PropertyReviewForm`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 231`** (1 nodes): `PropertyValuationEstimator`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 232`** (1 nodes): `ViewingBooking`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 233`** (1 nodes): `WishlistManager`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 235`** (1 nodes): `ContactController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 236`** (1 nodes): `CoreConfigurationController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 237`** (1 nodes): `NewsArticleController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 238`** (1 nodes): `PropertyCategoryController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 239`** (1 nodes): `PropertyPriceAlertController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 240`** (1 nodes): `PropertyTemplateController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 241`** (1 nodes): `VirtualStagingController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 242`** (1 nodes): `ThemeDiscovery`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 243`** (1 nodes): `PropertyGalleryItem`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 244`** (1 nodes): `LocaleFormatter`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 245`** (1 nodes): `UpdateUserProfileInformation`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 246`** (1 nodes): `ReadinessRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 247`** (1 nodes): `DeleteUser`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 248`** (1 nodes): `LanguageSwitcher`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 249`** (1 nodes): `ThemeSwitcher`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 250`** (1 nodes): `ActivityLogEntry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 251`** (1 nodes): `LeaseAgreement`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 253`** (1 nodes): `PropertyPriceAlert`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 254`** (1 nodes): `Valuation`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 255`** (1 nodes): `VendorQuote`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 256`** (1 nodes): `WorkOrder`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 257`** (1 nodes): `AccountSecurity`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 258`** (1 nodes): `ManageSiteSettings`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 259`** (1 nodes): `LettingPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 260`** (1 nodes): `ManagementRecordPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 261`** (1 nodes): `TelescopeDashboardServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 262`** (1 nodes): `RecoveryCodeHasher`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 263`** (1 nodes): `SearcherRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 264`** (1 nodes): `LocalSearchIndexer`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 265`** (1 nodes): `ActivityCommentsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 266`** (1 nodes): `AnalyticsCoreFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 267`** (1 nodes): `AnalyticsGoogleFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 268`** (1 nodes): `AnalyticsMetaFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 269`** (1 nodes): `ApiAccessFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 270`** (1 nodes): `ApplicationFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 271`** (1 nodes): `AuditFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 272`** (1 nodes): `CurrencyContextFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 273`** (1 nodes): `DeveloperExperienceFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 274`** (1 nodes): `FeatureFlagsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 275`** (1 nodes): `FilesMediaFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 276`** (1 nodes): `ImportExportFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 277`** (1 nodes): `IntegrationsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 278`** (1 nodes): `JetstreamBridgeFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 279`** (1 nodes): `LettingsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 280`** (1 nodes): `LocalizationCoreFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 281`** (1 nodes): `NotificationsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 282`** (1 nodes): `ObservabilityFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 283`** (1 nodes): `OnTheMarketFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 284`** (1 nodes): `PortalsReportingFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 285`** (1 nodes): `ProfilesFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 286`** (1 nodes): `PropertiesFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 287`** (1 nodes): `PropertyManagementFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 288`** (1 nodes): `RealEstateCoreFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 289`** (1 nodes): `RightmoveFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 290`** (1 nodes): `SalesProgressionFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 291`** (1 nodes): `SchedulerQueuesFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 292`** (1 nodes): `SearchFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 293`** (1 nodes): `TwoFactorAuthenticationFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 294`** (1 nodes): `WebhooksFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 295`** (1 nodes): `ZooplaFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 296`** (1 nodes): `ConfiguredRegistrationPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 297`** (1 nodes): `DestinationRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 298`** (1 nodes): `GoogleDestination`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 299`** (1 nodes): `IntegrationRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 300`** (1 nodes): `MetaCustomerNormalizer`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 301`** (1 nodes): `MetaDestination`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 302`** (1 nodes): `TranslationRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 303`** (1 nodes): `TrustedDeviceManager`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 306`** (1 nodes): `UpdateConnectedAccount`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 307`** (1 nodes): `GenerateNeuralPropertyValuation`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 308`** (1 nodes): `GeneratePropertyDescription`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 309`** (1 nodes): `SendPropertyToFriend`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 310`** (1 nodes): `SyncOnTheMarketListing`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 311`** (1 nodes): `SyncRightmoveListing`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 312`** (1 nodes): `SyncZooplaListing`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 314`** (1 nodes): `ContactEnquiryForm`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 315`** (1 nodes): `MarketingCampaignList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 316`** (1 nodes): `MatchProfileList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 317`** (1 nodes): `MortgageCalculator`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 318`** (1 nodes): `PortalReportList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 319`** (1 nodes): `PropertyPreview`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 320`** (1 nodes): `RentalApplicationForm`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 321`** (1 nodes): `RentalYieldCalculator`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 322`** (1 nodes): `SalesProgressionList`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 324`** (1 nodes): `LocaleContext`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 331`** (1 nodes): `CalendarEntryController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 332`** (1 nodes): `CommunicationController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 333`** (1 nodes): `CommunityEventController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 334`** (1 nodes): `HomeReportController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 335`** (1 nodes): `PropertySavedSearchController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 336`** (1 nodes): `CoreCapabilityDefinition`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 337`** (1 nodes): `ContentPagesTest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 339`** (1 nodes): `ModulePlugins`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 340`** (1 nodes): `OrganizationUser`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 341`** (1 nodes): `CreateNewUser`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 342`** (1 nodes): `PropertyShareMail`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 343`** (1 nodes): `SetLocale`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 345`** (1 nodes): `Agency`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 346`** (1 nodes): `Branch`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 347`** (1 nodes): `CalendarEntry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 348`** (1 nodes): `Communication`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 349`** (1 nodes): `Contact`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 350`** (1 nodes): `DocumentTemplate`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 351`** (1 nodes): `Inspection`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 352`** (1 nodes): `Instruction`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 353`** (1 nodes): `Letting`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 354`** (1 nodes): `Listing`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 355`** (1 nodes): `MaintenanceRequest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 356`** (1 nodes): `ManagementRecord`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 357`** (1 nodes): `MarketingCampaign`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 358`** (1 nodes): `MatchProfile`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 359`** (1 nodes): `OnTheMarketSync`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 360`** (1 nodes): `Organization`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 361`** (1 nodes): `PortalReport`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 362`** (1 nodes): `PropertySavedSearch`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 363`** (1 nodes): `RentalCharge`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 364`** (1 nodes): `RightmoveSync`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 365`** (1 nodes): `SalesProgression`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 366`** (1 nodes): `SavedReport`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 367`** (1 nodes): `StatusDefinition`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 368`** (1 nodes): `TeamInvitation`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 369`** (1 nodes): `Team`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 370`** (1 nodes): `Territory`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 371`** (1 nodes): `ViewingFeedback`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 372`** (1 nodes): `ZooplaSync`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 373`** (1 nodes): `EditNewsArticle`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 374`** (1 nodes): `FortifyServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 375`** (1 nodes): `HorizonDashboardServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 376`** (1 nodes): `IndexableRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 377`** (1 nodes): `PermissionRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 378`** (1 nodes): `BreakGlass`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 379`** (1 nodes): `CurrencyContext`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 380`** (1 nodes): `CurrencyRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 381`** (1 nodes): `MoneyFormatter`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 382`** (1 nodes): `OpenExchangeRateApiProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 383`** (1 nodes): `ScopedSettings`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 384`** (1 nodes): `AnalyticsServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 385`** (1 nodes): `ApplicationCoreServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 386`** (1 nodes): `AuditServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 387`** (1 nodes): `CurrencyServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 388`** (1 nodes): `FilesMediaServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 389`** (1 nodes): `IdentityServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 390`** (1 nodes): `InstructionsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 391`** (1 nodes): `IntegrationsServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 392`** (1 nodes): `LettingsServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 393`** (1 nodes): `ListingsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 394`** (1 nodes): `LocalizationServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 395`** (1 nodes): `MarketingFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 396`** (1 nodes): `MatchingFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 397`** (1 nodes): `MediaAndDocumentsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 398`** (1 nodes): `ModuleManagerServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 399`** (1 nodes): `ModuleValidationGuard`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 400`** (1 nodes): `ObservabilityServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 401`** (1 nodes): `OffersFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 402`** (1 nodes): `OrganizationsServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 403`** (1 nodes): `PartiesServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 404`** (1 nodes): `RolesPermissionsServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 405`** (1 nodes): `SearchServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 406`** (1 nodes): `SessionsDevicesServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 407`** (1 nodes): `TwoFactorServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 408`** (1 nodes): `ValuationsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 409`** (1 nodes): `ViewingsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 410`** (1 nodes): `DeliveryRetry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 411`** (1 nodes): `EventRouter`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 412`** (1 nodes): `EventSanitizer`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 413`** (1 nodes): `IdempotencyStore`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 414`** (1 nodes): `JobPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 415`** (1 nodes): `NotificationPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 416`** (1 nodes): `NullMetrics`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 417`** (1 nodes): `RowValidator`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 418`** (1 nodes): `SloRegistry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 419`** (1 nodes): `TeamIntegrationSettings`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 420`** (1 nodes): `ThemeColors`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 421`** (1 nodes): `WebhookSigner`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 422`** (1 nodes): `ExchangeRate`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 423`** (1 nodes): `RoleWelcomeWidget`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 424`** (1 nodes): `AcceptInvitation`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 425`** (1 nodes): `CreateConnectedAccount`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 426`** (1 nodes): `GenerateRedirectForProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 427`** (1 nodes): `HandleInvalidState`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 428`** (1 nodes): `InviteMember`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 429`** (1 nodes): `ResolveSocialiteUser`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 430`** (1 nodes): `TransferOwnership`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 431`** (1 nodes): `UpdateProfile`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 432`** (1 nodes): `CheckPriceAlerts`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 433`** (1 nodes): `CreateCalendarEntry`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 434`** (1 nodes): `CreateContactMessage`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 435`** (1 nodes): `CreateInspection`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 436`** (1 nodes): `CreateMaintenanceRequest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 437`** (1 nodes): `CreatePriceAlert`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 438`** (1 nodes): `CreateRentalCharge`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 439`** (1 nodes): `CreateVendorQuote`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 440`** (1 nodes): `DecideRentalApplication`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 441`** (1 nodes): `DecideVendorQuote`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 442`** (1 nodes): `DeletePropertySearch`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 443`** (1 nodes): `GeneratePropertyQrCode`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 444`** (1 nodes): `SubmitPropertyReview`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 445`** (1 nodes): `TransitionInstruction`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 446`** (1 nodes): `TransitionListing`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 447`** (1 nodes): `TransitionProperty`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 448`** (1 nodes): `UpdateMaintenanceRequest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 449`** (1 nodes): `UpdateRentalApplicationScreening`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 450`** (1 nodes): `UploadHomeReportFile`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 451`** (2 nodes): `scopeSearch()`, `searchableColumns()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 452`** (1 nodes): `CacheModulesCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 453`** (1 nodes): `ClearModulesCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 454`** (1 nodes): `FoundationDoctorCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 455`** (1 nodes): `ListFeaturesCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 456`** (1 nodes): `ListModulesCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 457`** (1 nodes): `ModuleStatusCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 458`** (1 nodes): `ReindexCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 459`** (1 nodes): `ThemeCacheCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 460`** (1 nodes): `ThemeClearCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 461`** (1 nodes): `ThemeValidateCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 462`** (1 nodes): `ValidateModulesCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 463`** (1 nodes): `LocaleResolver`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 467`** (1 nodes): `ReadinessController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 468`** (1 nodes): `ProfileUpdate`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 469`** (1 nodes): `TransferSchema`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 470`** (1 nodes): `TwoFactorPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 471`** (1 nodes): `IdentityEvent`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 472`** (1 nodes): `ConnectedAccountFactory`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 473`** (1 nodes): `TeamFactory`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 474`** (1 nodes): `TestPanelProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 476`** (1 nodes): `EmitAuthenticationEvent`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 477`** (1 nodes): `CorrelationId`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 478`** (1 nodes): `SecurityHeaders`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 526`** (1 nodes): `ConnectedAccount`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 527`** (1 nodes): `ContactMessage`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 530`** (1 nodes): `WorkOrderUpdate`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 531`** (1 nodes): `CreatePropertySavedSearch`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 532`** (1 nodes): `CreateUser`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 533`** (1 nodes): `EditPropertySavedSearch`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 534`** (1 nodes): `EditTeam`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 535`** (1 nodes): `EditUser`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 536`** (1 nodes): `ListTeams`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 537`** (1 nodes): `ListUsers`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 538`** (1 nodes): `ListLettings`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 539`** (1 nodes): `ListManagementRecords`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 540`** (1 nodes): `CommunityEventResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 541`** (1 nodes): `ContactResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 542`** (1 nodes): `LeaseAgreementResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 543`** (1 nodes): `PropertyHistoryResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 544`** (1 nodes): `PropertyPriceAlertResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 545`** (1 nodes): `PropertyQrCodeResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 546`** (1 nodes): `PropertyShareResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 547`** (1 nodes): `RentalChargeResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 548`** (1 nodes): `WorkOrderUpdateResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 549`** (1 nodes): `AnyTeamRoleLookup`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 550`** (1 nodes): `CurrencyPreferenceResolver`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 551`** (1 nodes): `CurrentTeamResolver`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 552`** (1 nodes): `SeparationOfDuty`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 553`** (1 nodes): `ActivityCommentsApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 554`** (1 nodes): `ActivityCommentsLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 555`** (1 nodes): `ActivityCommentsServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 556`** (1 nodes): `AnalyticsCoreApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 557`** (1 nodes): `AnalyticsCoreLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 558`** (1 nodes): `AnalyticsGoogleApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 559`** (1 nodes): `AnalyticsGoogleLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 560`** (1 nodes): `AnalyticsGoogleServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 561`** (1 nodes): `AnalyticsMetaApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 562`** (1 nodes): `AnalyticsMetaFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 563`** (1 nodes): `AnalyticsMetaLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 564`** (1 nodes): `AnalyticsMetaServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 565`** (1 nodes): `ApiAccessApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 566`** (1 nodes): `ApiAccessLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 567`** (1 nodes): `ApplicationApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 568`** (1 nodes): `ApplicationLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 569`** (1 nodes): `AuditApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 570`** (1 nodes): `AuditLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 571`** (1 nodes): `CurrencyContextFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 572`** (1 nodes): `CurrencyContextLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 573`** (1 nodes): `DeveloperExperienceApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 574`** (1 nodes): `DeveloperExperienceLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 575`** (1 nodes): `DeveloperExperienceServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 576`** (1 nodes): `FeatureFlagsFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 577`** (1 nodes): `FeatureFlagsLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 578`** (1 nodes): `FeatureFlagsServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 579`** (1 nodes): `FilesMediaApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 580`** (1 nodes): `FilesMediaLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 581`** (1 nodes): `IdentityCoreApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 582`** (1 nodes): `IdentityCoreLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 583`** (1 nodes): `ImportExportApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 584`** (1 nodes): `ImportExportFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 585`** (1 nodes): `ImportExportLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 586`** (1 nodes): `ImportExportServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 587`** (1 nodes): `IntegrationsApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 588`** (1 nodes): `IntegrationsLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 589`** (1 nodes): `JetstreamBridgeApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 590`** (1 nodes): `JetstreamBridgeFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 591`** (1 nodes): `JetstreamBridgeLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 592`** (1 nodes): `LettingsApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 593`** (1 nodes): `LettingsFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 594`** (1 nodes): `LocalizationCoreApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 595`** (1 nodes): `LocalizationLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 596`** (1 nodes): `MediaAndDocumentsApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 597`** (1 nodes): `ModuleDiscovery`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 598`** (1 nodes): `ModuleManagerApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 599`** (1 nodes): `ModuleManagerFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 600`** (1 nodes): `ModuleManagerLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 601`** (1 nodes): `ModuleValidator`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 602`** (1 nodes): `MyMemoryServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 603`** (1 nodes): `NotificationsApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 604`** (1 nodes): `NotificationsFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 605`** (1 nodes): `NotificationsLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 606`** (1 nodes): `NotificationsServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 607`** (1 nodes): `ObservabilityApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 608`** (1 nodes): `ObservabilityLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 609`** (1 nodes): `OrganizationsTeamsApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 610`** (1 nodes): `OrganizationsTeamsLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 611`** (1 nodes): `ProfilesApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 612`** (1 nodes): `ProfilesFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 613`** (1 nodes): `ProfilesLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 614`** (1 nodes): `ProfilesServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 615`** (1 nodes): `PropertyManagementApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 616`** (1 nodes): `PropertyManagementFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 617`** (1 nodes): `RightmoveServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 618`** (1 nodes): `RolesPermissionsApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 619`** (1 nodes): `RolesPermissionsFilamentPlugin`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 620`** (1 nodes): `RolesPermissionsLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 621`** (1 nodes): `SchedulerQueuesApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 622`** (1 nodes): `SchedulerQueuesFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 623`** (1 nodes): `SchedulerQueuesLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 624`** (1 nodes): `SchedulerQueuesServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 625`** (1 nodes): `SearchApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 626`** (1 nodes): `SearchFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 627`** (1 nodes): `SearchLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 628`** (1 nodes): `SessionsDevicesApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 629`** (1 nodes): `SessionsDevicesFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 630`** (1 nodes): `SessionsDevicesLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 631`** (1 nodes): `SettingsApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 632`** (1 nodes): `SettingsLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 633`** (1 nodes): `SettingsServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 634`** (1 nodes): `ThemeSupportLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 635`** (1 nodes): `TwoFactorAuthenticationApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 636`** (1 nodes): `TwoFactorAuthenticationFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 637`** (1 nodes): `TwoFactorAuthenticationLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 638`** (1 nodes): `WebhooksApiServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 639`** (1 nodes): `WebhooksFilamentServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 640`** (1 nodes): `WebhooksLivewireServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 641`** (1 nodes): `WebhooksServiceProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 642`** (1 nodes): `AuditContext`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 643`** (1 nodes): `ConsentPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 644`** (1 nodes): `DatabaseAuditRecorder`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 645`** (1 nodes): `EnvironmentDoctor`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 646`** (1 nodes): `EnvironmentValidator`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 647`** (1 nodes): `FlagEvaluator`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 648`** (1 nodes): `GoogleEventMapper`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 649`** (1 nodes): `IdentifierNormalizer`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 650`** (1 nodes): `Redactor`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 651`** (1 nodes): `RejectingInvitationValidator`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 652`** (1 nodes): `RejectingMalwareScanner`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 653`** (1 nodes): `RetrySchedule`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 654`** (1 nodes): `SystemClock`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 655`** (1 nodes): `UploadPolicy`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 656`** (1 nodes): `UuidIdentifierFactory`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 657`** (2 nodes): `coverageThemePackage()`, `writeCoverageTheme()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 659`** (1 nodes): `ExampleTest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 660`** (1 nodes): `Currency`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 661`** (1 nodes): `Code-level Conformance Audit`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 662`** (1 nodes): `/api/v1/real-estate Versioned Routes`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 663`** (1 nodes): `Sweep AI automation`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 664`** (1 nodes): `Real Estate Legacy Parity Note`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 665`** (1 nodes): `Sanctum Bearer Security Scheme`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `OpenAPI v1: Real Estate OnTheMarket Sync API` and `Real Estate Portals and Reporting (core module)`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **What is the exact relationship between `liberusoftware/boilerplate-scripts` and `scripts/fleet`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **What is the exact relationship between `Admin user-management enhancement` and `Host boundary: /app is composition only`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **What is the exact relationship between `Session handoffs in docs/handoffs/` and `lerd local PHP development environment`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **What is the exact relationship between `Full-text indexes` and `Post model`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **What is the exact relationship between `Explicit module enablement` and `Reproducible composition from the lockfile`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **What is the exact relationship between `modules/search package` and `liberusoftware/module-search-demo`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._