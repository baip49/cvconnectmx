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
                Section::make(__('admin.audit_logs.detalles_del_registro'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('admin.audit_logs.usuario'))
                                    ->placeholder(__('admin.audit_logs.sistema')),
                                TextEntry::make('action')
                                    ->label(__('admin.audit_logs.accion'))
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'created' => __('admin.audit_logs.creacion'),
                                        'updated' => __('admin.audit_logs.actualizacion'),
                                        'deleted' => __('admin.audit_logs.eliminacion'),
                                        'restored' => __('admin.audit_logs.restauracion'),
                                        'force_deleted' => __('admin.audit_logs.eliminacion_permanente'),
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
                                    ->label(__('admin.audit_logs.resultado'))
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'success' => __('admin.audit_logs.exitoso'),
                                        'failure' => __('admin.audit_logs.fallido'),
                                        default => $state ?? __('admin.audit_logs.sin_resultado'),
                                    })
                                    ->color(fn (?string $state): string => match ($state) {
                                        'success' => 'success',
                                        'failure' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('category')
                                    ->label(__('admin.audit_logs.categoria'))
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'auth' => __('admin.audit_logs.autenticacion'),
                                        'access' => __('admin.audit_logs.acceso'),
                                        'candidate' => __('admin.audit_logs.candidato'),
                                        'company' => __('admin.audit_logs.empresa'),
                                        'vacancy' => __('admin.audit_logs.vacante'),
                                        'application' => __('admin.audit_logs.postulacion'),
                                        default => __('admin.audit_logs.sistema'),
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
                                    ->label(__('admin.audit_logs.severidad'))
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'low' => __('admin.audit_logs.baja'),
                                        'medium' => __('admin.audit_logs.media'),
                                        'high' => __('admin.audit_logs.alta'),
                                        'critical' => __('admin.audit_logs.critica'),
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
                                    ->label(__('admin.audit_logs.tipo_de_entidad')),
                                TextEntry::make('entity_id')
                                    ->label(__('admin.audit_logs.id_de_entidad')),
                                TextEntry::make('ip_address')
                                    ->label(__('admin.audit_logs.direccion_ip')),
                                TextEntry::make('created_at')
                                    ->label(__('admin.audit_logs.fecha_y_hora'))
                                    ->dateTime('d/m/Y H:i:s'),
                            ]),
                    ]),
                Section::make(__('admin.audit_logs.informacion_del_dispositivo'))
                    ->schema([
                        TextEntry::make('user_agent')
                            ->label(__('admin.audit_logs.navegador_dispositivo'))
                            ->placeholder(__('admin.audit_logs.no_registrado'))
                            ->columnSpanFull()
                            ->limit(100)
                            ->tooltip(fn ($record) => $record->user_agent),
                    ]),
                Section::make(__('admin.audit_logs.detalles'))
                    ->schema([
                        TextEntry::make('details')
                            ->label(__('admin.audit_logs.descripcion'))
                            ->placeholder(__('admin.audit_logs.sin_detalles_adicionales'))
                            ->wrap()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->details)),
                Section::make(__('admin.audit_logs.cambios_en_los_datos'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                KeyValueEntry::make('old_data')
                                    ->label(__('admin.audit_logs.datos_anteriores')),
                                KeyValueEntry::make('new_data')
                                    ->label(__('admin.audit_logs.datos_nuevos')),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
