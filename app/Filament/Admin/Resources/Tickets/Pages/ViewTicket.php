<?php

namespace App\Filament\Admin\Resources\Tickets\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
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
                ->label(__('admin.tickets.reclamar'))
                ->icon(Heroicon::OutlinedUserPlus)
                ->visible(fn (Ticket $record): bool => $record->canBeClaimedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->claim(Auth::user());

                    Notification::make()->title(__('admin.tickets.ticket_reclamado'))->success()->send();
                }),

            Action::make('join')
                ->label(__('admin.tickets.unirse_como_segundo_asistente'))
                ->icon(Heroicon::OutlinedUsers)
                ->visible(fn (Ticket $record): bool => $record->canBeJoinedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->joinAsSecond(Auth::user());

                    Notification::make()->title(__('admin.tickets.te_uniste_al_ticket'))->success()->send();
                }),

            Action::make('assign')
                ->label(__('admin.tickets.asignar'))
                ->icon(Heroicon::OutlinedUserPlus)
                ->visible(fn (): bool => (bool) Auth::user()?->hasPermission('tickets.assign'))
                ->form([
                    Select::make('claimed_by')
                        ->label(__('admin.tickets.asistente'))
                        ->options(fn (): array => User::query()
                            ->whereHas('role', fn ($query) => $query->whereIn('name', ['support', 'admin']))
                            ->pluck('name', 'id')
                            ->all())
                        ->required(),
                ])
                ->action(function (Ticket $record, array $data): void {
                    $record->assignTo(
                        User::query()->findOrFail($data['claimed_by']),
                        Auth::user()
                    );

                    Notification::make()->title(__('admin.tickets.ticket_asignado'))->success()->send();
                }),

            Action::make('release')
                ->label(__('admin.tickets.abandonar_ticket'))
                ->icon(Heroicon::OutlinedArrowRightStartOnRectangle)
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription(__('admin.tickets.dejaras_de_atender_este_ticket_tu_salida'))
                ->visible(fn (Ticket $record): bool => $record->canBeReleasedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->release(Auth::user());

                    Notification::make()->title(__('admin.tickets.abandonaste_el_ticket'))->success()->send();
                }),

            Action::make('transition')
                ->label(__('admin.tickets.cambiar_estado'))
                ->icon(Heroicon::OutlinedArrowsRightLeft)
                ->form([
                    Select::make('status')
                        ->label(__('admin.tickets.estado'))
                        ->options([
                            'open' => __('admin.tickets.abierto'),
                            'in_progress' => __('admin.tickets.en_progreso'),
                        ])
                        ->required(),
                ])
                ->visible(fn (Ticket $record): bool => $record->canBeTransitionedBy(Auth::user()))
                ->action(function (Ticket $record, array $data): void {
                    $record->transitionTo(Auth::user(), $data['status']);

                    Notification::make()->title(__('admin.tickets.estado_actualizado'))->success()->send();
                }),

            Action::make('close')
                ->label(__('admin.tickets.cerrar_ticket'))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Ticket $record): bool => $record->canBeClosedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->close(Auth::user());

                    Notification::make()->title(__('admin.tickets.ticket_cerrado'))->success()->send();
                }),

            Action::make('reopen')
                ->label(__('admin.tickets.reabrir_ticket'))
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription(__('admin.tickets.el_ticket_volvera_a_estado_abierto_o_en'))
                ->visible(fn (Ticket $record): bool => $record->canBeReopenedBy(Auth::user()))
                ->action(function (Ticket $record): void {
                    $record->reopen(Auth::user());

                    Notification::make()->title(__('admin.tickets.ticket_reabierto'))->success()->send();
                }),
        ];
    }
}
