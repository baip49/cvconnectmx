<?php

namespace App\Models;

use App\Services\TicketNotifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'evidence' => 'array',
        'detected_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function affectedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affected_user_id');
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function secondaryAssistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secondary_assistant_id');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress'], true);
    }

    public function isAttendant(?User $user): bool
    {
        return $user !== null && ($user->isSupport() || $user->isAdmin());
    }

    public function isAssignedTo(?User $user): bool
    {
        return $user !== null
            && ($this->claimed_by === $user->id || $this->secondary_assistant_id === $user->id);
    }

    public function canBeViewedBy(?User $user): bool
    {
        return $user !== null
            && $user->hasPermission('tickets.view')
            && ($this->created_by === $user->id || $this->isAttendant($user) || $this->isAssignedTo($user));
    }

    public function canBeClaimedBy(?User $user): bool
    {
        return $user !== null
            && $this->isOpen()
            && $this->claimed_by === null
            && $this->isAttendant($user)
            && $user->hasPermission('tickets.claim');
    }

    public function canBeJoinedBy(?User $user): bool
    {
        return $user !== null
            && $this->isOpen()
            && $this->claimed_by !== null
            && $this->secondary_assistant_id === null
            && $this->isAttendant($user)
            && $user->hasPermission('tickets.join')
            && $this->claimed_by !== $user->id;
    }

    public function canBeReleasedBy(?User $user): bool
    {
        return $user !== null
            && $this->isOpen()
            && ($this->claimed_by === $user->id || $this->secondary_assistant_id === $user->id);
    }

    public function canBeRepliedBy(?User $user): bool
    {
        return $user !== null
            && $this->status !== 'closed'
            && $user->hasPermission('tickets.reply')
            && ($this->created_by === $user->id || $this->isAttendant($user) || $this->isAssignedTo($user));
    }

    public function canBeClosedBy(?User $user): bool
    {
        return $user !== null
            && $this->status !== 'closed'
            && $user->hasPermission('tickets.close')
            && ($this->created_by === $user->id || $this->isAttendant($user) || $this->isAssignedTo($user));
    }

    public function canBeEditedBy(?User $user): bool
    {
        return $user !== null
            && $this->status !== 'closed'
            && $user->hasPermission('tickets.edit')
            && $this->isAttendant($user);
    }

    public function canBeReopenedBy(?User $user): bool
    {
        return $user !== null
            && $this->status === 'closed'
            && $this->closed_at !== null
            && $this->closed_at->gte(now()->subDays(30))
            && $user->hasPermission('tickets.reopen')
            && ($this->created_by === $user->id || $this->isAttendant($user) || $this->isAssignedTo($user));
    }

    public function claim(User $user): void
    {
        abort_unless($this->canBeClaimedBy($user), 403);

        $this->update(['claimed_by' => $user->id, 'status' => 'in_progress']);

        $this->recordSystemEvent("{$user->name} tomó el ticket y te atenderá ahora.");

        app(TicketNotifier::class)->claimed($this->refresh(), $user);
    }

    public function joinAsSecond(User $user): void
    {
        abort_unless($this->canBeJoinedBy($user), 403);

        $this->update(['secondary_assistant_id' => $user->id]);

        $this->recordSystemEvent("{$user->name} se unió como segundo asistente.");

        app(TicketNotifier::class)->joined($this->refresh(), $user);
    }

    /**
     * Release the ticket: the claimer steps out (the second assistant
     * is promoted) or the second assistant steps out.
     */
    public function release(User $user): void
    {
        abort_unless($this->canBeReleasedBy($user), 403);

        if ($this->secondary_assistant_id === $user->id) {
            $this->update(['secondary_assistant_id' => null]);
            $this->recordSystemEvent("{$user->name} abandonó el ticket.", true);
            app(TicketNotifier::class)->released($this->refresh(), $user);

            return;
        }

        $this->update([
            'claimed_by' => $this->secondary_assistant_id,
            'secondary_assistant_id' => null,
            'status' => $this->secondary_assistant_id === null ? 'open' : $this->status,
        ]);

        $this->recordSystemEvent("{$user->name} abandonó el ticket.", true);
        app(TicketNotifier::class)->released($this->refresh(), $user);
    }

    public function addReply(User $user, string $message): TicketReply
    {
        abort_unless($this->canBeRepliedBy($user), 403);

        $reply = $this->replies()->create([
            'message' => $message,
            'performed_by' => $user->id,
        ]);

        if ($this->status === 'open' && $this->isAttendant($user)) {
            $this->update(['status' => 'in_progress']);
        }

        app(TicketNotifier::class)->replyReceived($this->refresh(), $user);

        return $reply;
    }

    public function close(User $user): void
    {
        abort_unless($this->canBeClosedBy($user), 403);

        $this->update([
            'status' => 'closed',
            'closed_by' => $user->id,
            'closed_at' => now(),
        ]);

        $this->recordSystemEvent("Ticket cerrado por {$user->name}.");

        app(TicketNotifier::class)->closed($this->refresh(), $user);
    }

    public function reopen(User $user): void
    {
        abort_unless($this->canBeReopenedBy($user), 403);

        $this->update([
            'status' => $this->claimed_by === null ? 'open' : 'in_progress',
            'closed_by' => null,
            'closed_at' => null,
        ]);

        $this->recordSystemEvent("Ticket reabierto por {$user->name}.");

        app(TicketNotifier::class)->reopened($this->refresh(), $user);
    }

    public function assignTo(User $assignee, User $assigner): void
    {
        abort_unless($assigner->hasPermission('tickets.assign'), 403);

        $this->update(['claimed_by' => $assignee->id, 'status' => 'in_progress']);

        $this->recordSystemEvent("{$assignee->name} fue asignado al ticket.");

        app(TicketNotifier::class)->assigned($this->refresh(), $assignee);
    }

    protected function recordSystemEvent(string $message, bool $internal = false): TicketReply
    {
        return $this->replies()->create([
            'message' => $message,
            'performed_by' => null,
            'is_internal' => $internal,
        ]);
    }

    public function canBeTransitionedBy(?User $user): bool
    {
        return $user !== null
            && $this->status !== 'closed'
            && $this->isAttendant($user);
    }

    /**
     * Manual status change between open and in_progress for support staff.
     * Closing and reopening keep their own rules and actions.
     */
    public function transitionTo(User $user, string $status): void
    {
        abort_unless($this->canBeTransitionedBy($user), 403);
        abort_unless(in_array($status, ['open', 'in_progress'], true), 422);

        $this->update(['status' => $status]);
    }
}
