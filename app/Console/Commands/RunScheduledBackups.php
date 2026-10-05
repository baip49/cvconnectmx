<?php

namespace App\Console\Commands;

use App\Jobs\RunDatabaseBackup;
use App\Models\BackupLog;
use App\Models\BackupSchedule;
use Illuminate\Console\Command;

class RunScheduledBackups extends Command
{
    protected $signature = 'backup:run-scheduled';

    protected $description = 'Encola los respaldos programados vencidos (diario, semanal, mensual).';

    public function handle(): int
    {
        $dispatched = 0;

        foreach (BackupSchedule::query()->where('is_active', true)->get() as $schedule) {
            if (! $schedule->isDue()) {
                continue;
            }

            $scopes = $schedule->scope === 'both' ? ['database', 'files'] : [$schedule->scope];

            foreach ($scopes as $scope) {
                $backupLog = BackupLog::create([
                    'type' => 'full',
                    'frequency' => $schedule->frequency,
                    'scope' => $scope,
                    'destination_path' => 'pending',
                    'size_bytes' => 0,
                    'checksum_sha256' => '',
                    'is_encrypted' => false,
                    'status' => 'in_progress',
                    'retention_days' => $schedule->retention_days,
                    'executed_by' => null,
                    'schedule_id' => $schedule->id,
                ]);

                RunDatabaseBackup::dispatch($backupLog->id, $scope);
                $dispatched++;
            }

            $schedule->update(['last_run_at' => now()]);
            $this->info("Programación '{$schedule->name}' encolada.");
        }

        if ($dispatched === 0) {
            $this->info('No hay respaldos programados vencidos.');
        }

        return self::SUCCESS;
    }
}
