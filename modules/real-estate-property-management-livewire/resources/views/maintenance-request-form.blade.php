<div>
    <h2 class="text-2xl font-semibold mb-4">Submit Maintenance Request</h2>
    <form wire:submit="submit" class="space-y-4">
        <input wire:model="title" placeholder="Title" required>
        <textarea wire:model="description" placeholder="Description" required></textarea>
        <input wire:model="property_id" type="number" placeholder="Property ID" required>
        <button type="submit">Submit request</button>
    </form>
    @if (session('message')) <div role="status">{{ session('message') }}</div> @endif
</div>
