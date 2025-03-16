<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use App\Models\Categories;

class DataOverview extends BaseWidget
{
    protected function getStats(): array
    {
        if (! Gate::allows('widget_DataOverview')) {
            return [];
        }
        return [
            Stat::make('Products', Product::query()->count()),
            Stat::make('Categories', Category::query()->count()),
            Stat::make('Users', User::query()->count()),
            //Stat::make('Dogs', Patient::query()->where('type', 'dog')->count()),
            //Stat::make('Rabbits', Patient::query()->where('type', 'rabbit')->count()),
        ];
    }
}
