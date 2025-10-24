<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';
    protected static ?string $title = 'Optionen';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Name')->required()->maxLength(255),
                Select::make('type')->label('Typ')->options([
                    'select' => 'Select',
                    'radio'  => 'Radio',
                    'multi'  => 'Mehrfachauswahl',
                    'text'   => 'Freitext',
                ])->default('select')->required(),
                Toggle::make('is_required')->label('Pflichtfeld')->default(false),
                TextInput::make('position')->label('Position')->numeric()->default(0),
                Repeater::make('values')
                    ->relationship('values')
                    ->label('Option-Werte')
                    ->defaultItems(0)
                    ->reorderable()
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('value')->label('Wert')->required()->maxLength(255),
                        TextInput::make('price_delta')->label('Preis Δ')->numeric()->step('0.01')->default(0),
                        TextInput::make('extra_lead_days')->label('Extra Vorlaufzeit (Tage)')->numeric()->default(0),
                        Toggle::make('is_active')->label('Aktiv')->default(true),
                        TextInput::make('position')->label('Pos.')->numeric()->default(0),
                    ])
                    ->addActionLabel('Wert hinzufügen'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Option')->searchable()->sortable(),
                TextColumn::make('type')->label('Typ')->badge(),
                IconColumn::make('is_required')->label('Pflicht')->boolean(),
                TextColumn::make('values_count')->counts('values')->label('Werte'),
                TextColumn::make('position')->label('Pos.')->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),

            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
