<?php

namespace App\Filament\Admin\Resources\SystemAlerts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SystemAlertInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.system_alerts.detalles_de_la_alerta'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('type')
                                ->label(__('admin.system_alerts.tipo'))
                                ->badge(),
                            TextEntry::make('level')
                                ->label(__('admin.system_alerts.nivel'))
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'info' => __('admin.system_alerts.informativo'),
                                    'warning' => __('admin.system_alerts.advertencia'),
                                    'error' => __('admin.system_alerts.error'),
                                    'critical' => __('admin.system_alerts.critico'),
                                    default => $state,
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'info' => 'info',
                                    'warning' => 'warning',
                                    'error' => 'danger',
                                    'critical' => 'danger',
                                    default => 'gray',
                                }),
                            TextEntry::make('is_resolved')
                                ->label(__('admin.system_alerts.estado'))
                                ->badge()
                                ->formatStateUsing(fn (bool $state): string => $state ? __('admin.system_alerts.resuelta') : __('admin.system_alerts.pendiente'))
                                ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
                            TextEntry::make('user.name')
                                ->label(__('admin.system_alerts.asignado_a'))
                                ->placeholder(__('admin.system_alerts.sin_asignar')),
                            TextEntry::make('reviewedBy.name')
                                ->label(__('admin.system_alerts.revisado_por'))
                                ->placeholder('N/A'),
                            TextEntry::make('created_at')
                                ->label(__('admin.system_alerts.creado'))
                                ->dateTime('d/m/Y H:i'),
                        ]),
                    ]),
                Section::make(__('admin.system_alerts.mensaje'))
                    ->schema([
                        TextEntry::make('message')
                            ->label(__('admin.system_alerts.mensaje'))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.system_alerts.resolucion'))
                    ->schema([
                        TextEntry::make('resolved_at')
                            ->label(__('admin.system_alerts.fecha_de_resolucion'))
                            ->dateTime('d/m/Y H:i')
                            ->placeholder(__('admin.system_alerts.pendiente')),
                        TextEntry::make('resolution_notes')
                            ->label(__('admin.system_alerts.notas_de_resolucion'))
                            ->placeholder(__('admin.system_alerts.sin_notas'))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->is_resolved),
            ]);
    }
}
