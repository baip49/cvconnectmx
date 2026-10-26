<?php

namespace App\Filament\Admin\Resources\SystemAlerts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SystemAlertForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.system_alerts.informacion_de_la_alerta'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('type')
                                ->label(__('admin.system_alerts.tipo_de_alerta'))
                                ->required()
                                ->maxLength(255),
                            Select::make('level')
                                ->label(__('admin.system_alerts.nivel'))
                                ->options([
                                    'info' => __('admin.system_alerts.informativo'),
                                    'warning' => __('admin.system_alerts.advertencia'),
                                    'error' => __('admin.system_alerts.error'),
                                    'critical' => __('admin.system_alerts.critico'),
                                ])
                                ->required(),
                            Select::make('user_id')
                                ->label(__('admin.system_alerts.asignado_a'))
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Toggle::make('is_resolved')
                                ->label(__('admin.system_alerts.resuelta'))
                                ->default(false),
                        ]),
                        Textarea::make('message')
                            ->label(__('admin.system_alerts.mensaje'))
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.system_alerts.resolucion'))
                    ->schema([
                        Textarea::make('resolution_notes')
                            ->label(__('admin.system_alerts.notas_de_resolucion'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record?->is_resolved ?? false),
            ]);
    }
}
