<?php

namespace App\Filament\Admin\Resources\BackupSchedules\Pages;

use App\Filament\Admin\Resources\BackupSchedules\BackupScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = BackupScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Programar respaldo'),
        ];
    }
}
