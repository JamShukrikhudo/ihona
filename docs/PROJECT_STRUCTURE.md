# Иерархическая структура проекта ihona

> Составлено по факту кода (`modules/*/module.json`, реальные namespace'ы) и графу
> зависимостей graphify, не по документации-заготовке из исходного boilerplate —
> см. раздел «Документация, не отражающая реальный код» ниже.

## 1. Composition Host

`app/` содержит ровно 5 файлов — модель `User`, `ModulePlugins`, `ThemeColors` и два
Filament-провайдера. Всё остальное — 40 `liberu-module` пакетов в `modules/` и 4
`liberu-theme` пакета в `themes/`.

## 2. Foundation-модули (инфраструктурные, не доменные)

Каждый указан без суффиксов `-api`/`-filament`/`-livewire` — большинство имеет все три
презентационных адаптера отдельными пакетами по тому же паттерну.

| Модуль | Назначение |
|---|---|
| `identity-core` | Пользователь, роли, аутентификация (Fortify), CRM-синхронизация новых регистраций |
| `identity-socialstream` | OAuth (Google) + Telegram Login Widget поверх Socialstream |
| `organizations-teams` | Команды (Jetstream Teams), мультитенантность |
| `roles-permissions` | Spatie Permission, team-scoped роли, Filament Shield |
| `localization-core` | `SetLocale`, переключатель языка, поддержка en/ru/tg/uz |
| `localization-mymemory` | Машинный перевод на лету (выключен по умолчанию) |
| `theme-support` | `ThemeManager`, наследование тем, Vite-интеграция |
| `search` | Полнотекстовый поиск по пользователям/постам/группам |
| `settings` | Настройки сайта (`SiteSettings`, активная тема и т.д.) |
| `notifications` | Уведомления (broadcast/queued) |
| `activity-comments` | Комментарии к записям |
| `analytics-core`, `analytics-google`, `analytics-meta` | Аналитика; google/meta выключены по умолчанию (нужны креды) |
| `api-access` | Управление API-токенами/доступом |
| `application` | Общая прикладная инфраструктура хоста |
| `audit` | Неизменяемый аудит-трейл (`ActivityLogResource`) |
| `currency-context` | `Money`-value-object, работа с валютами |
| `developer-experience` | `FoundationDoctorCommand`, инструменты для разработчиков |
| `feature-flags` | Флаги функциональности |
| `files-media` | Общее файловое/медиа-хранилище (не путать с `real-estate-media-and-documents`) |
| `import-export` | Импорт/экспорт данных |
| `integrations` | Внешние интеграции |
| `jetstream-bridge` | Мост между Jetstream и остальной платформой |
| `module-manager` | `ModuleRegistry`, `ModuleManagerServiceProvider`, разбор `module.json` |
| `observability` | Гейты для Telescope/Pulse (`ObservabilityActor::isAdmin()`) |
| `profiles` | Расширение `users` — `locale`, `theme_preference`, `timezone` |
| `scheduler-queues` | Horizon, очереди, `schedule` |
| `sessions-devices` | Управление сессиями/устройствами, 2FA-таблицы |
| `two-factor-authentication` | TOTP/recovery codes поверх Fortify |
| `webhooks` | Исходящие вебхуки |

## 3. Real Estate — доменные модули (`real-estate-*`)

Общий паттерн на каждый домен: **Core** (доменная логика, provider-neutral) → **API**
(`-api`, Sanctum, версionированный OpenAPI-контракт в `openapi/v1/`) → **Filament**
(`-filament`, admin UI) → **Livewire** (`-livewire`, публичный UI). Не каждый домен имеет
все 4 слоя (например, `real-estate-core` не имеет собственного публичного Livewire).

| Домен | Что покрывает |
|---|---|
| `real-estate-core` | Агентства, филиалы, территории, определения статусов — общая инфраструктура |
| `real-estate-properties` | Объекты недвижимости — самый крупный домен: CRUD, поиск, избранное, ценовые алерты, калькуляторы (ипотека, налог, стоимость переезда), публичная витрина |
| `real-estate-listings` | Объявления — публикация, статус-переходы |
| `real-estate-offers` | Предложения по цене (вторичный рынок) |
| `real-estate-viewings` | Показы объектов, доступность слотов |
| `real-estate-valuations` | Оценки объектов, explainable-калькуляторы |
| `real-estate-matching` | Профили подбора, скоринг соответствия |
| `real-estate-marketing` | Маркетинговые кампании, новости (`NewsArticleResource`) |
| `real-estate-portals-reporting` | Отчёты по порталам-агрегаторам, метрики |
| `real-estate-sales-progression` | Сопровождение сделки (цепочка, вехи) |
| `real-estate-parties` | Контрагенты (стороны сделки), согласия, связи между сторонами |
| `real-estate-media-and-documents` | Медиа и документы объекта, брошюры, права доступа |
| `real-estate-instructions` | Инструкции/поручения на продажу, согласование |
| `real-estate-lettings` | Аренда — заявки, договоры, арендные платежи |
| `real-estate-property-management` | Управление объектом после сделки — заявки на обслуживание, наряды, подрядчики, инспекции |
| `real-estate-rightmove`, `real-estate-zoopla`, `real-estate-onthemarket` | Синхронизация с внешними порталами-агрегаторами (Великобритания) |

## 4. Themes (`themes/`)

`base` (shared-корень) ← `default`, `dark`, `clear-signal` (public-темы). Отдельная
`real-estate-default` — активная публичная тема сайта (`THEME_PUBLIC`).

## 5. Внешние репозитории (не входят в Composer-дерево хоста)

- **`ihona-frontend`** (Nuxt 3) — отдельный репозиторий, потребляет REST API хоста через
  Sanctum personal access tokens. Никогда не появляется в `composer.lock`.
- **`crm-laravel`** (Liberu CRM) — отдельный проект, интегрирован только через одностороннюю
  синхронизацию новых регистраций (`SyncNewUserToCrm` job в `identity-core`).

## 6. Находки при составлении этой структуры

### 6.1 Мусорные директории в `modules/`
Пять директорий не являются реальными пакетами — только `README.md`, без `module.json`,
без `src/`, не участвуют ни в `composer.json`, ни в `ModuleRegistry`:

```
modules/core/
modules/features/
modules/filament/
modules/livewire/
modules/api/
```

Похоже на остаток незавершённого переименования/разбиения модулей (возможно, черновик
паттерна `<domain>-core`/`<domain>-filament` до того, как имена получили доменный
префикс). Кандидат на удаление — стоит подтвердить с автором истории репозитория перед
`git rm`, так как это не моё решение в рамках данной задачи.

### 6.2 Документация, не отражающая реальный код
Несколько файлов в `docs/` и корне — унаследованы из исходного `boilerplate-laravel`
одним бутстрап-коммитом (`75f78186`, 23 августа) и описывают архитектуру, которой в
`ihona` нет:

- **`docs/ADMIN_PANEL_ENHANCEMENTS.md`**, **`docs/ADMIN_PANEL_VISUAL_COMPARISON.md`** —
  описывают вкладки, `ViewUser`-страницу и виджеты дашборда для `App\Filament\Admin\Resources\Users\...`,
  которых в модульной архитектуре `identity-core-filament` не существует (там инлайновые
  `form()`/`table()` в самом `UserResource`, без вкладок и без `ViewUser`).
- **`docs/MESSAGING.md`**, **`docs/MESSAGING_ARCHITECTURE.md`**, **`docs/MESSAGING_DEVELOPER_NOTES.md`**,
  **`docs/SETUP_MESSAGING.md`** — модуля `messaging` **не существует** в `modules/` вообще
  (проверено по полному листингу директорий). Документация полностью аспирационная.

Прежде чем на них ориентироваться в дальнейшей работе — сверяйтесь с фактическим кодом,
а не с этими файлами. Единственный по-настоящему актуальный источник архитектурных правил —
корневой `CLAUDE.md`.

---

*Составлено вручную на основе фактического листинга `modules/`, `module.json`-манифестов и
графа зависимостей graphify (`.graphify/graph.json`, коммит `87e83fe0`). Обновляйте при
структурных изменениях модулей — этот файл не генерируется автоматически.*
