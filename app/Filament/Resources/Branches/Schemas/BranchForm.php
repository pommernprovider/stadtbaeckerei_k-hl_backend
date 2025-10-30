<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        // Stammdaten + Bild
                        Section::make('Filiale')
                            ->description('Stammdaten & Standort')
                            ->icon('heroicon-o-building-storefront')
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                TextInput::make('number')
                                    ->label('Filialnummer')
                                    ->required(),
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                SpatieMediaLibraryFileUpload::make('branch_images')
                                    ->label('Filialbild')
                                    ->collection('branch_images')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->downloadable()
                                    ->helperText('Empfohlenes Format: quer, mind. 1200px Breite')
                                    ->columnSpan(2),
                            ]),

                        // Adresse (volle Breite in der linken Gruppe)
                        Section::make('Adresse')
                            ->columns(12)
                            ->schema([
                                TextInput::make('street')
                                    ->label('Straße')
                                    ->columnSpan(7),

                                TextInput::make('zip')
                                    ->label('PLZ')
                                    ->maxLength(15)
                                    ->columnSpan(2),

                                TextInput::make('city')
                                    ->label('Ort')
                                    ->columnSpan(3),
                            ]),

                        // Geokoordinaten (volle Breite in der linken Gruppe)
                        Section::make('Geokoordinaten')
                            ->columns(12)
                            ->schema([
                                TextInput::make('lat')
                                    ->label('Breitengrad (lat)')
                                    ->numeric()
                                    ->columnSpan(['lg' => 6, 'md' => 12]),

                                TextInput::make('lng')
                                    ->label('Längengrad (lng)')
                                    ->numeric()
                                    ->columnSpan(['lg' => 6, 'md' => 12]),

                            ]),
                    ])

                    ->columnSpan(['lg' => 2]),

                // Seitenleiste (rechts)
                Section::make('Status')
                    ->columns(1) // einspaltig
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktiv')
                            ->inline(false)
                            ->default(true),

                        TimePicker::make('order_cutoff_time')
                            ->label('Bestellschluss (optional)')
                            ->seconds(false)
                            ->nullable()
                            ->helperText('Nach dieser Uhrzeit sind Bestellungen für den heutigen Tag nicht mehr möglich.'),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
