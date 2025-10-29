<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentOrdersTable extends BaseWidget
{
    protected static ?string $heading = 'Letzte Bestellungen';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;

    protected function getTableQuery(): Builder
    {
        return Order::query()->latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [

            Tables\Columns\TextColumn::make('order_number')
                ->label('Bestellnr.')
                ->searchable()
                ->weight('bold'),

            Tables\Columns\TextColumn::make('customer_name')
                ->label('Kunde')
                ->toggleable(),

            Tables\Columns\TextColumn::make('branch.name')
                ->label('Filiale')
                ->toggleable(),

            Tables\Columns\TextColumn::make('pickup_at')
                ->label('Abholung')
                ->dateTime('d.m.Y'),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->formatStateUsing(function ($state) {
                    $enum = $state instanceof OrderStatus ? $state : OrderStatus::from($state);
                    return $enum->label();
                })
                ->color(function ($state) {
                    $enum = $state instanceof OrderStatus ? $state : OrderStatus::from($state);
                    return $enum->getColor();
                }),

            Tables\Columns\TextColumn::make('grand_total')
                ->label('Summe')
                ->money('eur', true),

            Tables\Columns\TextColumn::make('created_at')->since()->label('Erstellt'),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('anzeigen')
                ->label('anzeigen')
                ->icon('heroicon-o-eye')
                ->url(fn(Order $record) => OrderResource::getUrl('view', ['record' => $record]))
                ->openUrlInNewTab(true),
        ];
    }

    /** Macht die gesamte Zeile klickbar (führt Action 'anzeigen' aus) */
    protected function getTableRecordAction(): ?string
    {
        return 'anzeigen';
    }
}
