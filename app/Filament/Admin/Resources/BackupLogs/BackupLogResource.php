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
use UnitEnum;

class BackupLogResource extends Resource
{
    protected static ?string $model = BackupLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static ?string $navigationLabel = 'Logs de Respaldo';

    protected static string|UnitEnum|null $navigationGroup = 'Seguridad';

    public static function infolist(Schema $schema): Schema
    {
        return BackupLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BackupLogsTable::configure($table);
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
