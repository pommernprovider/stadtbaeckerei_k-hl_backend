<?php

namespace App\Filament\Resources\BrandSettings\Pages;

use App\Filament\Resources\BrandSettings\BrandSettingResource;
use App\Models\BrandSetting;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBrandSetting extends EditRecord
{
    protected static string $resource = BrandSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
        ];
    }

    public function mount($record = null): void
    {
        $this->record = BrandSetting::singleton();
        parent::mount($this->record->getKey());
    }
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Branding gespeichert';
    }
}
