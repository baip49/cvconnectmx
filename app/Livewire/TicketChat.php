<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Support\TicketLabels;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TicketChat extends Component
{
    public int $ticketId;

    public string $message = '';

    public bool $editing = false;

    public string $editTitle = '';

    public string $editType = 'general';

    public string $editLevel = 'medium';

    public function mount(int $ticketId): void
    {
        $ticket = Ticket::query()->findOrFail($ticketId);

        abort_unless($ticket->canBeViewedBy(Auth::user()), 403);

        $this->ticketId = $ticket->id;
    }

    public function send(): void
    {
        $ticket = Ticket::query()->findOrFail($this->ticketId);

        $this->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $ticket->addReply(Auth::user(), trim($this->message));

        $this->reset('message');
    }

    public function startEdit(): void
    {
        $ticket = Ticket::query()->findOrFail($this->ticketId);

        abort_unless($ticket->canBeEditedBy(Auth::user()), 403);

        $this->editTitle = (string) $ticket->title;
        $this->editType = (string) $ticket->type;
        $this->editLevel = (string) $ticket->level;
        $this->editing = true;
    }

    public function saveEdit(): void
    {
        $ticket = Ticket::query()->findOrFail($this->ticketId);

        abort_unless($ticket->canBeEditedBy(Auth::user()), 403);

        $this->validate([
            'editTitle' => ['required', 'string', 'max:255'],
            'editType' => ['required', 'string', 'max:255'],
            'editLevel' => ['required', 'in:low,medium,high'],
        ]);

        $ticket->update([
            'title' => $this->editTitle,
            'type' => $this->editType,
            'level' => $this->editLevel,
        ]);

        $this->editing = false;
    }

    public function render(): View
    {
        $ticket = Ticket::query()->with(['creator', 'claimedBy', 'replies.performedBy'])->findOrFail($this->ticketId);
        $viewer = Auth::user();

        return view('livewire.ticket-chat', [
            'ticket' => $ticket,
            'replies' => $ticket->replies()->oldest()->get(),
            'canReply' => $ticket->canBeRepliedBy($viewer),
            'canEdit' => $ticket->canBeEditedBy($viewer),
            'isAttendant' => $ticket->isAttendant($viewer),
            'typeOptions' => TicketLabels::types(),
        ]);
    }
}
