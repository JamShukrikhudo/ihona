# Real Estate Portal Integrations

These provider integrations are independently releasable from the core
portal/reporting module. Each provider has one core package and matching API,
Filament, and Livewire adapters.

| Provider | Core | API | Filament | Livewire |
| --- | --- | --- | --- | --- |
| Rightmove | `liberusoftware/real-estate-rightmove` | `liberusoftware/real-estate-rightmove-api` | `liberusoftware/real-estate-rightmove-filament` | `liberusoftware/real-estate-rightmove-livewire` |
| Zoopla | `liberusoftware/real-estate-zoopla` | `liberusoftware/real-estate-zoopla-api` | `liberusoftware/real-estate-zoopla-filament` | `liberusoftware/real-estate-zoopla-livewire` |
| OnTheMarket | `liberusoftware/real-estate-onthemarket` | `liberusoftware/real-estate-onthemarket-api` | `liberusoftware/real-estate-onthemarket-filament` | `liberusoftware/real-estate-onthemarket-livewire` |

Provider credentials and transport implementations remain replaceable
boundaries. These packages own team-scoped synchronization state and payload
contracts; they do not depend on `real-estate-portals-reporting`.
