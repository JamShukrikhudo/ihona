<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-3">
        <x-filament::card>
            <h2 class="text-lg font-semibold">My Properties</h2>
            <p class="text-3xl font-bold">{{ $this->myProperties }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold">Active Listings</h2>
            <p class="text-3xl font-bold">{{ $this->activeListings }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold">Total Bookings</h2>
            <p class="text-3xl font-bold">{{ $this->totalBookings }}</p>
        </x-filament::card>
    </div>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-semibold mb-4">Seller Recent Activity</h2>
        <!-- Add seller-specific recent activity content here -->
    </x-filament::card>
</x-filament-panels::page>