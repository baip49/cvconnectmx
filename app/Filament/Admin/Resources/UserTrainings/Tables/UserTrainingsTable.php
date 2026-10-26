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
                    ->label(__('admin.user_trainings.usuario'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('training.title')
                    ->label(__('admin.user_trainings.capacitacion'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('admin.user_trainings.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('admin.user_trainings.pendiente'),
                        'in_progress' => __('admin.user_trainings.en_progreso'),
                        'completed' => __('admin.user_trainings.completada'),
                        'expired' => __('admin.user_trainings.expirada'),
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
                    ->label(__('admin.user_trainings.calificacion'))
                    ->placeholder('N/A')
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label(__('admin.user_trainings.completado'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder(__('admin.user_trainings.pendiente')),

                TextColumn::make('expires_at')
                    ->label(__('admin.user_trainings.expira'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('N/A'),

                TextColumn::make('created_at')
                    ->label(__('admin.user_trainings.asignado'))
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
