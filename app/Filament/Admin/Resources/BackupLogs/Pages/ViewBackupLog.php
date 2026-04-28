<?php

namespace App\Filament\Admin\Resources\BackupLogs\Pages;

use App\Filament\Admin\Resources\BackupLogs\BackupLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBackupLog extends ViewRecord
{
    protected static string $resource = BackupLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
