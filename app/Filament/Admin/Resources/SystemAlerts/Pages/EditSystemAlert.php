<?php

namespace App\Filament\Admin\Resources\SystemAlerts\Pages;

use App\Filament\Admin\Resources\SystemAlerts\SystemAlertResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSystemAlert extends EditRecord
{
    protected static string $resource = SystemAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
