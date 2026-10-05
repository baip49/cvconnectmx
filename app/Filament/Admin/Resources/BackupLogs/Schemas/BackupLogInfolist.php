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
                Section::make('Detalles del Respaldo')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('type')
                                ->label('Tipo')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'full' => 'Completo',
                                    'incremental' => 'Incremental',
                                    'differential' => 'Diferencial',
                                    default => $state,
                                }),
                            TextEntry::make('status')
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
                                }),
                            TextEntry::make('frequency')
                                ->label('Frecuencia')
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'manual' => 'Manual',
                                    'daily' => 'Diario',
                                    'weekly' => 'Semanal',
                                    'monthly' => 'Mensual',
                                    default => $state,
                                }),
                            TextEntry::make('scope')
                                ->label('Alcance')
                                ->badge()
                                ->formatStateUsing(fn (?string $state): string => match ($state) {
                                    'database' => 'Base de datos',
                                    'files' => 'Archivos',
                                    'both' => 'Base de datos y archivos',
                                    default => $state ?? 'Base de datos',
                                }),
                            TextEntry::make('destination_path')
                                ->label('Destino')
                                ->columnSpanFull(),
                            IconEntry::make('is_encrypted')
                                ->label('Cifrado')
                                ->boolean(),
                            IconEntry::make('restoration_tested')
                                ->label('Restauración Probada')
                                ->boolean(),
                            TextEntry::make('size_bytes')
                                ->label('Tamaño')
                                ->formatStateUsing(fn ($state): string => round($state / 1024 / 1024, 2).' MB'),
                            TextEntry::make('retention_days')
                                ->label('Retención (días')
                                ->suffix(' días'),
                        ]),
                    ]),
                Section::make('Información de Ejecución')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('executedBy.name')
                                ->label('Ejecutado por')
                                ->placeholder('Sistema'),
                            TextEntry::make('checksum_sha256')
                                ->label('Checksum SHA256')
                                ->fontFamily(FontFamily::Mono)
                                ->copyable()
                                ->columnSpanFull()
                                ->extraAttributes(['class' => 'break-all']),
                            TextEntry::make('created_at')
                                ->label('Fecha de Ejecución')
                                ->dateTime('d/m/Y H:i:s'),
                        ]),
                    ]),
            ]);
    }
}
