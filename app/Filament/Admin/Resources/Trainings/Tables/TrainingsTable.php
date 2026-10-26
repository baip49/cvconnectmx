<?php

namespace App\Filament\Admin\Resources\Trainings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.trainings.titulo'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('admin.trainings.tipo'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('targetRole.name')
                    ->label(__('admin.trainings.rol_objetivo'))
                    ->placeholder(__('admin.trainings.todos')),

                TextColumn::make('validity_days')
                    ->label(__('admin.trainings.validez'))
                    ->formatStateUsing(fn ($state): string => __('admin.trainings.dias', ['count' => $state])),

                IconColumn::make('is_active')
                    ->label(__('admin.trainings.activo'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('admin.trainings.creado'))
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
