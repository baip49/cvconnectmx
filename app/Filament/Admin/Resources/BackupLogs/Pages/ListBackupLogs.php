<?php

namespace App\Filament\Admin\Resources\BackupLogs\Pages;

use App\Filament\Admin\Resources\BackupLogs\BackupLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBackupLogs extends ListRecords
{
    protected static string $resource = BackupLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
