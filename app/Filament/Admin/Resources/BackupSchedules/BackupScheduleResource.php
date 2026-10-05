<?php

namespace App\Filament\Admin\Resources\BackupSchedules;

use App\Filament\Admin\Resources\BackupSchedules\Pages\CreateSchedule;
use App\Filament\Admin\Resources\BackupSchedules\Pages\EditSchedule;
use App\Filament\Admin\Resources\BackupSchedules\Pages\ListSchedules;
use App\Filament\Admin\Resources\BackupSchedules\Pages\ViewSchedule;
use App\Filament\Admin\Resources\BackupSchedules\Schemas\ScheduleForm;
use App\Filament\Admin\Resources\BackupSchedules\Schemas\ScheduleInfolist;
use App\Filament\Admin\Resources\BackupSchedules\Tables\SchedulesTable;
use App\Models\BackupSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BackupScheduleResource extends Resource
{
    protected static ?string $model = BackupSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Respaldos Programados';

    protected static string|UnitEnum|null $navigationGroup = 'Seguridad';

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
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
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'view' => ViewSchedule::route('/{record}'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }
}
