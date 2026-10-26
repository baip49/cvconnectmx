<?php

namespace App\Filament\Candidate\Resources\Tickets\Schemas;

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
                Section::make(__('candidate.tickets.abrir_ticket_de_ayuda'))
                    ->description(__('candidate.tickets.describe_tu_problema_y_el_equipo_de_asis'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('candidate.tickets.asunto'))
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label(__('candidate.tickets.tipo_de_ayuda'))
                            ->options(TicketLabels::types())
                            ->default('general')
                            ->required(),
                        Select::make('level')
                            ->label(__('candidate.tickets.urgencia'))
                            ->options([
                                'low' => __('candidate.tickets.baja'),
                                'medium' => __('candidate.tickets.media'),
                                'high' => __('candidate.tickets.alta'),
                            ])
                            ->default('medium')
                            ->required(),
                        Textarea::make('description')
                            ->label(__('candidate.tickets.descripcion_del_problema'))
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
