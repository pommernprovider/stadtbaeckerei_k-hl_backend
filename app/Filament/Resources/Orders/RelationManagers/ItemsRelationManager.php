<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\OrderItem;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name_snapshot')
            ->columns([
                TextColumn::make('product_name_snapshot')->label('Artikel'),
                TextColumn::make('quantity')->label('Menge'),
                TextColumn::make('price_snapshot')->label('Preis/Stk')->money('eur', true),
                TextColumn::make('line_total')->label('Summe')->money('eur', true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),


            ])
            ->recordActions([
                Action::make('details')
                    ->label('Details')
                    ->icon('heroicon-m-eye')
                    ->modalHeading(fn(OrderItem $record) => 'Optionen – ' . $record->product_name_snapshot)
                    ->modalContent(function (OrderItem $record) {
                        $record->loadMissing('options');

                        if (! $record->options->count()) {
                            return view('components.simple-pre', ['text' => 'Keine Optionen']);
                        }

                        $list = $record->options->map(function ($o) {
                            $val   = $o->value_label_snapshot ?? $o->free_text ?? '—';
                            $delta = (float) $o->price_delta_snapshot;
                            $deltaTxt = $delta == 0.0 ? '' : ' (Δ ' . number_format($delta, 2, ',', '.') . ' €)';
                            return "{$o->option_name_snapshot}: {$val}{$deltaTxt}";
                        })->implode("\n");

                        return view('components.simple-pre', ['text' => $list]);
                    })
                    ->modalSubmitAction(false), // nur Ansicht
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
