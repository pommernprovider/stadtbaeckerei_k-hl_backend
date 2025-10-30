<?php

namespace App\Filament\Resources\LegalSettings;

use App\Filament\Clusters\Website\WebsiteCluster;
use App\Filament\Resources\LegalSettings\Pages\CreateLegalSetting;
use App\Filament\Resources\LegalSettings\Pages\EditLegalSetting;
use App\Filament\Resources\LegalSettings\Pages\ListLegalSettings;
use App\Filament\Resources\LegalSettings\Schemas\LegalSettingForm;
use App\Filament\Resources\LegalSettings\Tables\LegalSettingsTable;
use App\Models\LegalSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LegalSettingResource extends Resource
{
    protected static ?string $model = LegalSetting::class;
    protected static ?string $cluster = WebsiteCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $modelLabel = 'Rechtliches';
    protected static ?string $pluralModelLabel = 'Impressum & Datenschutz';
    protected static ?string $navigationLabel = 'Impressum & Datenschutz';

    public static function form(Schema $schema): Schema
    {
        return LegalSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LegalSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLegalSettings::route('/'),
            'create' => CreateLegalSetting::route('/create'),
            'edit' => EditLegalSetting::route('/{record}/edit'),
        ];
    }
}
