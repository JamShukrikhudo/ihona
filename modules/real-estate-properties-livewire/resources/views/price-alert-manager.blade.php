<div class="space-y-4">
    <h2 class="text-lg font-semibold">Price alerts for {{ $property->title ?: $property->address }}</h2>

    @if (session('message'))
        <p role="status">{{ session('message') }}</p>
    @endif

    <form wire:submit="createAlert" class="space-y-4">
        <div>
            <label for="price-alert-percentage">Change threshold (%)</label>
            <input id="price-alert-percentage" type="number" step="0.1" min="0.1" max="100" wire:model="alertPercentage" required>
            @error('alertPercentage') <p role="alert">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="price-alert-frequency">Frequency</label>
            <select id="price-alert-frequency" wire:model="alertFrequency" required>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
            @error('alertFrequency') <p role="alert">{{ $message }}</p> @enderror
        </div>
        <button type="submit">Create price alert</button>
    </form>

    <ul aria-label="Price alerts">
        @forelse ($priceAlerts as $alert)
            <li wire:key="price-alert-{{ $alert['id'] }}">
                {{ $alert['alert_percentage'] }}% · {{ ucfirst($alert['alert_frequency']) }}
                <button type="button" wire:click="toggleAlert({{ $alert['id'] }})">{{ $alert['is_active'] ? 'Pause' : 'Resume' }}</button>
                <button type="button" wire:click="deleteAlert({{ $alert['id'] }})">Delete</button>
            </li>
        @empty
            <li>No price alerts yet.</li>
        @endforelse
    </ul>
</div>
