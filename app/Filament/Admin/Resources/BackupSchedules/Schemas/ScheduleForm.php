<?php

namespace App\Filament\Admin\Resources\BackupSchedules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function scopes(): array
    {
        return [
            'database' => __('admin.backup_schedules.base_de_datos'),
            'files' => __('admin.backup_schedules.archivos'),
            'both' => __('admin.backup_schedules.base_de_datos_y_archivos'),
        ];
    }

    public static function frequencies(): array
    {
        return [
            'daily' => __('admin.backup_schedules.diario'),
            'weekly' => __('admin.backup_schedules.semanal'),
            'monthly' => __('admin.backup_schedules.mensual'),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.backup_schedules.programacion_de_respaldo'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('admin.backup_schedules.nombre'))
                                ->required()
                                ->maxLength(255),
                            Select::make('scope')
                                ->label(__('admin.backup_schedules.que_respaldar'))
                                ->options(self::scopes())
                                ->default('database')
                                ->required(),
                            Select::make('frequency')
                                ->label(__('admin.backup_schedules.cada_cuanto'))
                                ->options(self::frequencies())
                                ->default('daily')
                                ->required(),
                            TimePicker::make('time')
                                ->label(__('admin.backup_schedules.hora'))
                                ->default('02:00')
                                ->required(),
                            TextInput::make('retention_days')
                                ->label(__('admin.backup_schedules.retencion_dias'))
                                ->numeric()
                                ->minValue(1)
                                ->default(30)
                                ->required(),
                            Toggle::make('is_active')
                                ->label(__('admin.backup_schedules.activo'))
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
