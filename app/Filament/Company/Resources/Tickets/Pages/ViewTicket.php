<?php

namespace App\Filament\Company\Resources\Tickets\Pages;

use App\Filament\Company\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('close')
                ->label(__('company.tickets.cerrar_ticket'))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Ticket $record): bool => $record->canBeClosedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->close(Auth::user());

                    Notification::make()->title(__('company.tickets.ticket_cerrado'))->success()->send();
                }),

            Action::make('reopen')
                ->label(__('company.tickets.reabrir_ticket'))
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription(__('company.tickets.el_ticket_volvera_a_estado_abierto_o_en'))
                ->visible(fn (Ticket $record): bool => $record->canBeReopenedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->reopen(Auth::user());

                    Notification::make()->title(__('company.tickets.ticket_reabierto'))->success()->send();
                }),
        ];
    }
}
