<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Dispatch;

class DispatchesChartDay extends ChartWidget
{
    

    protected static ?string $heading = 'Chart of Dispatches of Day';

    public static function canView(): bool
    {
        return auth()->user()->can('widget_DispatchesChartDay', new \stdClass);
    }
    protected function getData(): array
    {
        
        $data = Trend::model(Dispatch::class)
        ->between(
            start: now()->subMonth(),
            end: now(),
        )
        ->perWeek()
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
        return 'bar';
    }
}
