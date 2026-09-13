# ihona — Иерархическая структура проекта

> Построено вручную поверх графа graphify: 200 именованных сообществ из 666 сгруппированы
> по реальной архитектуре хоста (см. [[CLAUDE.md Architecture Notes]]), а не по алгоритмической
> кластеризации напрямую. Ссылки — на заметки графа. См. [[index]] для полного плоского списка
> всех 666 сообществ по размеру.

---

## 1. Composition Host (`ihona`) — `app/` + инфраструктура

Хост держит всего 5 файлов в `app/`; почти всё остальное — 40 модулей + 4 темы.

- **Модульная система**: [[Module Manifest Parser]] · [[ModuleRegistry Service]] · [[Module Discovery and Enablement]] · [[Filament Module Plugin System]] · [[RegistryCache]]
- **Capability-контракты**: [[Foundation Module Capabilities]] · [[Foundation Capability Declarations]] · [[Capability Definitions per Module]] · [[Liberu Module Package Registry]]
- **Границы хоста**: [[Composition Host Boundaries]] · [[User Model Authorization]] (host `App\Models\User`)
- **Архитектура и conformance-документация**: [[CLAUDE.md Architecture Notes]] · [[Conformance Naming Decisions]] · [[Conformance Audit Findings]] · [[Composer Package Boundary Rules]] · [[Architecture Rule Consolidation]] · [[Developer Experience Conformance]]
- **Dev-окружение**: [[Lerd Dev Environment Config]] · [[Ops Tooling and Boost Guidelines]]

---

## 2. Foundation-модули (не доменные, инфраструктурные пакеты)

### 2.1 Identity & Auth
- **identity-core**: [[User Policy]] · [[UserFactory]] · [[Connected Account Policy]] · [[SyncNewUserToCrm Job]] (синхронизация с внешней CRM)
- **identity-socialstream**: [[Telegram Login Bridge]] (Telegram Login Widget + OAuth мост в Nuxt SPA)
- **identity-core-filament**: [[UserResource]] · [[Identity Filament Module Scope]]
- **roles-permissions**: [[Role Policy]] · [[Shield Policy Generation Commit]] (Spatie Permission + Filament Shield)

### 2.2 Organizations & Teams
- **organizations-teams**: [[Team Policy]] · [[TeamResource]]

### 2.3 Localization
- **localization-core**: [[Localization Module Support]] · [[Localization Translation Files]] · [[Translation Publishing Commits]]
- **localization-mymemory**: [[TranslationService]] (машинный перевод, выключен по умолчанию)

### 2.4 Themes (`themes/`)
- [[ThemeManager Service]] · [[ThemeManifest Value Object]] · [[ThemeCache]] · [[Theme Helper Functions]] · [[Theme System Conventions]] · [[Theme Package Manifests]] · [[Real Estate Theme Integration]] (тема `real-estate-default`)

### 2.5 Search
- [[Search Module Architecture]] · [[SearchController]] · [[SearchService]] · [[Searcher Registry Tests]]

### 2.6 Messaging & Notifications
- [[Messaging API Architecture]] · [[Notifications and Broadcast Design]]

### 2.7 Settings, Dashboard, Onбординг
- [[Role-Aware Dashboard Seeders]] · [[Property Dashboard Widgets]] · [[Account Setup Wizard]] · [[Home Report and Dashboard Controllers]]

### 2.8 Платежи (контракт, ещё не реализовано)
- [[Payment Gateway Contract]] (`PaymentGateway`/`NullPaymentGateway` — задел на будущее биллинга)

---

## 3. Real Estate — доменные модули (`real-estate-*`)

Каждый домен обычно раскладывается на 4 слоя: **Core** (доменная логика) → **API** (`-api`,
Sanctum-адаптер) → **Filament** (`-filament`, admin UI) → **Livewire** (`-livewire`, публичный UI).

### 3.1 real-estate-core (общая инфраструктура домена)
- [[Agency Policy]] · [[Agency Controller]] · [[AgencyResource]]
- [[Branch Policy]] · [[Branch Controller]] · [[BranchResource]]
- [[Territory Policy]] · [[Territory Controller]] · [[TerritoryResource]]
- [[Status Definition Policy]] · [[StatusDefinitionResource]]

### 3.2 real-estate-properties (самый крупный домен)
- **Ресурс/форма**: [[PropertyResource]] · [[Property Policy]] · [[Property Model Behavior]]
- **Категории/шаблоны/поиск**: [[PropertyCategoryResource]] · [[Property Category Policy]] · [[PropertyTemplateResource]] · [[Property Template Policy]] · [[PropertySavedSearchResource]] · [[Advanced Property Search Livewire]]
- **История изменений**: [[PropertyHistory Model]] · [[Property Create/Update Commits]] · [[Property Deletion and Tax Commits]] · [[Property Favorites Commit History]]
- **Калькуляторы**: [[Property Financial Calculators]] · [[Mortgage and Valuation Calculators]] · [[Property Tax Estimator Action]] · [[Property Tax Estimator Livewire]] · [[Property Valuation Prediction Action]] · [[Public Calculators Livewire]]
- **Публичная витрина**: [[Public Property Controller]] · [[Public Property API Commits]] · [[Public Property List Livewire]] · [[Public Property Detail Livewire]] · [[Public Property Comparison Livewire]] · [[Property Submission Form Livewire]] · [[Property Map Livewire]] · [[Property Presentation Helpers]]
- **Смежные модели**: [[CommunityEvent Model]] · [[Neighborhood Model]] · [[NeighborhoodReview Model]] · [[Walkability Score Fetcher]]

### 3.3 real-estate-listings
- [[ListingResource]] · [[Listing Policy]] · [[Listing Controller]] · [[Listing List Livewire]] · [[Real Estate Listings Scope]]

### 3.4 real-estate-offers
- [[OfferResource]] · [[Offer Policy]] · [[Offer Controller]] · [[Offer List Livewire]] · [[Offer Model]]

### 3.5 real-estate-viewings
- [[ViewingResource]] · [[Viewing Policy]] · [[Viewing Controller]] · [[Viewing List Livewire]] · [[Viewing Model]] · [[Viewing Module Surface]]

### 3.6 real-estate-valuations
- [[ValuationResource]] · [[Valuation Policy]] · [[Valuation Controller]] · [[Valuation List Livewire]] · [[Valuation API Module]]

### 3.7 real-estate-matching
- [[MatchProfileResource]] · [[Match Profile Policy]] · [[Match Profile Controller]] · [[Match Score Calculator]]

### 3.8 real-estate-marketing
- [[MarketingCampaignResource]] · [[Marketing Campaign Policy]] · [[Marketing Campaign Controller]] · [[Marketing Campaign Lifecycle]]
- **Новости** (сегодняшний CRUD-фикс): [[NewsArticleResource]] · [[NewsArticle Model]]

### 3.9 real-estate-portals-reporting
- [[PortalReportResource]] · [[Portal Report Policy]] · [[Portal Report Controller]] · [[Portal Report Module Surface]] · [[Saved Report Controller]]

### 3.10 real-estate-sales-progression
- [[SalesProgressionResource]] · [[Sales Progression Policy]] · [[Sales Progression Controller]]

### 3.11 real-estate-parties
- [[PartyResource]] · [[Party Policy]] · [[Party Controller]] · [[Party Model]] · [[Party Module Surface]] · [[PartyReview Model]]

### 3.12 real-estate-media-and-documents
- [[MediaDocumentResource]] · [[Media Document Policy]] · [[Media Document Controller]] · [[Media Document List Livewire]] · [[Media Document Module Commits]] · [[MediaDocument Model]] · [[HomeReport Model]]

### 3.13 real-estate-instructions
- [[InstructionResource]] · [[Instruction Policy]] · [[Instruction Controller]] · [[Instruction List Livewire]]

### 3.14 real-estate-lettings
- [[LettingResource]] · [[Letting Controller]] · [[Lettings Domain Specification]]
- [[Lease Agreement Controller]] · [[Rental Charge Controller]] · [[RentalApplicationResource]] · [[Rental Application Controller]] · [[RentalApplication Model]]

### 3.15 real-estate-property-management
- [[ManagementRecordResource]] · [[Management Record Controller]]
- [[MaintenanceRequestResource]] · [[Maintenance Request Controller]]
- [[WorkOrderResource]] · [[Work Order Controller]]
- [[VendorQuoteResource]] · [[Vendor Quote Controller]]
- [[InspectionResource]] · [[Inspection Controller]]

### 3.16 Синхронизация с внешними порталами
- **Rightmove**: [[RightmoveSyncResource]] · [[Rightmove Sync Controller]] · [[Real Estate Rightmove Adapter Tier]]
- **Zoopla**: [[ZooplaSyncResource]] · [[Zoopla Sync Controller]] · [[Real Estate Zoopla Adapter Tier]] · [[Real Estate Zoopla API Tier]]
- **OnTheMarket**: [[OnTheMarketSyncResource]] · [[OnTheMarket Sync Controller]] · [[Real Estate OnTheMarket Core Tier]]
- **Общее**: [[Portal Sync Service Providers]] · [[Real Estate Migrations Batch]]

### 3.17 Сквозная инфраструктура real-estate
- [[Real Estate OpenAPI Contracts]] (13 версионированных OpenAPI-спек `-api` модулей)
- [[Real Estate Feature Commit History]] · [[Real Estate Domain Scope Docs]] · [[Real Estate Host Application README]]
- [[Real Estate Properties Livewire Tier]] · [[Real Estate Livewire List Adapters]] · [[Real Estate Marketing/Listings Adapters]]
- [[Listing and Sync Creation Actions]] · [[Letting and Management Record Actions]] · [[Instruction and Offer Actions]] · [[Valuation and Party Actions]] · [[Viewing and Valuation Lifecycle Actions]] · [[Lettings and Viewings Actions]]
- [[Money Value Object]] (общий value-object для цен/валют)

---

## 4. Не архитектурные, а «временные» кластеры (commit-history)

Часть сообществ graphify сгруппировал не по коду, а по **совместной истории коммитов**
(узлы — хэши коммитов, co-occurring в одном PR/фиче). Это не модули, а срез истории разработки:

- [[Real Estate Feature Commit History]] · [[Auth Views and Panel Commit History]]
- [[Admin Panel Localization Commits]] · [[Translation Publishing Commits]]
- [[Property Create/Update Commits]] · [[Property Deletion and Tax Commits]] · [[Property Favorites Commit History]]
- [[Media Document Module Commits]] · [[Public Property API Commits]] · [[Shield Policy Generation Commit]]
- [[Real Estate Migrations Batch]]

---

## 5. Артефакты кластеризации (не доменные категории)

Два сообщества — самые крупные в графе, но **не отражают архитектуру**, а являются
побочным эффектом того, что множество мелких файлов используют одинаковые общие имена методов:

- **[[Common CRUD Action Verbs]]** (1517 узлов, №1 по размеру) — общие Livewire/Blade
  вьюхи и глаголы действий (`create`/`edit`/`delete`/`attach`/`detach`...), повторяющиеся
  почти в каждом модуле. Не смотреть как на единый компонент.
- **[[Jetstream Blade Component Library]]** (178 узлов) — вендорные Blade-компоненты
  Jetstream (`action-message.blade.php`, `dialog-modal.blade.php` и т.д.), не собственный код проекта.

---

## 6. Неклассифицированные мелкие сообщества (~466 из 666)

Остальные сообщества (3–6 узлов каждое) — это, как правило, одна пара `Policy`+`Resource`
или узкий фрагмент кода, слишком мелкий, чтобы осмысленно выделять в эту иерархию по
отдельности. Полный список — в [[index]] (отсортирован по размеру, эти сообщества там
подписаны как `Community NNN`). Загляните в саму заметку, если нужен конкретный мелкий кусок —
её «Key Concepts» и «Source Files» точно укажут файл.

---

*Собрано вручную поверх графа graphify (обновлён на коммит `87e83fe0`, см. предыдущий разговор).
Обновляйте эту заметку при следующем `graphify --update`, если структура модулей изменится.*
