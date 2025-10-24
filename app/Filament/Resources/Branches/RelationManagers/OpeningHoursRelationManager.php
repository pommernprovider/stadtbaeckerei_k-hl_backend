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
use Illuminate\Validation\Rules\Unique;
use Livewire\Component as Livewire;

class OpeningHoursRelationManager extends RelationManager
{
    protected static string $relationship = 'openingHours';
    protected static ?string $title = 'Öffnungszeiten (Woche)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('weekday')
                    ->label('Wochentag')
                    ->options(self::weekdayOptions())
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule, Livewire $livewire) {
                            return $rule->where('branch_id', $livewire->ownerRecord->id);
                        }
                    ),
                Toggle::make('is_closed')
                    ->label('Geschlossen')
                    ->default(false)
                    ->live(),
                TimePicker::make('opens_at')
                    ->label('Öffnet')
                    ->seconds(false)
                    ->disabled(fn(callable $get) => (bool) $get('is_closed'))
                    ->required(fn(callable $get) => ! $get('is_closed'))
                    ->columnSpan(6),
                TimePicker::make('closes_at')
                    ->label('Schließt')
                    ->seconds(false)
                    ->disabled(fn(callable $get) => (bool) $get('is_closed'))
                    ->required(fn(callable $get) => ! $get('is_closed'))
                    ->columnSpan(6),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('weekday')
            ->columns([
                TextColumn::make('weekday')->label('Tag')
                    ->formatStateUsing(fn($state) => self::weekdayOptions()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('opens_at')->label('Öffnet'),
                TextColumn::make('closes_at')->label('Schließt'),
                IconColumn::make('is_closed')->label('Zu')->boolean(),
            ])
            ->defaultSort('weekday')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                // DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
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
