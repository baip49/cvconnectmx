<?php

namespace App\Filament\Company\Resources\Tickets;

use App\Filament\Company\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Company\Resources\Tickets\Pages\ListTickets;
use App\Filament\Company\Resources\Tickets\Pages\ViewTicket;
use App\Filament\Company\Resources\Tickets\Schemas\TicketForm;
use App\Filament\Company\Resources\Tickets\Schemas\TicketInfolist;
use App\Filament\Company\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static ?string $navigationLabel = 'Mis tickets';

    protected static string|UnitEnum|null $navigationGroup = 'Soporte';

    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('created_by', auth()->id());
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
            'create' => CreateTicket::route('/create'),
            'view' => ViewTicket::route('/{record}'),
        ];
    }
}
