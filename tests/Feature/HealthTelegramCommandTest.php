<?php

use App\Notifications\HealthCheckNotifiable;
use App\Notifications\HealthCheckTelegramNotification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;

test('health telegram test command sends notification', function () {
    Notification::fake();

    config()->set('services.telegram.chat_id', 'test-chat-id');
    config()->set('services.telegram.token', 'test-token');

    $exitCode = Artisan::call('health:telegram-test');

    expect($exitCode)->toBe(0);

    Notification::assertSentTo(
        new HealthCheckNotifiable,
        HealthCheckTelegramNotification::class
    );
});
