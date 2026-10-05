<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class TicketNotifier
{
    protected function ticketUrl(User $user, Ticket $ticket): string
    {
        return url($user->panelPath()."/tickets/{$ticket->id}");
    }

    public function replyReceived(Ticket $ticket, User $author): void
    {
        $this->sendToMany(
            $this->interestedParties($ticket, $author),
            'Nueva respuesta en tu ticket',
            "{$author->name} respondió en \"{$ticket->title}\".",
            'heroicon-o-chat-bubble-left-right',
            $ticket
        );
    }

    public function claimed(Ticket $ticket, User $claimer): void
    {
        $this->sendToMany(
            $this->interestedParties($ticket, $claimer),
            'Tu ticket está siendo atendido',
            "{$claimer->name} reclamó tu ticket \"{$ticket->title}\" y te atenderá ahora.",
            'heroicon-o-user-plus',
            $ticket
        );
    }

    public function joined(Ticket $ticket, User $joiner): void
    {
        $this->sendToMany(
            $this->interestedParties($ticket, $joiner),
            'Un segundo asistente se unió',
            "{$joiner->name} se unió como segundo asistente al ticket \"{$ticket->title}\".",
            'heroicon-o-users',
            $ticket
        );
    }

    public function released(Ticket $ticket, User $leaver): void
    {
        $this->sendToMany(
            $this->interestedParties($ticket, $leaver),
            'Un asistente abandonó el ticket',
            "{$leaver->name} dejó de atender el ticket \"{$ticket->title}\".",
            'heroicon-o-arrow-right-start-on-rectangle',
            $ticket
        );
    }

    public function closed(Ticket $ticket, User $closer): void
    {
        $this->sendToMany(
            $this->interestedParties($ticket, $closer),
            'Ticket cerrado',
            "{$closer->name} cerró el ticket \"{$ticket->title}\".",
            'heroicon-o-check-circle',
            $ticket
        );
    }

    public function reopened(Ticket $ticket, User $reopener): void
    {
        $this->sendToMany(
            $this->interestedParties($ticket, $reopener),
            'Ticket reabierto',
            "{$reopener->name} reabrió el ticket \"{$ticket->title}\".",
            'heroicon-o-arrow-path',
            $ticket
        );
    }

    public function assigned(Ticket $ticket, User $assignee): void
    {
        $this->sendToMany(
            $this->interestedParties($ticket, $assignee),
            'Tienes un ticket asignado',
            "Se te asignó el ticket \"{$ticket->title}\".",
            'heroicon-o-inbox-arrow-down',
            $ticket
        );
    }

    protected function sendToMany(iterable $users, string $title, string $body, string $icon, ?Ticket $linkTicket = null): void
    {
        foreach ($users as $user) {
            $notification = Notification::make()
                ->title($title)
                ->body($body)
                ->icon($icon);

            if ($linkTicket) {
                $notification->actions([
                    Action::make('view')
                        ->label('Ver')
                        ->url($this->ticketUrl($user, $linkTicket)),
                ]);
            }

            $notification->sendToDatabase($user);
        }
    }

    /**
     * @return array<int, User>
     */
    protected function interestedParties(Ticket $ticket, User $except): array
    {
        $ids = array_filter([
            $ticket->created_by,
            $ticket->claimed_by,
            $ticket->secondary_assistant_id,
        ]);

        return User::query()
            ->whereIn('id', $ids)
            ->where('id', '!=', $except->id)
            ->get()
            ->all();
    }
}
