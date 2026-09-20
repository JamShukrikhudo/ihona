<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;

final class PropertiesByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Объекты по статусам';

    private const LABELS = [
        'draft' => 'Черновик',
        'moderation' => 'На модерации',
        'published' => 'Опубликован',
        'archive' => 'В архиве',
    ];

    private const COLORS = [
        'draft' => '#94a3b8',
        'moderation' => '#c2622d',
        'published' => '#2f7fd6',
        'archive' => '#64748b',
    ];

    protected function getData(): array
    {
        $team = Filament::getTenant();

        if (! $team) {
            return ['datasets' => [], 'labels' => []];
        }

        $counts = Property::query()
            ->forTeam($team->id)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $order = array_column(PropertyStatus::cases(), 'value');
        $counts = $counts->sortBy(fn ($value, $key) => array_search($key, $order, true));

        return [
            'datasets' => [[
                'data' => $counts->values()->all(),
                'backgroundColor' => $counts->keys()->map(fn ($status) => self::COLORS[$status] ?? '#94a3b8')->all(),
            ]],
            'labels' => $counts->keys()->map(fn ($status) => self::LABELS[$status] ?? $status)->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
