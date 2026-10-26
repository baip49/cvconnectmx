<?php

namespace App\Filament\Admin\Resources\BackupSchedules\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.backup_schedules.programacion_de_respaldo'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('name')
                                ->label(__('admin.backup_schedules.nombre')),
                            TextEntry::make('scope')
                                ->label(__('admin.backup_schedules.que_respalda'))
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => ScheduleForm::scopes()[$state] ?? $state),
                            TextEntry::make('frequency')
                                ->label(__('admin.backup_schedules.frecuencia'))
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => ScheduleForm::frequencies()[$state] ?? $state),
                            TextEntry::make('time')
                                ->label(__('admin.backup_schedules.hora'))
                                ->time('H:i'),
                            TextEntry::make('retention_days')
                                ->label(__('admin.backup_schedules.retencion'))
                                ->suffix(__('admin.backup_schedules.dias')),
                            IconEntry::make('is_active')
                                ->label(__('admin.backup_schedules.activo'))
                                ->boolean(),
                            TextEntry::make('last_run_at')
                                ->label(__('admin.backup_schedules.ultima_ejecucion'))
                                ->dateTime('d/m/Y H:i')
                                ->placeholder(__('admin.backup_schedules.nunca')),
                        ]),
                    ]),
            ]);
    }
}
