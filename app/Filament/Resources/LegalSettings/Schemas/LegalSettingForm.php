<?php

namespace App\Filament\Resources\LegalSettings\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class LegalSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('legalTabs')
                    ->tabs([
                        Tab::make('Impressum')->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('impressum_title')
                                        ->label('Titel')
                                        ->default('Impressum'),
                                    RichEditor::make('impressum_html')

                                        ->columnSpanFull(),
                                ]),
                        ]),
                        Tab::make('Datenschutz')->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('datenschutz_title')
                                        ->label('Titel')
                                        ->default('Datenschutzerklärung'),
                                    RichEditor::make('datenschutz_html')
                                        ->columnSpanFull(),
                                ])
                        ]),
                        Tab::make('AGB')->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('agb_title')
                                        ->label('Titel')
                                        ->default('Allgemeine Geschäftsbedingungen'),
                                    RichEditor::make('agb_html')
                                        ->columnSpanFull(),
                                ])
                        ]),
                        Tab::make('Widerruf (optional)')->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('widerruf_title')
                                        ->label('Titel'),
                                    RichEditor::make('widerruf_html')
                                        ->columnSpanFull(),
                                ])
                        ]),
                    ])


            ])
            ->columns(1);
    }
}
