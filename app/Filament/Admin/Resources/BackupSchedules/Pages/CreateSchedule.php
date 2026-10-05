<?php

namespace App\Filament\Admin\Resources\BackupSchedules\Pages;

use App\Filament\Admin\Resources\BackupSchedules\BackupScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = BackupScheduleResource::class;
}
