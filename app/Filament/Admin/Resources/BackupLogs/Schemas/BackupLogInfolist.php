<?php

namespace App\Filament\Admin\Resources\BackupLogs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;

class BackupLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.backup_logs.detalles_del_respaldo'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('type')
                                ->label(__('admin.backup_logs.tipo'))
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'full' => __('admin.backup_logs.completo'),
                                    'incremental' => __('admin.backup_logs.incremental'),
                                    'differential' => __('admin.backup_logs.diferencial'),
                                    default => $state,
                                }),
                            TextEntry::make('status')
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
                                }),
                            TextEntry::make('frequency')
                                ->label(__('admin.backup_logs.frecuencia'))
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'manual' => __('admin.backup_logs.manual'),
                                    'daily' => __('admin.backup_logs.diario'),
                                    'weekly' => __('admin.backup_logs.semanal'),
                                    'monthly' => __('admin.backup_logs.mensual'),
                                    default => $state,
                                }),
                            TextEntry::make('scope')
                                ->label(__('admin.backup_logs.alcance'))
                                ->badge()
                                ->formatStateUsing(fn (?string $state): string => match ($state) {
                                    'database' => __('admin.backup_logs.base_de_datos'),
                                    'files' => __('admin.backup_logs.archivos'),
                                    'both' => __('admin.backup_logs.base_de_datos_y_archivos'),
                                    default => $state ?? __('admin.backup_logs.base_de_datos'),
                                }),
                            TextEntry::make('destination_path')
                                ->label(__('admin.backup_logs.destino'))
                                ->columnSpanFull(),
                            IconEntry::make('is_encrypted')
                                ->label(__('admin.backup_logs.cifrado'))
                                ->boolean(),
                            IconEntry::make('restoration_tested')
                                ->label(__('admin.backup_logs.restauracion_probada'))
                                ->boolean(),
                            TextEntry::make('size_bytes')
                                ->label(__('admin.backup_logs.tamano'))
                                ->formatStateUsing(fn ($state): string => round($state / 1024 / 1024, 2).' MB'),
                            TextEntry::make('retention_days')
                                ->label(__('admin.backup_logs.retencion_dias_2'))
                                ->suffix(__('admin.backup_logs.dias')),
                        ]),
                    ]),
                Section::make(__('admin.backup_logs.informacion_de_ejecucion'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('executedBy.name')
                                ->label(__('admin.backup_logs.ejecutado_por'))
                                ->placeholder(__('admin.backup_logs.sistema')),
                            TextEntry::make('checksum_sha256')
                                ->label(__('admin.backup_logs.checksum_sha256'))
                                ->fontFamily(FontFamily::Mono)
                                ->copyable()
                                ->columnSpanFull()
                                ->extraAttributes(['class' => 'break-all']),
                            TextEntry::make('created_at')
                                ->label(__('admin.backup_logs.fecha_de_ejecucion'))
                                ->dateTime('d/m/Y H:i:s'),
                        ]),
                    ]),
            ]);
    }
}
