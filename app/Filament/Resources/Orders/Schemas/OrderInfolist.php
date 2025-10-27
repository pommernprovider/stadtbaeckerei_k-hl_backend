<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Linke Seite (breit): Bestellung + Kunde
                Group::make()
                    ->schema([
                        Section::make('Bestellung')
                            ->icon('heroicon-o-clipboard-document-check')

                            ->schema([
                                TextEntry::make('order_number')
                                    ->label('Bestellnummer')
                                    ->copyable(),

                                TextEntry::make('status')
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

                                TextEntry::make('branch.name')->label('Filiale'),
                                TextEntry::make('pickup_at')->label('Abholdatum')->date('d.m.Y'),
                                TextEntry::make('pickup_window_text')->label('Fenster'),
                            ])
                            ->columns(5),
                        Section::make('Summen')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                TextEntry::make('subtotal')->label('Netto')->money('eur', true),
                                TextEntry::make('tax_total')->label('MwSt.')->money('eur', true),
                                TextEntry::make('grand_total')->label('Brutto')->money('eur', true),
                            ])->columns(3),
                        Section::make('Kundeninformationen')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextEntry::make('customer_name')->label('Name'),
                                TextEntry::make('customer_email')
                                    ->url(fn($state) => $state ? "mailto:{$state}" : null)
                                    ->label('E-Mail'),
                                TextEntry::make('customer_phone')->label('Telefon')
                                    ->url(fn($state) => $state ? "tel:" . preg_replace('/\s+/', '', $state) : null)
                                    ->placeholder('-'),
                                TextEntry::make('customer_adress')->label('Adresse'),
                                TextEntry::make('customer_tax')->label('Postleitzahl'),
                                TextEntry::make('customer_city')->label('Stadt'),
                                TextEntry::make('customer_note')->label('Notiz')->placeholder('-')->columnSpanFull(),
                            ])
                            ->columns(3),

                    ])
                    ->columnSpan(['lg' => fn(?Order $record) => $record === null ? 4 : 3]),
                Section::make()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Bestellt am')
                            ->since(),

                        TextEntry::make('updated_at')
                            ->label('Zuletzt aktualisiert')
                            ->since(),
                    ])
                    // Rechte Meta-/Summen-Spalte (schmal)

                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?Order $record) => $record === null),
            ])
            ->columns(4);
    }
}
