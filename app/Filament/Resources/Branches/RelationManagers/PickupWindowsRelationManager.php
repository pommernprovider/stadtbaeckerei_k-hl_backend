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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PickupWindowsRelationManager extends RelationManager
{
    protected static string $relationship = 'pickupWindows';
    protected static ?string $title = 'Abholfenster (Wochentage)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('weekday')
                    ->label('Wochentag')
                    ->options(self::weekdayOptions())
                    ->required(),
                TimePicker::make('starts_at')->label('Start')->seconds(false)->required()->columnSpan(6),
                TimePicker::make('ends_at')->label('Ende')->seconds(false)->required()->columnSpan(6),
                TextInput::make('label')->label('Label')->maxLength(255)->placeholder('z. B. Früh 08–10'),
                Toggle::make('is_active')->label('Aktiv')->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('weekday')->label('Tag')
                    ->formatStateUsing(fn($state) => self::weekdayOptions()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('starts_at')->label('Start'),
                TextColumn::make('ends_at')->label('Ende'),
                TextColumn::make('label')->label('Label')->limit(20),
                IconColumn::make('is_active')->label('Aktiv')->boolean(),
            ])
            ->defaultSort('weekday')
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

    private static function weekdayOptions(): array
    {
        return [
            0 => 'Sonntag',
            1 => 'Montag',
            2 => 'Dienstag',
            3 => 'Mittwoch',
            4 => 'Donnerstag',
            5 => 'Freitag',
            6 => 'Samstag',
        ];
    }
}
