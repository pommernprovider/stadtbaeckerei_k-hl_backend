<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductVisibility;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('branch_images')
                    ->label('Bild')
                    ->collection('product_main')
                    ->conversion('thumb')
                    ->circular()
                    ->toggleable(),
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategorie')->sortable()->toggleable(),
                TextColumn::make('base_price')->label('Preis')->money('eur', true)->sortable(),
                TextColumn::make('visibility_status')
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
                IconColumn::make('is_published')->label('Publiziert')->boolean(),
                TextColumn::make('available_from')->label('ab')->dateTime()->since()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('available_until')->label('bis')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Aktualisiert')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')->relationship('category', 'name'),
                TernaryFilter::make('is_published')->label('Publiziert'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
