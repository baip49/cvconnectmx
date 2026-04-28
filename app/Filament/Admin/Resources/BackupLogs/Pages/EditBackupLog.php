<?php

namespace App\Filament\Admin\Resources\BackupLogs\Pages;

use App\Filament\Admin\Resources\BackupLogs\BackupLogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBackupLog extends EditRecord
{
    protected static string $resource = BackupLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
