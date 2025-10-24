<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Bestellung')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                TextInput::make('order_number')
                                    ->label('Bestellnummer')
                                    ->disabled()
                                    ->dehydrated()
                                    ->suffixAction(fn() => null)
                                    ->helperText('Wird automatisch vergeben.'),

                                // Status (Enum-Labels)
                                ToggleButtons::make('status')
                                    ->label('Status')
                                    ->inline()
                                    ->options(OrderStatus::options())
                                    ->required(),

                                // Filiale (wenn Relation vorhanden)
                                Select::make('branch_id')
                                    ->label('Filiale')
                                    ->relationship('branch', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->hidden(fn(?Order $record) => ! method_exists($record ?? new Order, 'branch')),

                                // Abholfenster (read-only aus Logik)
                                DateTimePicker::make('pickup_at')
                                    ->label('Abholzeit (Fensterstart)')
                                    ->seconds(false)
                                    ->disabled()
                                    ->dehydrated(false),

                                Placeholder::make('pickup_window_text')
                                    ->label('Fenster')
                                    ->content(fn(?Order $record) => $record?->pickup_window_text ?? '—'),
                            ])
                            ->columns(2),

                        Section::make('Summen')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                TextInput::make('subtotal')
                                    ->label('Netto')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('€'),

                                TextInput::make('tax_total')
                                    ->label('MwSt.')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('€'),

                                TextInput::make('grand_total')
                                    ->label('Brutto')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('€'),
                            ])
                            ->columns(3),

                        Section::make('Kundeninformationen')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('customer_name')
                                    ->label('Name')
                                    ->required(),

                                TextInput::make('customer_email')
                                    ->label('E-Mail')
                                    ->email()
                                    ->required(),

                                TextInput::make('customer_phone')
                                    ->label('Telefon')
                                    ->tel()
                                    ->placeholder('—'),

                                // Hinweis: falls dein Feld wirklich 'customer_adress' heißt, so lassen.
                                TextInput::make('customer_adress')
                                    ->label('Adresse'),

                                TextInput::make('customer_tax')
                                    ->label('Postleitzahl'),

                                TextInput::make('customer_city')
                                    ->label('Stadt'),

                                Textarea::make('customer_note')
                                    ->label('Notiz')
                                    ->placeholder('—')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpan(['lg' => fn(?Order $record) => $record === null ? 3 : 2]),

                // Rechte Meta-Spalte (schmal)
                Section::make()
                    ->schema([
                        // Read-only Meta; in Forms per disabled + format
                        TextInput::make('created_at')
                            ->label('Bestellt am')
                            ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->diffForHumans() : null)
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('updated_at')
                            ->label('Zuletzt aktualisiert')
                            ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->diffForHumans() : null)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?Order $record) => $record === null),
            ])
            ->columns(3);
    }
}
