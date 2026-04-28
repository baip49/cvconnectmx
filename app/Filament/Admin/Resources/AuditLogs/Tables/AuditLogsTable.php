<?php

namespace App\Filament\Admin\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sistema'),
                TextColumn::make('action')
                    ->label('Acción')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('entity_type')
                    ->label('Tipo de Entidad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('result')
                    ->label('Resultado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failure' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(50)
            ->toolbarActions([]);
    }
}
