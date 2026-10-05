<?php

use App\Health\Checks\SystemCpuLoadCheck;
use App\Health\Checks\SystemMemoryUsageCheck;
use App\Notifications\HealthCheckNotifiable;
use App\Notifications\HealthCheckTelegramNotification;
use App\Services\SystemMonitorService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Checks\Result;
use Spatie\Health\Commands\RunHealthChecksCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('system:monitor', function () {
    $result = app(SystemMonitorService::class)->run();

    $this->info("Monitoreo completado: {$result['alerts_created']} alertas creadas, {$result['resolved']} resueltas.");

    return 0;
})->purpose('Check server resources every 15 minutes, record alerts and notify Telegram.');

Schedule::command(RunHealthChecksCommand::class)->everyMinute();
Schedule::command('backup:run-scheduled')->everyMinute();
Schedule::command('system:monitor')->everyFifteenMinutes();

Artisan::command('health:telegram-test', function () {
    $chatId = config('services.telegram.chat_id');
    $token = config('services.telegram.token');

    if (! $chatId || ! $token) {
        $this->error('Missing TELEGRAM_CHAT_ID or TELEGRAM_BOT_TOKEN in the environment.');

        return 1;
    }

    $memoryCheck = new SystemMemoryUsageCheck;
    $cpuCheck = new SystemCpuLoadCheck;

    $memoryResult = Result::make()
        ->failed('Memory forced fail (>=90%).')
        ->check($memoryCheck);

    $cpuResult = Result::make()
        ->warning('CPU forced warning (>=80%).')
        ->check($cpuCheck);

    cache()->forget(config('health.notifications.throttle_notifications_key', 'health:latestNotificationSentAt:').'telegram');

    (new HealthCheckNotifiable)
        ->notify(new HealthCheckTelegramNotification([$memoryResult, $cpuResult]));

    $this->info('Telegram health notification sent.');

    return 0;
})->purpose('Send a test Telegram health notification.');
