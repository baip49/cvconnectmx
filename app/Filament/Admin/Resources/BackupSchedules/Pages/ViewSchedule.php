<?php

namespace App\Filament\Admin\Resources\BackupSchedules\Pages;

use App\Filament\Admin\Resources\BackupSchedules\BackupScheduleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSchedule extends ViewRecord
{
    protected static string $resource = BackupScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
