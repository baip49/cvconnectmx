<?php

namespace App\Filament\Company\Resources\Tickets\Schemas;

use App\Models\Ticket;
use Filament\Schemas\Components\Livewire as LivewireEntry;
use Filament\Schemas\Schema;

class TicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                LivewireEntry::make('ticket-chat', fn (Ticket $record): array => ['ticketId' => $record->id])
                    ->columnSpanFull(),
            ]);
    }
}
