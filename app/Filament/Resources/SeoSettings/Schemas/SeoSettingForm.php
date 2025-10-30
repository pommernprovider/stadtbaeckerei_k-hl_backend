<?php

namespace App\Filament\Resources\SeoSettings\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeoSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Meta Defaults')->schema([
                    TextInput::make('default_meta_title')->label('Default Meta Title'),
                    Textarea::make('default_meta_description')->rows(3)->label('Default Meta Description'),
                    KeyValue::make('meta_tags')->keyLabel('Name')->valueLabel('Wert')->reorderable()
                        ->helperText('z.B. robots=index,follow | keywords=…'),
                    SpatieMediaLibraryFileUpload::make('og_image')
                        ->label('OG Image (Social Sharing)')
                        ->image()
                        ->collection('og_image')
                        ->downloadable(),
                ])->columns(2),
            ])->columns(1);
    }
}
