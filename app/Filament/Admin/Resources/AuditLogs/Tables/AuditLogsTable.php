<?php

namespace App\Filament\Admin\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('admin.audit_logs.fecha'))
                    ->dateTime('d/m/Y H:i')
                    ->sinceTooltip()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('admin.audit_logs.usuario'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('admin.audit_logs.sistema')),

                TextColumn::make('action')
                    ->label(__('admin.audit_logs.accion'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => __('admin.audit_logs.creacion'),
                        'updated' => __('admin.audit_logs.actualizacion'),
                        'deleted' => __('admin.audit_logs.eliminacion'),
                        'restored' => __('admin.audit_logs.restauracion'),
                        'force_deleted' => __('admin.audit_logs.eliminacion_permanente'),
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'restored' => 'warning',
                        'force_deleted' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('entity_type')
                    ->label(__('admin.audit_logs.entidad'))
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('details')
                    ->label(__('admin.audit_logs.detalle'))
                    ->limit(70)
                    ->wrap()
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->toggleable(),

                TextColumn::make('category')
                    ->label(__('admin.audit_logs.categoria'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'auth' => __('admin.audit_logs.autenticacion'),
                        'access' => __('admin.audit_logs.acceso'),
                        'candidate' => __('admin.audit_logs.candidato'),
                        'company' => __('admin.audit_logs.empresa'),
                        'vacancy' => __('admin.audit_logs.vacante'),
                        'application' => __('admin.audit_logs.postulacion'),
                        default => __('admin.audit_logs.sistema'),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'auth' => 'info',
                        'candidate' => 'success',
                        'company' => 'warning',
                        'vacancy' => 'primary',
                        'application' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('severity')
                    ->label(__('admin.audit_logs.severidad'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => __('admin.audit_logs.baja'),
                        'medium' => __('admin.audit_logs.media'),
                        'high' => __('admin.audit_logs.alta'),
                        'critical' => __('admin.audit_logs.critica'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('result')
                    ->label(__('admin.audit_logs.resultado'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'success' => __('admin.audit_logs.exitoso'),
                        'failure' => __('admin.audit_logs.fallido'),
                        default => $state ?? __('admin.audit_logs.sin_resultado'),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failure' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label(__('admin.audit_logs.accion'))
                    ->options([
                        'created' => __('admin.audit_logs.creacion'),
                        'updated' => __('admin.audit_logs.actualizacion'),
                        'deleted' => __('admin.audit_logs.eliminacion'),
                        'restored' => __('admin.audit_logs.restauracion'),
                        'force_deleted' => __('admin.audit_logs.eliminacion_permanente'),
                    ]),
                SelectFilter::make('category')
                    ->label(__('admin.audit_logs.categoria'))
                    ->options([
                        'auth' => __('admin.audit_logs.autenticacion'),
                        'access' => __('admin.audit_logs.acceso'),
                        'candidate' => __('admin.audit_logs.candidato'),
                        'company' => __('admin.audit_logs.empresa'),
                        'vacancy' => __('admin.audit_logs.vacante'),
                        'application' => __('admin.audit_logs.postulacion'),
                        'system' => __('admin.audit_logs.sistema'),
                    ]),
                SelectFilter::make('severity')
                    ->label(__('admin.audit_logs.severidad'))
                    ->options([
                        'low' => __('admin.audit_logs.baja'),
                        'medium' => __('admin.audit_logs.media'),
                        'high' => __('admin.audit_logs.alta'),
                        'critical' => __('admin.audit_logs.critica'),
                    ]),
                SelectFilter::make('result')
                    ->label(__('admin.audit_logs.resultado'))
                    ->options([
                        'success' => __('admin.audit_logs.exitoso'),
                        'failure' => __('admin.audit_logs.fallido'),
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(50)
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
