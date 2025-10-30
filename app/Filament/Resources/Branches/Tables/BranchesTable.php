<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('branch_images')
                    ->label('Bild')
                    ->collection('branch_images')
                    ->conversion('thumb')
                    ->circular()
                    ->toggleable(),
                TextColumn::make('number')
                    ->label('Filialnummer')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Bezeichnung')
                    ->searchable(),
                TextColumn::make('street')
                    ->label('Straße')
                    ->searchable(),
                TextColumn::make('zip')
                    ->label('PLZ')
                    ->searchable(),
                TextColumn::make('city')
                    ->label('Ort')
                    ->searchable(),
                TextColumn::make('lat')
                    ->label('Breitengrad')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lng')
                    ->label('Längengrad')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),


                TextColumn::make('order_cutoff_time')
                    ->label('Bestellschlusszeit')
                    ->time()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Zuletzt aktualisiert am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
