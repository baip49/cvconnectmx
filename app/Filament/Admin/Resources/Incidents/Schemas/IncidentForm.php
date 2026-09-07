<?php

namespace App\Filament\Admin\Resources\Incidents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IncidentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Incidente')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('type')
                                ->label('Tipo de Incidente')
                                ->required()
                                ->maxLength(255),
                            Select::make('level')
                                ->label('Nivel de Severidad')
                                ->options([
                                    'low' => 'Bajo',
                                    'medium' => 'Medio',
                                    'high' => 'Alto',
                                ])
                                ->required(),
                            Select::make('status')
                                ->label('Estado')
                                ->options([
                                    'open' => 'Abierto',
                                    'in_progress' => 'En progreso',
                                    'resolved' => 'Resuelto',
                                    'closed' => 'Cerrado',
                                ])
                                ->required()
                                ->default('open'),
                            DatePicker::make('detected_at')
                                ->label('Fecha de Detección')
                                ->required()
                                ->default(now()),
                        ]),
                        Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Información Adicional')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('affected_user_id')
                                ->label('Usuario Afectado')
                                ->relationship('affectedUser', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),
                        Textarea::make('lessons_learned')
                            ->label('Lecciones Aprendidas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
