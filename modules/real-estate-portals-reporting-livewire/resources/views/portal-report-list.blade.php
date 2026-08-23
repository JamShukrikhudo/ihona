<div>
    <input wire:model.live="search" type="search" placeholder="Search portal reports">
    <ul>
        @foreach ($reports as $report)
            <li>{{ $report->portal }} — {{ $report->report_type }} — {{ $report->status->value }}</li>
        @endforeach
    </ul>
    {{ $reports->links() }}
</div>
