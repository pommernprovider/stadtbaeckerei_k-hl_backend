<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyOrdersChart extends ChartWidget
{
    protected ?string $heading = 'Bestellungen – Zeitraum';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 4;

    /** Dropdown oben rechts im Widget */
    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'Dieser Monat',
            '3m'         => 'Letzte 3 Monate',
            '6m'         => 'Letzte 6 Monate',
            '12m'        => 'Letzte 12 Monate',
            'ytd'        => 'YTD (aktuelles Jahr)',
        ];
    }

    /** Standard-Auswahl */
    protected function getDefaultFilter(): ?string
    {
        return 'this_month';
    }

    protected function getData(): array
    {
        [$start, $end, $bucket] = $this->resolveRangeAndBucket();
        // $bucket: 'day' | 'month'

        if ($bucket === 'day') {
            // TÄGLICH gruppieren
            $rows = Order::query()
                ->selectRaw("DATE(created_at) as d, COUNT(*) as c")
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('d')
                ->orderBy('d')
                ->pluck('c', 'd')
                ->all();

            // Lücken füllen
            $labels = [];
            $data   = [];
            $cursor = (clone $start)->startOfDay();
            while ($cursor->lte($end)) {
                $key      = $cursor->toDateString();
                $labels[] = $cursor->isoFormat('DD.MM.');
                $data[]   = (int)($rows[$key] ?? 0);
                $cursor->addDay();
            }

            return [
                'labels'   => $labels,
                'datasets' => [[
                    'label' => 'Bestellungen pro Tag',
                    'data'  => $data,
                ]],
            ];
        }

        // MONATLICH gruppieren
        $rows = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('c', 'ym')
            ->all();

        $labels = [];
        $data   = [];

        $cursor = (clone $start)->startOfMonth();
        $endM   = (clone $end)->startOfMonth();

        while ($cursor->lte($endM)) {
            $key      = $cursor->format('Y-m');
            $labels[] = $cursor->isoFormat('MMM YY');
            $data[]   = (int)($rows[$key] ?? 0);
            $cursor->addMonth();
        }

        return [
            'labels'   => $labels,
            'datasets' => [[
                'label' => 'Bestellungen pro Monat',
                'data'  => $data,
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Ermittelt Zeitraum & Bucket (Tag/Monat) basierend auf dem aktiven Filter.
     *
     * @return array{0: \Carbon\Carbon, 1: \Carbon\Carbon, 2: 'day'|'month'}
     */
    private function resolveRangeAndBucket(): array
    {
        $filter = $this->filter ?? $this->getDefaultFilter(); // Filament stellt $this->filter bereit

        $now = now();
        $end = (clone $now)->endOfDay();

        return match ($filter) {
            'this_month' => [
                $now->copy()->startOfMonth(),
                $end,
                'day',
            ],
            '3m' => [
                $now->copy()->startOfMonth()->subMonths(2), // inkl. aktueller Monat = 3
                $end,
                'month',
            ],
            '6m' => [
                $now->copy()->startOfMonth()->subMonths(5),
                $end,
                'month',
            ],
            '12m' => [
                $now->copy()->startOfMonth()->subMonths(11),
                $end,
                'month',
            ],
            'ytd' => [
                $now->copy()->startOfYear(),
                $end,
                // YTD sinnvoll monatlich
                'month',
            ],
            default => [
                $now->copy()->startOfMonth(),
                $end,
                'day',
            ],
        };
    }
}
