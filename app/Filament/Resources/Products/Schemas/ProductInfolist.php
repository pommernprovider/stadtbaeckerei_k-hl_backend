<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductVisibility;
use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Group::make()
                    ->schema([
                        //Linke Spalte
                        Section::make('Stammdaten')->schema([
                            TextEntry::make('name')
                                ->label('Name'),
                            TextEntry::make('category.name')
                                ->label('Kategorie')
                                ->placeholder('-'),
                            SpatieMediaLibraryImageEntry::make('product_main')
                                ->label('Bild')
                                ->conversion('thumb')
                                ->collection('product_main'),

                            TextEntry::make('tags.name')
                                ->label('Tags')
                                ->badge()
                                ->placeholder('-'),

                            TextEntry::make('slug')
                                ->label('Slug'),

                            TextEntry::make('description')
                                ->label('Beschreibung')
                                ->placeholder('-')
                                ->columnSpanFull(),

                            TextEntry::make('ingredients')
                                ->label('Inhaltsstoffe')
                                ->placeholder('-')
                                ->columnSpanFull(),

                            TextEntry::make('allergens')
                                ->label('Allergene')
                                ->placeholder('-')
                                ->columnSpanFull(),
                        ])->columns(2),

                        Section::make('Preis & Regeln')->schema([
                            TextEntry::make('base_price')
                                ->label('Preis (brutto)')
                                ->formatStateUsing(fn($state) => $state === null ? '-' : number_format((float)$state, 2, ',', '.') . ' €'),

                            TextEntry::make('tax_rate')
                                ->label('Steuersatz')
                                ->formatStateUsing(fn($state) => $state === null ? '-' : rtrim(rtrim(number_format((float)$state, 2, ',', '.'), '0'), ',') . ' %'),

                            TextEntry::make('min_lead_days')
                                ->label('Min. Vorlauf (Tage)')
                                ->numeric()
                                ->placeholder('0'),

                            IconEntry::make('notes_required')
                                ->label('Notiz nötig')
                                ->boolean(),
                        ])->columns(4),


                    ])
                    ->columnSpan(['lg' => fn(?Product $record) => $record === null ? 3 : 2]),

                //Rechte Spalte
                Group::make()
                    ->schema([
                        Section::make('Verfügbarkeit')->schema([
                            IconEntry::make('is_published')
                                ->label('Veröffentlicht')
                                ->boolean(),

                            TextEntry::make('visibility_status')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(function ($state) {
                                    $enum = $state instanceof ProductVisibility  ? $state : ProductVisibility::from($state);
                                    return $enum->label();
                                })
                                ->color(function ($state) {
                                    $enum = $state instanceof ProductVisibility  ? $state : ProductVisibility::from($state);
                                    return $enum->color();
                                }),

                            TextEntry::make('available_from')
                                ->label('Verfügbar ab')
                                ->dateTime('d.m.Y H:i')
                                ->placeholder('-'),

                            TextEntry::make('available_until')
                                ->label('Verfügbar bis')
                                ->dateTime('d.m.Y H:i')
                                ->placeholder('-'),
                        ])->columns(4),

                        Section::make('Metadaten')->schema([
                            TextEntry::make('position')
                                ->label('Position')
                                ->numeric()
                                ->placeholder('0'),

                            TextEntry::make('created_at')
                                ->label('Erstellt')
                                ->dateTime('d.m.Y H:i')
                                ->placeholder('-'),

                            TextEntry::make('updated_at')
                                ->label('Aktualisiert')
                                ->dateTime('d.m.Y H:i')
                                ->placeholder('-'),
                        ])->columns(3),
                    ])->columnSpan(['lg' => 1])
                    ->hidden(fn(?Product $record) => $record === null),





            ])->columns(3);
    }
}
