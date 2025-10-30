<?php

namespace App\Filament\Resources\BrandSettings\Pages;

use App\Filament\Resources\BrandSettings\BrandSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBrandSettings extends ListRecords
{
    protected static string $resource = BrandSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
