<?php

namespace App\Filament\Admin\Resources\BackupLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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
                    ->label(__('admin.backup_logs.tipo'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'full' => __('admin.backup_logs.completo'),
                        'incremental' => __('admin.backup_logs.incremental'),
                        'differential' => __('admin.backup_logs.diferencial'),
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('admin.backup_logs.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'success' => __('admin.backup_logs.exitoso'),
                        'failed' => __('admin.backup_logs.fallido'),
                        'in_progress' => __('admin.backup_logs.en_progreso'),
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
                    ->label(__('admin.backup_logs.frecuencia'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'manual' => __('admin.backup_logs.manual'),
                        'daily' => __('admin.backup_logs.diario'),
                        'weekly' => __('admin.backup_logs.semanal'),
                        'monthly' => __('admin.backup_logs.mensual'),
                        default => $state,
                    })
                    ->searchable(),

                TextColumn::make('scope')
                    ->label(__('admin.backup_logs.alcance'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'database' => __('admin.backup_logs.base_de_datos'),
                        'files' => __('admin.backup_logs.archivos'),
                        'both' => __('admin.backup_logs.base_de_datos_y_archivos'),
                        default => $state ?? __('admin.backup_logs.base_de_datos'),
                    }),

                TextColumn::make('destination_path')
                    ->label(__('admin.backup_logs.destino'))
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->destination_path),

                IconColumn::make('is_encrypted')
                    ->label(__('admin.backup_logs.cifrado'))
                    ->boolean(),

                TextColumn::make('size_bytes')
                    ->label(__('admin.backup_logs.tamano'))
                    ->formatStateUsing(fn ($state): string => round($state / 1024 / 1024, 2).' MB'),

                TextColumn::make('executedBy.name')
                    ->label(__('admin.backup_logs.ejecutado_por'))
                    ->placeholder(__('admin.backup_logs.sistema')),

                TextColumn::make('created_at')
                    ->label(__('admin.backup_logs.fecha'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.backup_logs.estado'))
                    ->options([
                        'success' => __('admin.backup_logs.exitoso'),
                        'failed' => __('admin.backup_logs.fallido'),
                        'in_progress' => __('admin.backup_logs.en_progreso'),
                    ]),
                SelectFilter::make('type')
                    ->label(__('admin.backup_logs.tipo'))
                    ->options([
                        'full' => __('admin.backup_logs.completo'),
                        'incremental' => __('admin.backup_logs.incremental'),
                        'differential' => __('admin.backup_logs.diferencial'),
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make()
                    ->label(__('admin.backup_logs.eliminar'))
                    ->modalHeading(__('admin.backup_logs.eliminar_respaldo'))
                    ->modalDescription(__('admin.backup_logs.se_eliminara_el_registro_y_el_archivo_de')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
