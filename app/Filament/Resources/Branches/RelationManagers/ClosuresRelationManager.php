<?php

namespace App\Filament\Resources\Branches\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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

class ClosuresRelationManager extends RelationManager
{
    protected static string $relationship = 'closures';
    protected static ?string $title = 'Schließtage / Ausnahmen';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')->label('Datum')->required()->native(false),
                Toggle::make('full_day')->label('Ganztägig geschlossen')->default(true)->live(),
                TimePicker::make('opens_at')
                    ->label('Öffnet (optional)')
                    ->seconds(false)
                    ->disabled(fn(callable $get) => (bool) $get('full_day'))
                    ->columnSpan(6),
                TimePicker::make('closes_at')
                    ->label('Schließt (optional)')
                    ->seconds(false)
                    ->disabled(fn(callable $get) => (bool) $get('full_day'))
                    ->columnSpan(6),
                TextInput::make('reason')->label('Grund')->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                TextColumn::make('date')->label('Datum')->date(),
                IconColumn::make('full_day')->label('Ganztägig')->boolean(),
                TextColumn::make('opens_at')->label('Öffnet'),
                TextColumn::make('closes_at')->label('Schließt'),
                TextColumn::make('reason')->label('Grund')->limit(30),
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
