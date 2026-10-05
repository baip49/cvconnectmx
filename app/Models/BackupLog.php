<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BackupLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'restoration_tested' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleting(function (BackupLog $backupLog): void {
            $path = (string) $backupLog->destination_path;

            if ($path === '' || $path === 'pending') {
                return;
            }

            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        });
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(BackupSchedule::class, 'schedule_id');
    }
}
