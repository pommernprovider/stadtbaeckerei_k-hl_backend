<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductsChart extends ChartWidget
{
    protected ?string $heading = 'Top-Produkte (Menge)';

    protected static ?int $sort = 3;
    protected function getData(): array
    {
        $top = DB::table('order_items')
            ->select('product_name_snapshot as name', DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_name_snapshot')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        return [
            'labels' => $top->pluck('name')->toArray(),
            'datasets' => [[
                'label' => 'Stück',
                'data'  => $top->pluck('qty')->map(fn($v) => (int)$v)->toArray(),
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
