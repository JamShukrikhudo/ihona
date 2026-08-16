<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-3">
        <x-filament::card>
            <h2 class="text-lg font-semibold">Open Jobs</h2>
            <p class="text-3xl font-bold">{{ $this->openJobs }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold">Completed Jobs</h2>
            <p class="text-3xl font-bold">{{ $this->completedJobs }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold">Pending Payments</h2>
            <p class="text-3xl font-bold">${{ number_format($this->pendingPayments, 2) }}</p>
        </x-filament::card>
    </div>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-semibold mb-4">Contractor Recent Activity</h2>
        <!-- Add contractor-specific recent activity content here -->
    </x-filament::card>
</x-filament-panels::page>