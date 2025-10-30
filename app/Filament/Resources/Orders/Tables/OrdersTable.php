<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')->label('Nr.')->searchable()->copyable(),
                TextColumn::make('customer_name')->label('Kunde')->searchable(),
                TextColumn::make('customer_email')->label('E-Mail')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('branch.number')->label('Filiale')->sortable()->searchable(),
                TextColumn::make('pickup_at')->label('Abholdatum')->date('d.m.Y')->sortable(),
                TextColumn::make('pickup_window_text')->label('Fenster'),

                TextColumn::make('grand_total')->label('Summe')->money('eur', true)->sortable(),
                TextColumn::make('status')
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

                TextColumn::make('created_at')->label('Erstellt')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Offen',
                        'confirmed' => 'Bestätigt',
                        'ready'     => 'Bereit',
                        'picked_up' => 'Abgeholt',
                        'cancelled' => 'Storniert',
                    ]),
                SelectFilter::make('branch')->relationship('branch', 'name')->label('Filiale'),
                Filter::make('pickup_date')
                    ->form([DatePicker::make('date')->label('Abholdatum')])
                    ->query(function ($query, array $data) {
                        if (!empty($data['date'])) {
                            $query->whereDate('pickup_at', $data['date']);
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('setConfirmed')->label('→ Bestätigt')
                        ->action(fn($records) => $records->each->update(['status' => 'confirmed'])),
                    BulkAction::make('setReady')->label('→ Bereit')
                        ->action(fn($records) => $records->each->update(['status' => 'ready'])),
                    BulkAction::make('setPickedUp')->label('→ Abgeholt')
                        ->action(fn($records) => $records->each->update(['status' => 'picked_up'])),
                    DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }
}
