<?php

namespace App\Filament\Admin\Resources\BackupSchedules\Tables;

use App\Filament\Admin\Resources\BackupSchedules\Schemas\ScheduleForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.backup_schedules.nombre'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('scope')
                    ->label(__('admin.backup_schedules.que_respalda'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ScheduleForm::scopes()[$state] ?? $state)
                    ->color('gray'),

                TextColumn::make('frequency')
                    ->label(__('admin.backup_schedules.frecuencia'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ScheduleForm::frequencies()[$state] ?? $state)
                    ->color('gray'),

                TextColumn::make('time')
                    ->label(__('admin.backup_schedules.hora'))
                    ->time('H:i'),

                IconColumn::make('is_active')
                    ->label(__('admin.backup_schedules.activo'))
                    ->boolean(),

                TextColumn::make('last_run_at')
                    ->label(__('admin.backup_schedules.ultima_ejecucion'))
                    ->dateTime('d/m/Y H:i')
                    ->placeholder(__('admin.backup_schedules.nunca'))
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
