<?php

namespace App\Jobs;

use App\Models\BackupLog;
use App\Services\DatabaseBackupService;
use App\Services\TelegramNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunDatabaseBackup implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 1800;

    public function __construct(public int $backupLogId, public string $scope = 'database') {}

    public function handle(DatabaseBackupService $backups, TelegramNotifier $telegram): void
    {
        $backupLog = BackupLog::findOrFail($this->backupLogId);

        $telegram->send(
            '🔄 <b>Respaldo iniciado</b>'."\n"
            .'Tipo: '.e($backupLog->type)."\n"
            .'Alcance: '.$this->scopeLabel()."\n"
            .'Solicitado por: '.e($backupLog->executedBy?->name ?? 'sistema')
        );

        try {
            $result = $backups->run($this->scope);

            $backupLog->update([
                'destination_path' => $result['relative_path'],
                'size_bytes' => $result['size_bytes'],
                'checksum_sha256' => $result['checksum_sha256'],
                'is_encrypted' => true,
                'scope' => $this->scope,
                'status' => 'success',
            ]);

            $sizeMb = round($result['size_bytes'] / 1024 / 1024, 2);

            $telegram->send(
                '✅ <b>Respaldo completado</b>'."\n"
                .'Tipo: '.e($backupLog->type)."\n"
                .'Alcance: '.$this->scopeLabel()."\n"
                ."Tamaño: {$sizeMb} MB\n"
                .'Destino: '.e($result['relative_path'])
            );
        } catch (Throwable $e) {
            Log::error('RunDatabaseBackup falló: '.$e->getMessage());

            $backupLog->update(['status' => 'failed']);

            $telegram->send(
                '🚨 <b>Respaldo fallido</b>'."\n"
                .'Tipo: '.e($backupLog->type)."\n"
                .'Alcance: '.$this->scopeLabel()."\n"
                .'Error: '.e(mb_substr($e->getMessage(), 0, 300))
            );
        }
    }

    protected function scopeLabel(): string
    {
        return match ($this->scope) {
            'files' => 'Archivos',
            'both' => 'Base de datos y archivos',
            default => 'Base de datos',
        };
    }
}
