<?php

namespace App\Filament\Company\Resources\Tickets\Schemas;

use App\Support\TicketLabels;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Abrir ticket de ayuda')
                    ->description('Describe tu problema y el equipo de asistencia te atenderá.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Asunto')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Tipo de ayuda')
                            ->options(TicketLabels::types())
                            ->default('general')
                            ->required(),
                        Select::make('level')
                            ->label('Urgencia')
                            ->options([
                                'low' => 'Baja',
                                'medium' => 'Media',
                                'high' => 'Alta',
                            ])
                            ->default('medium')
                            ->required(),
                        Textarea::make('description')
                            ->label('Descripción del problema')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
