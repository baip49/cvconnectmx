<?php

use App\Health\Checks\SystemCpuLoadCheck;
use App\Health\Checks\SystemMemoryUsageCheck;
use App\Models\SystemAlert;
use App\Services\SystemMonitorService;
use App\Services\TelegramNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

uses(RefreshDatabase::class);

function fakeTelegramApi(): void
{
    config()->set('services.telegram.token', 'test-token');
    config()->set('services.telegram.chat_id', '12345');

    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);
}

function monitorService(int $memoryPercentage = 10, int $cpuPercentage = 10): SystemMonitorService
{
    $memory = (new SystemMemoryUsageCheck)->resolveMemoryUsageUsing(fn () => [
        'used' => $memoryPercentage,
        'total' => 100,
        'source' => 'test',
    ]);

    $cpu = (new SystemCpuLoadCheck)->resolveCpuUsageUsing(fn () => [
        'percentage' => $cpuPercentage,
        'source' => 'test',
    ]);

    return new SystemMonitorService($memory, $cpu, new UsedDiskSpaceCheck, new TelegramNotifier);
}

test('resource breach creates a system alert and notifies telegram', function () {
    fakeTelegramApi();

    $result = monitorService(memoryPercentage: 95)->run();

    expect($result['alerts_created'])->toBeGreaterThanOrEqual(1);

    $alert = SystemAlert::query()->where('type', 'system.memory')->first();

    expect($alert)->not->toBeNull()
        ->and($alert->level)->toBe('critical')
        ->and($alert->is_resolved)->toBeFalse();

    Http::assertSent(fn ($request) => str_contains((string) $request['text'], 'Memoria del servidor'));
});

test('healthy resources resolve open alerts without notifying', function () {
    fakeTelegramApi();

    config()->set('health.system_resources.disk.warning_percentage', 100);
    config()->set('health.system_resources.disk.critical_percentage', 100);
    config()->set('health.system_resources.cpu.warning_percentage', 100);
    config()->set('health.system_resources.cpu.critical_percentage', 100);

    SystemAlert::create([
        'type' => 'system.memory',
        'level' => 'critical',
        'message' => 'Alerta vieja.',
        'user_id' => null,
        'is_resolved' => false,
        'reviewed_by' => null,
    ]);

    $result = monitorService(memoryPercentage: 10, cpuPercentage: 10)->run();

    expect($result['resolved'])->toBeGreaterThanOrEqual(1)
        ->and(SystemAlert::query()->where('type', 'system.memory')->where('is_resolved', false)->exists())->toBeFalse();

    Http::assertNotSent(fn ($request) => str_contains((string) $request->url(), 'api.telegram.org'));
});

test('system monitor command runs successfully', function () {
    $this->artisan('system:monitor')->assertSuccessful();
});
