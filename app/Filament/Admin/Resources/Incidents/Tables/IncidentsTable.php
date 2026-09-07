<?php

namespace App\Filament\Admin\Resources\Incidents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class IncidentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
                        'low' => 'Bajo',
                        'medium' => 'Medio',
                        'high' => 'Alto',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Abierto',
                        'in_progress' => 'En progreso',
                        'resolved' => 'Resuelto',
                        'closed' => 'Cerrado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('affectedUser.name')
                    ->label('Afectado')
                    ->searchable()
                    ->placeholder('N/A'),

                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),

                TextColumn::make('detected_at')
                    ->label('Detectado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label('Nivel')
                    ->options([
                        'low' => 'Bajo',
                        'medium' => 'Medio',
                        'high' => 'Alto',
                    ]),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'open' => 'Abierto',
                        'in_progress' => 'En progreso',
                        'resolved' => 'Resuelto',
                        'closed' => 'Cerrado',
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
