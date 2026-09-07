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
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sinceTooltip()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sistema'),

                TextColumn::make('action')
                    ->label('Acción')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Creación',
                        'updated' => 'Actualización',
                        'deleted' => 'Eliminación',
                        'restored' => 'Restauración',
                        'force_deleted' => 'Eliminación permanente',
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
                    ->label('Entidad')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('details')
                    ->label('Detalle')
                    ->limit(70)
                    ->wrap()
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->toggleable(),

                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'auth' => 'Autenticación',
                        'access' => 'Acceso',
                        'candidate' => 'Candidato',
                        'company' => 'Empresa',
                        'vacancy' => 'Vacante',
                        'application' => 'Postulación',
                        default => 'Sistema',
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
                    ->label('Severidad')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'critical' => 'Crítica',
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
                    ->label('Resultado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'success' => 'Exitoso',
                        'failure' => 'Fallido',
                        default => $state ?? 'Sin resultado',
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
                    ->label('Acción')
                    ->options([
                        'created' => 'Creación',
                        'updated' => 'Actualización',
                        'deleted' => 'Eliminación',
                        'restored' => 'Restauración',
                        'force_deleted' => 'Eliminación permanente',
                    ]),
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'auth' => 'Autenticación',
                        'access' => 'Acceso',
                        'candidate' => 'Candidato',
                        'company' => 'Empresa',
                        'vacancy' => 'Vacante',
                        'application' => 'Postulación',
                        'system' => 'Sistema',
                    ]),
                SelectFilter::make('severity')
                    ->label('Severidad')
                    ->options([
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'critical' => 'Crítica',
                    ]),
                SelectFilter::make('result')
                    ->label('Resultado')
                    ->options([
                        'success' => 'Exitoso',
                        'failure' => 'Fallido',
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
