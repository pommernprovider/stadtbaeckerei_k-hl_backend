<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductVisibility;
use App\Models\Category;
use App\Models\Tag;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;


class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Produkt')->tabs([
                    Tab::make('Stammdaten')->schema([
                        Select::make('category_id')
                            ->label('Kategorie')
                            ->options(Category::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('tags')
                            ->label('Tag')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->rule('alpha_dash')
                            ->unique(ignoreRecord: true),

                        Textarea::make('description')
                            ->label('Beschreibung')
                            ->rows(5),

                        Textarea::make('ingredients')
                            ->label('Inhaltsstoffe (Anzeige)')
                            ->rows(3),

                        Textarea::make('allergens')
                            ->label('Allergene (Anzeige)')
                            ->rows(3),

                        Toggle::make('notes_required')
                            ->label('Freitext/Notiz nötig (z. B. Widmung)')
                            ->default(false),

                        TextInput::make('position')
                            ->label('Position')
                            ->numeric()
                            ->default(0),
                    ]),

                    Tab::make('Preis & Steuer')->schema([
                        TextInput::make('base_price')
                            ->label('Basispreis (brutto)')
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01')
                            ->required(),

                        TextInput::make('tax_rate')
                            ->label('Steuersatz %')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step('0.01')
                            ->default(7.00),

                        TextInput::make('min_lead_days')
                            ->label('Min. Vorlauf (Tage)')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ]),

                    Tab::make('Verfügbarkeit')->schema([
                        Toggle::make('is_published')
                            ->label('Veröffentlicht')
                            ->default(false),

                        Select::make('visibility_status')
                            ->label('Status')
                            ->options(ProductVisibility::options())
                            ->default(ProductVisibility::Draft->value)
                            ->required()
                            ->native(false),

                        DateTimePicker::make('available_from')
                            ->label('Verfügbar ab')
                            ->seconds(false),

                        DateTimePicker::make('available_until')
                            ->label('Verfügbar bis')
                            ->seconds(false),
                    ]),

                    Tab::make('Media')->schema([
                        SpatieMediaLibraryFileUpload::make('product_main')
                            ->label('Hauptbild')
                            ->collection('product_main')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('product_gallery')
                            ->label('Galerie')
                            ->collection('product_gallery')
                            ->disk('public')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->imageEditor()
                            ->downloadable(),
                    ]),
                ])->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}
