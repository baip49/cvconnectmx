<?php

namespace App\Filament\Admin\Resources\BackupLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BackupLogsTable
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

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'success' => 'Exitoso',
                        'failed' => 'Fallido',
                        'in_progress' => 'En progreso',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'in_progress' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('frequency')
                    ->label('Frecuencia')
                    ->searchable(),

                TextColumn::make('destination_path')
                    ->label('Destino')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->destination_path),

                IconColumn::make('is_encrypted')
                    ->label('Cifrado')
                    ->boolean(),

                TextColumn::make('size_bytes')
                    ->label('Tamaño')
                    ->formatStateUsing(fn ($state): string => round($state / 1024 / 1024, 2).' MB'),

                TextColumn::make('executedBy.name')
                    ->label('Ejecutado por')
                    ->placeholder('Sistema'),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'success' => 'Exitoso',
                        'failed' => 'Fallido',
                        'in_progress' => 'En progreso',
                    ]),
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'full' => 'Completo',
                        'incremental' => 'Incremental',
                        'differential' => 'Diferencial',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
