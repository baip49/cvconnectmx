<?php

namespace App\Filament\Support\Resources\Tickets\Pages;

use App\Filament\Support\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
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
            Action::make('claim')
                ->label('Reclamar')
                ->icon(Heroicon::OutlinedUserPlus)
                ->visible(fn (Ticket $record): bool => $record->canBeClaimedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->claim(Auth::user());

                    Notification::make()->title('Ticket reclamado')->success()->send();
                }),

            Action::make('join')
                ->label('Unirse como segundo asistente')
                ->icon(Heroicon::OutlinedUsers)
                ->visible(fn (Ticket $record): bool => $record->canBeJoinedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->joinAsSecond(Auth::user());

                    Notification::make()->title('Te uniste al ticket')->success()->send();
                }),

            Action::make('assign')
                ->label('Asignar')
                ->icon(Heroicon::OutlinedUserPlus)
                ->visible(fn (): bool => (bool) Auth::user()?->hasPermission('tickets.assign'))
                ->form([
                    Select::make('claimed_by')
                        ->label('Asistente')
                        ->options(fn (): array => User::query()
                            ->whereHas('role', fn ($query) => $query->whereIn('name', ['support', 'admin']))
                            ->pluck('name', 'id')
                            ->all())
                        ->required(),
                ])
                ->action(function (Ticket $record, array $data): void {
                    $record->update(['claimed_by' => $data['claimed_by'], 'status' => 'in_progress']);

                    Notification::make()->title('Ticket asignado')->success()->send();
                }),

            Action::make('release')
                ->label('Abandonar ticket')
                ->icon(Heroicon::OutlinedArrowRightStartOnRectangle)
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('Dejarás de atender este ticket. Tu salida quedará registrada en la auditoría.')
                ->visible(fn (Ticket $record): bool => $record->canBeReleasedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->release(Auth::user());

                    Notification::make()->title('Abandonaste el ticket')->success()->send();
                }),

            Action::make('transition')
                ->label('Cambiar estado')
                ->icon(Heroicon::OutlinedArrowsRightLeft)
                ->form([
                    Select::make('status')
                        ->label('Estado')
                        ->options([
                            'open' => 'Abierto',
                            'in_progress' => 'En progreso',
                        ])
                        ->required(),
                ])
                ->visible(fn (Ticket $record): bool => $record->canBeTransitionedBy(Auth::user()))
                ->action(function (Ticket $record, array $data): void {
                    $record->transitionTo(Auth::user(), $data['status']);

                    Notification::make()->title('Estado actualizado')->success()->send();
                }),

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
