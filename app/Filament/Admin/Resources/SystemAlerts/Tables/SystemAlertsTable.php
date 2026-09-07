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
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('level')
                    ->label('Nivel')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'info' => 'Informativo',
                        'warning' => 'Advertencia',
                        'error' => 'Error',
                        'critical' => 'Crítico',
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
                    ->label('Mensaje')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->message),

                TextColumn::make('user.name')
                    ->label('Asignado a')
                    ->searchable()
                    ->placeholder('Sin asignar'),

                TextColumn::make('resolved_at')
                    ->label('Resuelto')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Pendiente'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label('Nivel')
                    ->options([
                        'info' => 'Informativo',
                        'warning' => 'Advertencia',
                        'error' => 'Error',
                        'critical' => 'Crítico',
                    ]),
                SelectFilter::make('is_resolved')
                    ->label('Estado')
                    ->options([
                        0 => 'Pendiente',
                        1 => 'Resuelta',
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
