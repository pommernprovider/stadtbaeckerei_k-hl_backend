<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Branch;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopBranchesChart extends ChartWidget
{
    protected ?string $heading = 'Top-Filialen (Bestellungen)';
    protected static ?int $sort = 3;
    protected function getData(): array
    {
        $rows = DB::table('orders')
            ->join('branches', 'branches.id', '=', 'orders.branch_id')
            ->select('branches.name as name', DB::raw('COUNT(*) as c'))
            ->groupBy('branches.name')
            ->orderByDesc('c')
            ->limit(10)
            ->get();

        return [
            'labels' => $rows->pluck('name')->toArray(),
            'datasets' => [[
                'label' => 'Bestellungen',
                'data'  => $rows->pluck('c')->map(fn($v) => (int)$v)->toArray(),
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
