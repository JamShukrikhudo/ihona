<x-filament-panels::page>
    <x-filament-widgets::widgets :widgets="$this->getVisibleHeaderWidgets()" :columns="$this->getColumns()" />

    <div class="mt-6 grid gap-6 md:grid-cols-3">
        <x-filament::card>
            <h2 class="text-lg font-semibold">Current Property</h2>
            <p class="text-3xl font-bold">{{ $this->currentProperty->address ?? 'N/A' }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold">Next Rent Due</h2>
            <p class="text-3xl font-bold">{{ $this->rentDueDate ? $this->rentDueDate->format('M d, Y') : 'N/A' }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold">Open Work Orders</h2>
            <p class="text-3xl font-bold">{{ $this->openWorkOrders }}</p>
        </x-filament::card>
    </div>

    <x-filament-widgets::widgets class="mt-6" :widgets="$this->getVisibleWidgets()" :columns="1" />
</x-filament-panels::page>