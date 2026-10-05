<?php

namespace App\Filament\Admin\Resources\BackupSchedules\Pages;

use App\Filament\Admin\Resources\BackupSchedules\BackupScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = BackupScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
