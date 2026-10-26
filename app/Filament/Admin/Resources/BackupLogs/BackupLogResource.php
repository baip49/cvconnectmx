<?php

namespace App\Filament\Admin\Resources\BackupLogs;

use App\Filament\Admin\Resources\BackupLogs\Pages\ListBackupLogs;
use App\Filament\Admin\Resources\BackupLogs\Pages\ViewBackupLog;
use App\Filament\Admin\Resources\BackupLogs\Schemas\BackupLogInfolist;
use App\Filament\Admin\Resources\BackupLogs\Tables\BackupLogsTable;
use App\Models\BackupLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BackupLogResource extends Resource
{
    protected static ?string $model = BackupLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    public static function getNavigationLabel(): string
    {
        return __('admin.backup_logs.logs_de_respaldo');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation_groups.seguridad');
    }

    public static function infolist(Schema $schema): Schema
    {
        return BackupLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BackupLogsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBackupLogs::route('/'),
            'view' => ViewBackupLog::route('/{record}'),
        ];
    }
}
