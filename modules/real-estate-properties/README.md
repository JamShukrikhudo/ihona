# Real Estate Properties

`liberusoftware/real-estate-properties` owns team-scoped property
records, structured property attributes, lifecycle status, and immutable
history entries. It is presentation-neutral; API, Filament, and Livewire
adapters depend on this package and do not duplicate its rules.

The module never assumes application `App\\` classes. Actor and team IDs are
explicit at mutation boundaries, and its migration owns only the two tables in
this package. Disabling the module does not delete data.
