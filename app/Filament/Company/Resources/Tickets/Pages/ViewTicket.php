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
                ->label('Cerrar ticket')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Ticket $record): bool => $record->canBeClosedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->close(Auth::user());

                    Notification::make()->title('Ticket cerrado')->success()->send();
                }),

            Action::make('reopen')
                ->label('Reabrir ticket')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('El ticket volverá a estado abierto o en progreso. Solo se puede reabrir dentro de los 30 días posteriores al cierre.')
                ->visible(fn (Ticket $record): bool => $record->canBeReopenedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->reopen(Auth::user());

                    Notification::make()->title('Ticket reabierto')->success()->send();
                }),
        ];
    }
}
