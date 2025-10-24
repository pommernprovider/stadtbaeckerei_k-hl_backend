<?php

namespace App\Filament\Resources\Branches\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PickupWindowOverridesRelationManager extends RelationManager
{
    protected static string $relationship = 'pickupWindowOverrides';
    protected static ?string $title = 'Abholfenster (Datum-Overrides)';
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')->label('Datum')->required()->native(false),
                TimePicker::make('starts_at')->label('Start')->seconds(false)->required()->columnSpan(6),
                TimePicker::make('ends_at')->label('Ende')->seconds(false)->required()->columnSpan(6),
                TextInput::make('label')->label('Label')->maxLength(255),
                Toggle::make('is_active')->label('Aktiv')->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                TextColumn::make('date')->label('Datum')->date()->sortable(),
                TextColumn::make('starts_at')->label('Start'),
                TextColumn::make('ends_at')->label('Ende'),
                TextColumn::make('label')->label('Label')->limit(20),
                IconColumn::make('is_active')->label('Aktiv')->boolean(),
            ])
            ->defaultSort('date', 'desc')
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
