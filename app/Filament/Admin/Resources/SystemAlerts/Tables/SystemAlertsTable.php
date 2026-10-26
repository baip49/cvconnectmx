<?php

namespace App\Filament\Admin\Resources\SystemAlerts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SystemAlertsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_resolved')
                    ->label(__('admin.system_alerts.estado'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('type')
                    ->label(__('admin.system_alerts.tipo'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'security' => __('admin.system_alerts.seguridad'),
                        'maintenance' => __('admin.system_alerts.mantenimiento'),
                        'system' => __('admin.system_alerts.sistema'),
                        'system.memory' => __('admin.system_alerts.memoria'),
                        'system.cpu' => __('admin.system_alerts.cpu'),
                        'system.disk' => __('admin.system_alerts.disco'),
                        'system.queue' => __('admin.system_alerts.cola'),
                        default => $state,
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'security' => __('admin.system_alerts.seguridad'),
                        'maintenance' => __('admin.system_alerts.mantenimiento'),
                        'system' => __('admin.system_alerts.sistema'),
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('level')
                    ->label(__('admin.system_alerts.nivel'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'info' => __('admin.system_alerts.informativo'),
                        'warning' => __('admin.system_alerts.advertencia'),
                        'error' => __('admin.system_alerts.error'),
                        'critical' => __('admin.system_alerts.critico'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'info',
                        'warning' => 'warning',
                        'error' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('message')
                    ->label(__('admin.system_alerts.mensaje'))
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->message),

                TextColumn::make('user.name')
                    ->label(__('admin.system_alerts.asignado_a'))
                    ->searchable()
                    ->placeholder(__('admin.system_alerts.sin_asignar')),

                TextColumn::make('resolved_at')
                    ->label(__('admin.system_alerts.resuelto'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder(__('admin.system_alerts.pendiente')),

                TextColumn::make('created_at')
                    ->label(__('admin.system_alerts.creado'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label(__('admin.system_alerts.nivel'))
                    ->options([
                        'info' => __('admin.system_alerts.informativo'),
                        'warning' => __('admin.system_alerts.advertencia'),
                        'error' => __('admin.system_alerts.error'),
                        'critical' => __('admin.system_alerts.critico'),
                    ]),
                SelectFilter::make('is_resolved')
                    ->label(__('admin.system_alerts.estado'))
                    ->options([
                        0 => __('admin.system_alerts.pendiente'),
                        1 => __('admin.system_alerts.resuelta'),
                    ]),
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
