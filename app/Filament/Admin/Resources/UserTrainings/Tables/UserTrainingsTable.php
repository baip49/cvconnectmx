<?php

namespace App\Filament\Admin\Resources\UserTrainings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserTrainingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('training.title')
                    ->label('Capacitación')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'in_progress' => 'En progreso',
                        'completed' => 'Completada',
                        'expired' => 'Expirada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('score')
                    ->label('Calificación')
                    ->placeholder('N/A')
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('Completado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Pendiente'),

                TextColumn::make('expires_at')
                    ->label('Expira')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('N/A'),

                TextColumn::make('created_at')
                    ->label('Asignado')
                    ->dateTime('d/m/Y H:i')
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
