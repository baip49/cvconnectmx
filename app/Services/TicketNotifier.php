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
        $this->sendToMany($this->interestedParties($ticket, $author), 'reply_received', $author, 'heroicon-o-chat-bubble-left-right', $ticket);
    }

    public function claimed(Ticket $ticket, User $claimer): void
    {
        $this->sendToMany($this->interestedParties($ticket, $claimer), 'claimed', $claimer, 'heroicon-o-user-plus', $ticket);
    }

    public function joined(Ticket $ticket, User $joiner): void
    {
        $this->sendToMany($this->interestedParties($ticket, $joiner), 'joined', $joiner, 'heroicon-o-users', $ticket);
    }

    public function released(Ticket $ticket, User $leaver): void
    {
        $this->sendToMany($this->interestedParties($ticket, $leaver), 'released', $leaver, 'heroicon-o-arrow-right-start-on-rectangle', $ticket);
    }

    public function closed(Ticket $ticket, User $closer): void
    {
        $this->sendToMany($this->interestedParties($ticket, $closer), 'closed', $closer, 'heroicon-o-check-circle', $ticket);
    }

    public function reopened(Ticket $ticket, User $reopener): void
    {
        $this->sendToMany($this->interestedParties($ticket, $reopener), 'reopened', $reopener, 'heroicon-o-arrow-path', $ticket);
    }

    public function assigned(Ticket $ticket, User $assignee): void
    {
        $this->sendToMany($this->interestedParties($ticket, $assignee), 'assigned', $assignee, 'heroicon-o-inbox-arrow-down', $ticket);
    }

    /**
     * Notifications are stored already written, so each one uses its recipient's language.
     */
    protected function sendToMany(iterable $users, string $event, User $actor, string $icon, Ticket $ticket): void
    {
        foreach ($users as $user) {
            $locale = $user->locale ?? config('app.locale');

            Notification::make()
                ->title(__("notifications.tickets.{$event}.title", [], $locale))
                ->body(__("notifications.tickets.{$event}.body", ['name' => $actor->name, 'title' => $ticket->title], $locale))
                ->icon($icon)
                ->actions([
                    Action::make('view')
                        ->label(__('notifications.view', [], $locale))
                        ->url($this->ticketUrl($user, $ticket)),
                ])
                ->sendToDatabase($user);
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
