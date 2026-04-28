<?php

namespace App\Filament\Admin\Resources\SystemAlerts\Pages;

use App\Filament\Admin\Resources\SystemAlerts\SystemAlertResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSystemAlerts extends ListRecords
{
    protected static string $resource = SystemAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
