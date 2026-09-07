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
                Section::make('Información de la Alerta')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('type')
                                ->label('Tipo de Alerta')
                                ->required()
                                ->maxLength(255),
                            Select::make('level')
                                ->label('Nivel')
                                ->options([
                                    'info' => 'Informativo',
                                    'warning' => 'Advertencia',
                                    'error' => 'Error',
                                    'critical' => 'Crítico',
                                ])
                                ->required(),
                            Select::make('user_id')
                                ->label('Asignado a')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Toggle::make('is_resolved')
                                ->label('Resuelta')
                                ->default(false),
                        ]),
                        Textarea::make('message')
                            ->label('Mensaje')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Resolución')
                    ->schema([
                        Textarea::make('resolution_notes')
                            ->label('Notas de Resolución')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record?->is_resolved ?? false),
            ]);
    }
}
