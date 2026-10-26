<?php

namespace App\Filament\Support\Resources\Tickets;

use App\Filament\Support\Resources\Tickets\Pages\ListTickets;
use App\Filament\Support\Resources\Tickets\Pages\ViewTicket;
use App\Filament\Support\Resources\Tickets\Schemas\TicketInfolist;
use App\Filament\Support\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    public static function getModelLabel(): string
    {
        return __('support.tickets.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('support.tickets.plural_model_label');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    public static function getNavigationLabel(): string
    {
        return __('support.tickets.tickets');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('support.navigation_groups.tickets');
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'view' => ViewTicket::route('/{record}'),
        ];
    }
}
