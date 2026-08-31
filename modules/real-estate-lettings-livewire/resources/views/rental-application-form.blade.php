<form wire:submit="submit" class="space-y-4">
    @if (session('status')) <div role="status">{{ session('status') }}</div> @endif
    <input wire:model="employment_status" placeholder="Employment status" required>
    <input wire:model="annual_income" type="number" min="0" step="0.01" placeholder="Annual income">
    <input wire:model="desired_move_in_date" type="date" required>
    <input wire:model="lease_end_date" type="date">
    <input wire:model="ethereum_address" placeholder="Ethereum address">
    <button type="submit">Submit application</button>
</form>
