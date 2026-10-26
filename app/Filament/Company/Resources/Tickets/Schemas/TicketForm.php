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
                Section::make(__('company.tickets.abrir_ticket_de_ayuda'))
                    ->description(__('company.tickets.describe_tu_problema_y_el_equipo_de_asis'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('company.tickets.asunto'))
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label(__('company.tickets.tipo_de_ayuda'))
                            ->options(TicketLabels::types())
                            ->default('general')
                            ->required(),
                        Select::make('level')
                            ->label(__('company.tickets.urgencia'))
                            ->options([
                                'low' => __('company.tickets.baja'),
                                'medium' => __('company.tickets.media'),
                                'high' => __('company.tickets.alta'),
                            ])
                            ->default('medium')
                            ->required(),
                        Textarea::make('description')
                            ->label(__('company.tickets.descripcion_del_problema'))
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
