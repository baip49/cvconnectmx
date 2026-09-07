<?php

namespace App\Filament\Admin\Resources\UserTrainings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserTrainingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Asignación de Capacitación')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->label('Usuario')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('training_id')
                                ->label('Capacitación')
                                ->relationship('training', 'title')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('status')
                                ->label('Estado')
                                ->options([
                                    'pending' => 'Pendiente',
                                    'in_progress' => 'En progreso',
                                    'completed' => 'Completada',
                                    'expired' => 'Expirada',
                                ])
                                ->required()
                                ->default('pending'),
                            TextInput::make('score')
                                ->label('Calificación')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->nullable(),
                            DatePicker::make('completed_at')
                                ->label('Fecha de Finalización')
                                ->nullable(),
                            DatePicker::make('expires_at')
                                ->label('Fecha de Expiración')
                                ->nullable(),
                        ]),
                    ]),
            ]);
    }
}
