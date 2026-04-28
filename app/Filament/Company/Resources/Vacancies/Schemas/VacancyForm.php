<?php

namespace App\Filament\Company\Resources\Vacancies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VacancyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Vacante')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título de la Vacante')
                            ->required()
                            ->maxLength(255),
                        Select::make('work_model')
                            ->label('Modalidad')
                            ->options([
                                'remote' => 'Remoto',
                                'hybrid' => 'Híbrido',
                                'on_site' => 'Presencial',
                            ])
                            ->required(),
                        TextInput::make('max_salary')
                            ->label('Sueldo Máximo (Mensual)')
                            ->numeric()
                            ->prefix('$'),
                        DatePicker::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->required(),
                        Textarea::make('description')
                            ->label('Descripción del Puesto')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),
                        Textarea::make('requirements')
                            ->label('Requisitos del Puesto')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
