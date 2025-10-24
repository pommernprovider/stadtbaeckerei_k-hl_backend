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
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'Varianten';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')->label('SKU')->maxLength(100),
                TextInput::make('name')->label('Name')->maxLength(255),
                TextInput::make('price_override')->label('Preis Override')->numeric()->step('0.01')->nullable(),
                TextInput::make('extra_lead_minutes')->label('Extra Lead (Min.)')->numeric()->default(0),
                KeyValue::make('combination')
                    ->label('Kombination (option_id:value_id)')
                    ->keyLabel('Option-ID')
                    ->valueLabel('Value-ID')
                    ->addButtonLabel('Paar hinzufügen')
                    ->columnSpanFull(),
                Toggle::make('is_active')->label('Aktiv')->default(true),
                TextInput::make('position')->label('Pos.')->numeric()->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('sku')->label('SKU')->toggleable(),
                TextColumn::make('name')->label('Name')->limit(30),
                TextColumn::make('price_override')->label('Preis')->money('eur', true)->sortable(),
                TextColumn::make('extra_lead_minutes')->label('Lead+')->sortable(),
                IconColumn::make('is_active')->label('Aktiv')->boolean(),
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
