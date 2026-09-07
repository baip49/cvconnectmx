<?php

namespace App\Filament\Admin\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Registro')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Usuario')
                                    ->placeholder('Sistema'),
                                TextEntry::make('action')
                                    ->label('Acción')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'created' => 'Creación',
                                        'updated' => 'Actualización',
                                        'deleted' => 'Eliminación',
                                        'restored' => 'Restauración',
                                        'force_deleted' => 'Eliminación permanente',
                                        default => ucfirst($state),
                                    })
                                    ->color(fn (string $state): string => match ($state) {
                                        'created' => 'success',
                                        'updated' => 'info',
                                        'deleted' => 'danger',
                                        'restored' => 'warning',
                                        'force_deleted' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('result')
                                    ->label('Resultado')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'success' => 'Exitoso',
                                        'failure' => 'Fallido',
                                        default => $state ?? 'Sin resultado',
                                    })
                                    ->color(fn (?string $state): string => match ($state) {
                                        'success' => 'success',
                                        'failure' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('category')
                                    ->label('Categoría')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'auth' => 'Autenticación',
                                        'access' => 'Acceso',
                                        'candidate' => 'Candidato',
                                        'company' => 'Empresa',
                                        'vacancy' => 'Vacante',
                                        'application' => 'Postulación',
                                        default => 'Sistema',
                                    })
                                    ->color(fn (?string $state): string => match ($state) {
                                        'auth' => 'info',
                                        'candidate' => 'success',
                                        'company' => 'warning',
                                        'vacancy' => 'primary',
                                        'application' => 'info',
                                        default => 'gray',
                                    }),
                                TextEntry::make('severity')
                                    ->label('Severidad')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'low' => 'Baja',
                                        'medium' => 'Media',
                                        'high' => 'Alta',
                                        'critical' => 'Crítica',
                                        default => $state,
                                    })
                                    ->color(fn (string $state): string => match ($state) {
                                        'low' => 'success',
                                        'medium' => 'warning',
                                        'high' => 'danger',
                                        'critical' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('entity_type')
                                    ->label('Tipo de Entidad'),
                                TextEntry::make('entity_id')
                                    ->label('ID de Entidad'),
                                TextEntry::make('ip_address')
                                    ->label('Dirección IP'),
                                TextEntry::make('created_at')
                                    ->label('Fecha y Hora')
                                    ->dateTime('d/m/Y H:i:s'),
                            ]),
                    ]),
                Section::make('Información del Dispositivo')
                    ->schema([
                        TextEntry::make('user_agent')
                            ->label('Navegador / Dispositivo')
                            ->placeholder('No registrado')
                            ->columnSpanFull()
                            ->limit(100)
                            ->tooltip(fn ($record) => $record->user_agent),
                    ]),
                Section::make('Detalles')
                    ->schema([
                        TextEntry::make('details')
                            ->label('Descripción')
                            ->placeholder('Sin detalles adicionales')
                            ->wrap()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->details)),
                Section::make('Cambios en los Datos')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                KeyValueEntry::make('old_data')
                                    ->label('Datos Anteriores'),
                                KeyValueEntry::make('new_data')
                                    ->label('Datos Nuevos'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
