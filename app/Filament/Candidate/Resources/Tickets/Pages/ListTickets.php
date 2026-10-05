<?php

namespace App\Filament\Candidate\Resources\Tickets\Pages;

use App\Filament\Candidate\Resources\Tickets\TicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Abrir ticket'),
        ];
    }
}
