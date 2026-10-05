<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BackupSchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function backupLogs(): HasMany
    {
        return $this->hasMany(BackupLog::class, 'schedule_id');
    }

    /**
     * Determine whether the schedule is due for a new run.
     */
    public function isDue(?\DateTimeInterface $now = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = $now ? CarbonImmutable::parse($now) : now();

        if ($now->format('H:i') < substr((string) $this->time, 0, 5)) {
            return false;
        }

        if ($this->last_run_at === null) {
            return true;
        }

        return match ($this->frequency) {
            'weekly' => $this->last_run_at->lt($now->subWeek()),
            'monthly' => $this->last_run_at->lt($now->subMonth()),
            default => $this->last_run_at->lt($now->subDay()),
        };
    }
}
