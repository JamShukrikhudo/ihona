<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading portal reports…</div>
    <input wire:model.live="search" type="search" placeholder="Search portal reports">
    <ul>
        @forelse ($reports as $report)
            <li>{{ $report->portal }} — {{ $report->report_type }} — {{ $report->status->value }}</li>
        @empty
            <li>No portal reports found.</li>
        @endforelse
    </ul>
    {{ $reports->links() }}
</div>
