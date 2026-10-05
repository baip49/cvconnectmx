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
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('scope')
                    ->label('Qué respalda')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ScheduleForm::scopes()[$state] ?? $state)
                    ->color('gray'),

                TextColumn::make('frequency')
                    ->label('Frecuencia')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ScheduleForm::frequencies()[$state] ?? $state)
                    ->color('gray'),

                TextColumn::make('time')
                    ->label('Hora')
                    ->time('H:i'),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),

                TextColumn::make('last_run_at')
                    ->label('Última ejecución')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Nunca')
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
