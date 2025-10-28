<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersStatsOverview extends BaseWidget
{
    protected ?string $heading = 'Bestell-Überblick';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Offen = alles außer PickedUp & Cancelled
        $open = Order::whereNotIn('status', [
            OrderStatus::PickedUp->value,
            OrderStatus::Cancelled->value,
        ])->count();

        $total = Order::count();

        $startOfMonth = now()->startOfMonth();

        // A) Umsatz nach ABHOL-Datum (empfohlen)
        $revenueThisMonth = Order::where('status', OrderStatus::PickedUp->value)
            ->where('pickup_at', '>=', $startOfMonth)
            ->sum('grand_total');

        return [
            Stat::make('Bestellungen gesamt', number_format($total, 0, ',', '.'))
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('Offene Bestellungen', number_format($open, 0, ',', '.'))
                ->icon('heroicon-o-clock')
                ->color($open > 0 ? 'warning' : 'success'),

            Stat::make('Umsatz (dieser Monat)', number_format($revenueThisMonth, 2, ',', '.') . ' €')
                ->icon('heroicon-o-banknotes')
                ->description(now()->isoFormat('MMMM YYYY')),
        ];
    }
}
