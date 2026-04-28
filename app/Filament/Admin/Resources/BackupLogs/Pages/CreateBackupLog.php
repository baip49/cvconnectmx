<?php

namespace App\Filament\Admin\Resources\BackupLogs\Pages;

use App\Filament\Admin\Resources\BackupLogs\BackupLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBackupLog extends CreateRecord
{
    protected static string $resource = BackupLogResource::class;
}
