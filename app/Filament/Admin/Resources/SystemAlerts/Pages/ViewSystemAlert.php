<?php

namespace App\Filament\Admin\Resources\SystemAlerts\Pages;

use App\Filament\Admin\Resources\SystemAlerts\SystemAlertResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSystemAlert extends ViewRecord
{
    protected static string $resource = SystemAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
