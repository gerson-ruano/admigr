<?php

namespace App\Filament\Widgets;

use App\Models\Dispatch;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DispatchesChart extends ChartWidget
{
    protected static ?string $heading = 'Chart of Dispatches';

    public static function canView(): bool
    {
        return auth()->user()->can('widget_DispatchesChart', new \stdClass);
    }
    protected function getData(): array
    {
        
        $data = Trend::model(Dispatch::class)
        ->between(
            start: now()->subYear(),
            end: now(),
        )
        ->perMonth()
        ->count();
        return [
            'datasets' => [
            [
                'label' => 'Dispatches',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
