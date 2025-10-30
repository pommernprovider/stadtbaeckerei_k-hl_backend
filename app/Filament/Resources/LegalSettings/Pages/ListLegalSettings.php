<?php

namespace App\Filament\Resources\LegalSettings\Pages;

use App\Filament\Resources\LegalSettings\LegalSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLegalSettings extends ListRecords
{
    protected static string $resource = LegalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
