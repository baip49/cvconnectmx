<?php

namespace App\Services;

use App\Health\Checks\SystemCpuLoadCheck;
use App\Health\Checks\SystemMemoryUsageCheck;
use App\Models\SystemAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;
use Throwable;

class SystemMonitorService
{
    private const QUEUE_WARNING_THRESHOLD = 100;

    public function __construct(
        protected SystemMemoryUsageCheck $memory = new SystemMemoryUsageCheck,
        protected SystemCpuLoadCheck $cpu = new SystemCpuLoadCheck,
        protected UsedDiskSpaceCheck $disk = new UsedDiskSpaceCheck,
        protected TelegramNotifier $telegram = new TelegramNotifier,
    ) {
        $this->memory
            ->warnWhenUsedMemoryIsAbovePercentage((int) config('health.system_resources.memory.warning_percentage', 80))
            ->failWhenUsedMemoryIsAbovePercentage((int) config('health.system_resources.memory.critical_percentage', 90));

        $this->cpu
            ->warnWhenLoadIsAbovePercentage((int) config('health.system_resources.cpu.warning_percentage', 80))
            ->failWhenLoadIsAbovePercentage((int) config('health.system_resources.cpu.critical_percentage', 90));

        $this->disk
            ->warnWhenUsedSpaceIsAbovePercentage((int) config('health.system_resources.disk.warning_percentage', 80))
            ->failWhenUsedSpaceIsAbovePercentage((int) config('health.system_resources.disk.critical_percentage', 90));
    }

    /**
     * Run resource checks, record alerts and notify Telegram on breaches.
     *
     * @return array{alerts_created: int, resolved: int}
     */
    public function run(): array
    {
        $created = 0;
        $resolved = 0;

        $checks = [
            'system.memory' => ['label' => 'Memoria del servidor', 'check' => $this->memory],
            'system.cpu' => ['label' => 'CPU del servidor', 'check' => $this->cpu],
            'system.disk' => ['label' => 'Disco del servidor', 'check' => $this->disk],
        ];

        foreach ($checks as $type => $check) {
            try {
                $result = $check['check']->run();
            } catch (Throwable $e) {
                Log::warning("SystemMonitorService: check {$type} no disponible: ".$e->getMessage());

                continue;
            }

            if ($this->handleResult($type, $check['label'], $result)) {
                $created++;
            } else {
                $resolved += $this->resolveAlerts($type);
            }
        }

        $queuePending = DB::table('jobs')->count();

        if ($queuePending >= self::QUEUE_WARNING_THRESHOLD) {
            if ($this->raiseAlert('system.queue', 'warning', "Hay {$queuePending} trabajos en espera en la cola. Es posible que el worker esté detenido.")) {
                $created++;
            }
        } else {
            $resolved += $this->resolveAlerts('system.queue');
        }

        return ['alerts_created' => $created, 'resolved' => $resolved];
    }

    /**
     * Record an alert and notify Telegram unless one is already open.
     */
    protected function handleResult(string $type, string $label, Result $result): bool
    {
        if ($result->status === Status::ok()) {
            return false;
        }

        $level = $result->status === Status::failed() || $result->status === Status::crashed() ? 'critical' : 'warning';
        $percentage = $this->extractPercentage($result);
        $message = $percentage !== null
            ? "{$label} al {$percentage}% de uso."
            : "{$label}: {$result->getNotificationMessage()}";

        return $this->raiseAlert($type, $level, $message);
    }

    protected function raiseAlert(string $type, string $level, string $message): bool
    {
        $alreadyOpen = SystemAlert::query()
            ->where('type', $type)
            ->where('level', $level)
            ->where('is_resolved', false)
            ->where('created_at', '>=', now()->subHours(6))
            ->exists();

        if ($alreadyOpen) {
            return false;
        }

        SystemAlert::create([
            'type' => $type,
            'level' => $level,
            'message' => $message,
            'user_id' => null,
            'is_resolved' => false,
            'reviewed_by' => null,
        ]);

        $emoji = $level === 'critical' ? '🚨' : '⚠️';

        $this->telegram->send("{$emoji} <b>Alerta del sistema</b>\n".e($message));

        return true;
    }

    protected function resolveAlerts(string $type): int
    {
        return SystemAlert::query()
            ->where('type', $type)
            ->where('is_resolved', false)
            ->update(['is_resolved' => true, 'resolved_at' => now()]);
    }

    protected function extractPercentage(Result $result): ?int
    {
        if (preg_match('/(\d+)%/', (string) $result->getShortSummary(), $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
