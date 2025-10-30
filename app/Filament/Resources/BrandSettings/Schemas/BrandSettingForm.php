<?php

namespace App\Filament\Resources\BrandSettings\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Allgemein')->schema([
                    TextInput::make('site_name')->label('Seitename'),
                    TextInput::make('contact_email')->email(),
                    TextInput::make('contact_phone'),
                    // KeyValue::make('social_links')->keyLabel('Plattform')->valueLabel('URL')->reorderable(),
                ])->columns(1),

                Section::make('Medien')->schema([
                    SpatieMediaLibraryFileUpload::make('logo')->label('Logo')->image()->imageEditor()->collection('logo')->downloadable(),
                    SpatieMediaLibraryFileUpload::make('favicon')->label('Favicon')->image()->imageEditor()->collection('favicon')->downloadable(),
                ])->columns(1),
            ]);
    }
}
